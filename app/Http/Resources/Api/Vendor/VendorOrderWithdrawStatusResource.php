<?php

namespace App\Http\Resources\Api\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorOrderWithdrawStatusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $latestAllocation = $this->whenLoaded('withdrawalAllocations', function () {
            return $this->withdrawalAllocations
                ->sortByDesc('id')
                ->first();
        });

        $withdrawalRequest = $latestAllocation?->withdrawalRequest;
        $allocatedAmount = (float) ($this->allocated_withdraw_amount ?? 0);
        $remainingAmount = max(0, round((float) $this->total - $allocatedAmount, 2));

        return [
            'id' => $this->id,
            'paymob_order_id' => $this->paymob_order_id,
            'paymob_transaction_id' => $this->paymob_transaction_id,
            'order_total' => (float) $this->total,
            'total' => $remainingAmount,
            'payment_status' => $this->payment_status?->value,
            'is_paid' => $this->payment_status?->value === 'paid',
            'withdraw_status' => $withdrawalRequest?->status?->value ?? 'available',
            'withdraw_request_id' => $withdrawalRequest?->id,
            'withdrawn_amount' => $allocatedAmount,
            'remaining_withdrawable_amount' => $remainingAmount,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
