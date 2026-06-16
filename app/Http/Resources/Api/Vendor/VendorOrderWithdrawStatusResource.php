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

        return [
            'id' => $this->id,
            'paymob_order_id' => $this->paymob_order_id,
            'paymob_transaction_id' => $this->paymob_transaction_id,
            'total' => (float) $this->total,
            'payment_status' => $this->payment_status?->value,
            'withdraw_status' => $withdrawalRequest?->status?->value ?? 'available',
            'withdraw_request_id' => $withdrawalRequest?->id,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
