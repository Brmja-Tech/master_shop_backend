<?php

namespace App\Http\Resources\Api\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorWithdrawalRequestOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_id' => $this->order_id,
            'amount' => (float) $this->amount,
            'paymob_order_id' => $this->whenLoaded('order', fn () => $this->order?->paymob_order_id),
            'paymob_transaction_id' => $this->whenLoaded('order', fn () => $this->order?->paymob_transaction_id),
        ];
    }
}
