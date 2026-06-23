<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\DeliveryWithdrawalStatus;
use App\Models\Order;
use App\Models\DeliveryUser;
use App\Models\DeliveryWithdrawalRequest;
use App\Models\DeliveryWithdrawalRequestOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DeliveryWalletService
{
    public function getOrdersWithWithdrawStatus(DeliveryUser $delivery, int $perPage = 15, ?string $withdrawStatus = null): LengthAwarePaginator
    {
        $orders = Order::query()
            ->where('delivery_id', $delivery->id)
            ->where('payment_status', PaymentStatus::Paid->value)
            ->where('payment_method', PaymentMethod::Paymob->value)
            ->withSum(['deliveryWithdrawalAllocations as allocated_withdraw_amount' => function ($query) {
                $query->whereHas('withdrawalRequest', function ($subQuery) {
                    $subQuery->whereIn('status', [
                        DeliveryWithdrawalStatus::Pending->value,
                        DeliveryWithdrawalStatus::Approved->value,
                    ]);
                });
            }], 'amount')
            ->with([
                'deliveryWithdrawalAllocations' => function ($query) {
                    $query->with('withdrawalRequest')->latest();
                },
            ])
            ->orderByDesc('id')
            ->get();

        $filteredOrders = $orders->filter(function (Order $order) use ($withdrawStatus) {
            $latestAllocation = $order->deliveryWithdrawalAllocations->sortByDesc('id')->first();
            $status = $latestAllocation?->withdrawalRequest?->status?->value ?? 'available';

            if ($withdrawStatus === null || $withdrawStatus === '') {
                return true;
            }

            return $status === $withdrawStatus;
        })->values();

        $currentPage = request()->integer('page', 1);
        $items = $filteredOrders->forPage($currentPage, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $filteredOrders->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function getAvailableWithdrawableAmount(DeliveryUser $delivery): float
    {
        return (float) Order::query()
            ->where('delivery_id', $delivery->id)
            ->where('payment_method', PaymentMethod::Paymob->value)
            ->where('payment_status', PaymentStatus::Paid->value)
            ->whereNotNull('paymob_transaction_id')
            ->whereDoesntHave('deliveryWithdrawalAllocations', function ($query) {
                $query->whereHas('withdrawalRequest', function ($subQuery) {
                    $subQuery->whereIn('status', [
                        DeliveryWithdrawalStatus::Pending->value,
                        DeliveryWithdrawalStatus::Approved->value,
                    ]);
                });
            })
            ->sum('delivery_fee');
    }

    public function getWithdrawableOrders(DeliveryUser $delivery, int $perPage = 15): LengthAwarePaginator
    {
        return Order::query()
            ->where('delivery_id', $delivery->id)
            ->where('payment_method', PaymentMethod::Paymob->value)
            ->where('payment_status', PaymentStatus::Paid->value)
            ->whereNotNull('paymob_transaction_id')
            ->whereDoesntHave('deliveryWithdrawalAllocations', function ($query) {
                $query->whereHas('withdrawalRequest', function ($subQuery) {
                    $subQuery->whereIn('status', [
                        DeliveryWithdrawalStatus::Pending->value,
                        DeliveryWithdrawalStatus::Approved->value,
                    ]);
                });
            })
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function createWithdrawalRequest(DeliveryUser $delivery, array $data): DeliveryWithdrawalRequest
    {
        return DB::transaction(function () use ($delivery, $data) {
            $lockedDelivery = DeliveryUser::query()->whereKey($delivery->id)->firstOrFail();
            $amount = round((float) $data['amount'], 2);

            if ($amount <= 0) {
                abort(422, __('delivery.invalid_withdraw_amount'));
            }

            $availableOrders = Order::query()
                ->where('delivery_id', $lockedDelivery->id)
                ->where('payment_method', PaymentMethod::Paymob->value)
                ->where('payment_status', PaymentStatus::Paid->value)
                ->whereNotNull('paymob_transaction_id')
                ->whereDoesntHave('deliveryWithdrawalAllocations', function ($query) {
                    $query->whereHas('withdrawalRequest', function ($subQuery) {
                        $subQuery->whereIn('status', [
                            DeliveryWithdrawalStatus::Pending->value,
                            DeliveryWithdrawalStatus::Approved->value,
                        ]);
                    });
                })
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $matchedOrders = $this->findMatchingOrdersByAmount($availableOrders, $amount);

            if ($matchedOrders->isEmpty()) {
                abort(422, __('delivery.withdraw_amount_must_match_full_orders'));
            }

            $withdrawalRequest = DeliveryWithdrawalRequest::query()->create([
                'delivery_id' => $lockedDelivery->id,
                'method' => $data['method'],
                'transfer_details' => $data['transfer_details'],
                'amount' => $amount,
                'status' => DeliveryWithdrawalStatus::Pending,
            ]);

            foreach ($matchedOrders as $order) {
                DeliveryWithdrawalRequestOrder::query()->create([
                    'delivery_withdrawal_request_id' => $withdrawalRequest->id,
                    'order_id' => $order->id,
                    'amount' => $order->delivery_fee,
                ]);
            }

            return $withdrawalRequest->load('orderAllocations.order');
        });
    }

    public function approveWithdrawalRequest(DeliveryWithdrawalRequest $request, int $adminId, ?string $adminNote = null): DeliveryWithdrawalRequest
    {
        return DB::transaction(function () use ($request, $adminId, $adminNote) {
            $withdrawalRequest = DeliveryWithdrawalRequest::query()
                ->whereKey($request->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($withdrawalRequest->status !== DeliveryWithdrawalStatus::Pending) {
                abort(422, __('delivery.withdraw_request_already_processed'));
            }

            $withdrawalRequest->update([
                'status' => DeliveryWithdrawalStatus::Approved,
                'admin_note' => $adminNote,
                'processed_by_admin_id' => $adminId,
                'processed_at' => now(),
            ]);

            // Subtract the amount from the delivery user's balance
            $deliveryUser = DeliveryUser::query()->whereKey($withdrawalRequest->delivery_id)->lockForUpdate()->firstOrFail();
            $deliveryUser->decrement('balance', $withdrawalRequest->amount);

            return $withdrawalRequest->fresh(['delivery', 'processedByAdmin', 'orderAllocations.order']);
        });
    }

    public function rejectWithdrawalRequest(DeliveryWithdrawalRequest $request, int $adminId, ?string $adminNote = null): DeliveryWithdrawalRequest
    {
        return DB::transaction(function () use ($request, $adminId, $adminNote) {
            $withdrawalRequest = DeliveryWithdrawalRequest::query()
                ->whereKey($request->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($withdrawalRequest->status !== DeliveryWithdrawalStatus::Pending) {
                abort(422, __('delivery.withdraw_request_already_processed'));
            }

            $withdrawalRequest->update([
                'status' => DeliveryWithdrawalStatus::Rejected,
                'admin_note' => $adminNote,
                'processed_by_admin_id' => $adminId,
                'processed_at' => now(),
            ]);

            return $withdrawalRequest->fresh(['delivery', 'processedByAdmin', 'orderAllocations.order']);
        });
    }

    private function findMatchingOrdersByAmount(Collection $orders, float $targetAmount): Collection
    {
        $targetCents = (int) round($targetAmount * 100);

        if ($targetCents <= 0) {
            return collect();
        }

        $states = [
            0 => [],
        ];

        foreach ($orders as $order) {
            $orderCents = (int) round(((float) $order->delivery_fee) * 100);
            $nextStates = $states;

            foreach ($states as $sum => $selectedOrderIds) {
                $newSum = $sum + $orderCents;

                if ($newSum > $targetCents || array_key_exists($newSum, $nextStates)) {
                    continue;
                }

                $nextStates[$newSum] = [...$selectedOrderIds, $order->id];
            }

            $states = $nextStates;

            if (array_key_exists($targetCents, $states)) {
                break;
            }
        }

        if (! array_key_exists($targetCents, $states)) {
            return collect();
        }

        $selectedIds = $states[$targetCents];

        return $orders->whereIn('id', $selectedIds)->values();
    }
}
