<?php

namespace App\Http\Resources\Api\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Vendor\VendorOrderItemResource;

class VendorOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isDetailed = $this->relationLoaded('items');

        return [
            'id' => $this->id,
            'paymob_order_id' => $this->paymob_order_id,
            'paymob_transaction_id' => $this->paymob_transaction_id,
            'customer_first_name' => $this->customer_first_name,
            'customer_last_name' => $this->customer_last_name,
            'customer_phone' => $this->customer_phone,
            'status' => $this->status?->value,
            'delivery_id' => $this->delivery_id,
            'delivery_status' => $this->delivery_status,
            'payment_method' => $this->payment_method?->value,
            'payment_status' => $this->payment_status?->value,
            'subtotal' => $this->when($isDetailed, $this->subtotal),
            'delivery_fee' => $this->when($isDetailed, $this->delivery_fee),
            'total' => $this->total,
            'delivery_address' => $this->delivery_address,
            'notes' => $this->when($isDetailed, $this->notes),
            'cancellation_reason' => $this->when($isDetailed, $this->cancellation_reason),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'items_count' => $this->whenCounted('items'),
            'items' => $this->whenLoaded('items', function () {
                return VendorOrderItemResource::collection($this->items);
            }),
            'delivery' => $this->whenLoaded('delivery', function () {
                if (! $this->delivery) {
                    return null;
                }

                return [
                    'id' => $this->delivery->id,
                    'name' => $this->delivery->name,
                    'phone' => $this->delivery->phone,
                    'email' => $this->delivery->email,
                    'lat' => $this->delivery->lat !== null ? (float) $this->delivery->lat : null,
                    'lng' => $this->delivery->lng !== null ? (float) $this->delivery->lng : null,
                ];
            }),
        ];
    }
}
