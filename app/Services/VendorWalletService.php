<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\VendorWithdrawalStatus;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\VendorWithdrawalRequest;
use App\Models\VendorWithdrawalRequestOrder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VendorWalletService
{
    public function getWithdrawableOrders(Vendor $vendor): Collection
    {
        $eligibleOrders = Order::query()
            ->where('vendor_id', $vendor->id)
            ->where('payment_method', PaymentMethod::Paymob->value)
            ->where('payment_status', PaymentStatus::Paid->value)
            ->whereNotNull('paymob_transaction_id')
            ->orderBy('id')
            ->get();

        return $eligibleOrders->filter(function (Order $order) {
            $allocatedAmount = (float) VendorWithdrawalRequestOrder::query()
                ->where('order_id', $order->id)
                ->whereHas('withdrawalRequest', function ($query) {
                    $query->whereIn('status', [
                        VendorWithdrawalStatus::Pending->value,
                        VendorWithdrawalStatus::Approved->value,
                    ]);
                })
                ->sum('amount');

            $availableAmount = round((float) $order->total - $allocatedAmount, 2);

            return $availableAmount > 0;
        })->values();
    }

    public function createWithdrawalRequest(Vendor $vendor, array $data): VendorWithdrawalRequest
    {
        return DB::transaction(function () use ($vendor, $data) {
            $lockedVendor = Vendor::query()->whereKey($vendor->id)->firstOrFail();
            $amount = round((float) $data['amount'], 2);

            if ($amount <= 0) {
                abort(422, __('vendor.invalid_withdraw_amount'));
            }

            $eligibleOrders = Order::query()
                ->where('vendor_id', $lockedVendor->id)
                ->where('payment_method', PaymentMethod::Paymob->value)
                ->where('payment_status', PaymentStatus::Paid->value)
                ->whereNotNull('paymob_transaction_id')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $remainingAmount = $amount;
            $allocations = [];

            foreach ($eligibleOrders as $order) {
                $allocatedAmount = (float) VendorWithdrawalRequestOrder::query()
                    ->where('order_id', $order->id)
                    ->whereHas('withdrawalRequest', function ($query) {
                        $query->whereIn('status', [
                            VendorWithdrawalStatus::Pending->value,
                            VendorWithdrawalStatus::Approved->value,
                        ]);
                    })
                    ->sum('amount');

                $availableAmount = round((float) $order->total - $allocatedAmount, 2);

                if ($availableAmount <= 0) {
                    continue;
                }

                // Withdraw requests can only consume whole orders, never partial amounts.
                if ($availableAmount > $remainingAmount) {
                    continue;
                }

                $allocations[] = [
                    'order' => $order,
                    'amount' => $availableAmount,
                ];

                $remainingAmount = round($remainingAmount - $availableAmount, 2);

                if ($remainingAmount <= 0) {
                    break;
                }
            }

            if ($remainingAmount > 0) {
                abort(422, __('vendor.withdraw_amount_must_match_full_orders'));
            }

            $withdrawalRequest = VendorWithdrawalRequest::query()->create([
                'vendor_id' => $lockedVendor->id,
                'method' => $data['method'],
                'transfer_details' => $data['transfer_details'],
                'amount' => $amount,
                'status' => VendorWithdrawalStatus::Pending,
            ]);

            foreach ($allocations as $allocation) {
                VendorWithdrawalRequestOrder::query()->create([
                    'vendor_withdrawal_request_id' => $withdrawalRequest->id,
                    'order_id' => $allocation['order']->id,
                    'amount' => $allocation['amount'],
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
}
