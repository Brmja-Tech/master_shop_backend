<?php

namespace App\Http\Resources\Api\Delivery;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryWithdrawableOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'paymob_order_id' => $this->paymob_order_id,
            'paymob_transaction_id' => $this->paymob_transaction_id,
            'delivery_fee' => (float) $this->delivery_fee,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
