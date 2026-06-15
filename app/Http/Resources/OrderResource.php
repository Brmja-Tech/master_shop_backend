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
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'payment_method' => $this->payment_method?->value,
            'payment_status' => $this->payment_status?->value,
            'payment_url' => $this->payment_url,
            'subtotal' => $this->subtotal,
            'discount_amount' => $this->discount_amount,
            'delivery_fee' => $this->delivery_fee,
            'total' => $this->total,
            'delivery_address' => $this->delivery_address,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toISOString(),
            'vendor' => $this->whenLoaded('vendor', function () {
                return [
                    'id' => $this->vendor->id,
                    'store_name' => $this->vendor->store_name,
                    'logo' => $this->vendor->logo ? asset($this->vendor->logo) : null,
                ];
            }),
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    $mainImage = $item->product?->mainImage?->first();

                    return [
                        'id' => $item->id,
                        'product_name' => $item->product_name,
                        'product_unit' => $item->product_unit,
                        'unit_price' => $item->unit_price,
                        'discount' => $item->discount,
                        'final_price' => $item->final_price,
                        'quantity' => (int) $item->quantity,
                        'total_price' => $item->total_price,
                        'product' => $item->relationLoaded('product') && $item->product ? [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'main_image' => $mainImage ? asset($mainImage->image) : null,
                        ] : null,
                    ];
                })->values();
            }),
            'delivery' => $this->whenLoaded('delivery', function () {
                return [
                    'status' => $this->delivery->status?->value,
                    'status_label' => $this->delivery->status?->label(),
                    'driver' => $this->delivery->relationLoaded('driver') && $this->delivery->driver ? [
                        'id' => $this->delivery->driver->id,
                        'name' => $this->delivery->driver->name,
                        'phone' => $this->delivery->driver->phone,
                    ] : null,
                    'estimated_minutes' => $this->delivery->estimated_minutes,
                    'driver_rating' => $this->delivery->driver_rating,
                ];
            }),
            'items_count' => $this->when(
                ! $this->relationLoaded('items') && isset($this->items_count),
                (int) $this->items_count
            ),
        ];
    }
}
