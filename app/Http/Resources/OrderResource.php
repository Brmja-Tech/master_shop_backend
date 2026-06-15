<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->customer_first_name,
            'last_name' => $this->customer_last_name,
            'phone' => $this->customer_phone,
            'status' => $this->status?->value,
            'payment_method' => $this->payment_method?->value,
            'payment_status' => $this->payment_status?->value,
            'payment_url' => $this->payment_url,
            'subtotal' => $this->subtotal,
            'delivery_fee' => $this->delivery_fee,
            'total' => $this->total,
            'delivery_address' => $this->delivery_address,
            'notes' => $this->notes,
            'vendor' => $this->whenLoaded('vendor', function () {
                return [
                    'id' => $this->vendor?->id,
                    'store_name' => $this->vendor?->store_name,
                    'logo' => $this->vendor?->logo ? asset($this->vendor->logo) : null,
                ];
            }),
            'items' => $this->whenLoaded('items', function () {
                return OrderItemResource::collection($this->items);
            }),
        ];
    }
}
