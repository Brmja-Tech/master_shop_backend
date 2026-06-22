<?php

namespace App\Http\Resources\Api\Delivery;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailableDeliveryOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->id,
            'status' => $this->status?->value,
            'delivery_status' => $this->delivery_status,
            'customer_name' => trim(($this->customer_first_name ?? '') . ' ' . ($this->customer_last_name ?? '')),
            'customer_first_name' => $this->customer_first_name,
            'customer_last_name' => $this->customer_last_name,
            'customer_phone' => $this->customer_phone,
            'payment_method' => $this->payment_method?->value,
            'payment_status' => $this->payment_status?->value,
            'delivery_location' => [
                'latitude' => $this->delivery_latitude !== null ? (float) $this->delivery_latitude : null,
                'longitude' => $this->delivery_longitude !== null ? (float) $this->delivery_longitude : null,
                'address' => $this->delivery_address,
            ],
            'pricing' => [
                'subtotal' => (float) $this->subtotal,
                'delivery_fee' => (float) $this->delivery_fee,
                'total' => (float) $this->total,
            ],
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'items_count' => $this->items_count ?? null,
            'distance_km' => isset($this->distance_km) ? round((float) $this->distance_km, 2) : null,
            'vendor' => [
                'id' => $this->vendor?->id,
                'store_name' => $this->vendor?->store_name,
                'phone' => $this->vendor?->phone,
                'logo' => $this->vendor?->logo ? asset($this->vendor->logo) : null,
                'latitude' => $this->vendor?->latitude !== null ? (float) $this->vendor->latitude : null,
                'longitude' => $this->vendor?->longitude !== null ? (float) $this->vendor->longitude : null,
                'address_description' => $this->vendor?->address_description,
            ],
        ];
    }
}
