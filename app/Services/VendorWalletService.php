<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\VendorWithdrawalStatus;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\VendorWithdrawalRequest;
use App\Models\VendorWithdrawalRequestOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VendorWalletService
{
    public function getWithdrawableOrders(Vendor $vendor, int $perPage = 15): LengthAwarePaginator
    {
        return Order::query()
            ->where('vendor_id', $vendor->id)
            ->where('payment_method', PaymentMethod::Paymob->value)
            ->where('payment_status', PaymentStatus::Paid->value)
            ->whereNotNull('paymob_transaction_id')
            ->whereDoesntHave('withdrawalAllocations', function ($query) {
                $query->whereHas('withdrawalRequest', function ($subQuery) {
                    $subQuery->whereIn('status', [
                        VendorWithdrawalStatus::Pending->value,
                        VendorWithdrawalStatus::Approved->value,
                    ]);
                });
            })
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function createWithdrawalRequest(Vendor $vendor, array $data): VendorWithdrawalRequest
    {
        return DB::transaction(function () use ($vendor, $data) {
            $lockedVendor = Vendor::query()->whereKey($vendor->id)->firstOrFail();
            $amount = round((float) $data['amount'], 2);

            if ($amount <= 0) {
                abort(422, __('vendor.invalid_withdraw_amount'));
            }

            $availableOrders = Order::query()
                ->where('vendor_id', $lockedVendor->id)
                ->where('payment_method', PaymentMethod::Paymob->value)
                ->where('payment_status', PaymentStatus::Paid->value)
                ->whereNotNull('paymob_transaction_id')
                ->whereDoesntHave('withdrawalAllocations', function ($query) {
                    $query->whereHas('withdrawalRequest', function ($subQuery) {
                        $subQuery->whereIn('status', [
                            VendorWithdrawalStatus::Pending->value,
                            VendorWithdrawalStatus::Approved->value,
                        ]);
                    });
                })
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $matchedOrders = $this->findMatchingOrdersByAmount($availableOrders, $amount);

            if ($matchedOrders->isEmpty()) {
                abort(422, __('vendor.withdraw_amount_must_match_full_orders'));
            }

            $withdrawalRequest = VendorWithdrawalRequest::query()->create([
                'vendor_id' => $lockedVendor->id,
                'method' => $data['method'],
                'transfer_details' => $data['transfer_details'],
                'amount' => $amount,
                'status' => VendorWithdrawalStatus::Pending,
            ]);

            foreach ($matchedOrders as $order) {
                VendorWithdrawalRequestOrder::query()->create([
                    'vendor_withdrawal_request_id' => $withdrawalRequest->id,
                    'order_id' => $order->id,
                    'amount' => $order->total,
                ]);
            }

            return $withdrawalRequest->load('orderAllocations.order');
        });
    }

    public function approveWithdrawalRequest(VendorWithdrawalRequest $request, int $adminId, ?string $adminNote = null): VendorWithdrawalRequest
    {
        return DB::transaction(function () use ($request, $adminId, $adminNote) {
            $withdrawalRequest = VendorWithdrawalRequest::query()
                ->whereKey($request->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($withdrawalRequest->status !== VendorWithdrawalStatus::Pending) {
                abort(422, __('vendor.withdraw_request_already_processed'));
            }

            $withdrawalRequest->update([
                'status' => VendorWithdrawalStatus::Approved,
                'admin_note' => $adminNote,
                'processed_by_admin_id' => $adminId,
                'processed_at' => now(),
            ]);

            return $withdrawalRequest->fresh(['vendor', 'processedByAdmin', 'orderAllocations.order']);
        });
    }

    public function rejectWithdrawalRequest(VendorWithdrawalRequest $request, int $adminId, ?string $adminNote = null): VendorWithdrawalRequest
    {
        return DB::transaction(function () use ($request, $adminId, $adminNote) {
            $withdrawalRequest = VendorWithdrawalRequest::query()
                ->whereKey($request->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($withdrawalRequest->status !== VendorWithdrawalStatus::Pending) {
                abort(422, __('vendor.withdraw_request_already_processed'));
            }

            $withdrawalRequest->update([
                'status' => VendorWithdrawalStatus::Rejected,
                'admin_note' => $adminNote,
                'processed_by_admin_id' => $adminId,
                'processed_at' => now(),
            ]);

            return $withdrawalRequest->fresh(['vendor', 'processedByAdmin', 'orderAllocations.order']);
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
            $orderCents = (int) round(((float) $order->total) * 100);
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
