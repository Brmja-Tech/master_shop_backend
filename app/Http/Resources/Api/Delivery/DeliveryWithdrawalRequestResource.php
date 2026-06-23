<?php

namespace App\Http\Resources\Api\Delivery;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryWithdrawalRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'delivery_id' => $this->delivery_id,
            'delivery_name' => $this->whenLoaded('delivery', fn () => $this->delivery?->name),
            'method' => $this->method,
            'transfer_details' => $this->transfer_details,
            'amount' => (float) $this->amount,
            'status' => $this->status?->value,
            'admin_note' => $this->admin_note,
            'processed_by_admin_id' => $this->processed_by_admin_id,
            'processed_at' => $this->processed_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
            'orders' => DeliveryWithdrawalRequestOrderResource::collection($this->whenLoaded('orderAllocations')),
        ];
    }
}
