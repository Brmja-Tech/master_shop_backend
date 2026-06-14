<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->whenLoaded('product');
        $mainImage = $product?->images?->firstWhere('is_main', true) ?? $product?->images?->first();
        $unitPrice = (float) ($product?->price_after_discount ?? 0);
        $totalPrice = $unitPrice * (int) $this->quantity;

        return [
            'id' => $this->id,
            'quantity' => (int) $this->quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'product' => $product ? [
                'id' => $product->id,
                'vendor_id' => $product->vendor_id,
                'subcategory_id' => $product->subcategory_id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => (float) $product->price,
                'discount' => (float) $product->discount,
                'price_after_discount' => (float) $product->price_after_discount,
                'remaining_quantity' => $product->remaining_quantity,
                'is_available' => $product->is_available,
                'image' => $mainImage ? asset($mainImage->image) : null,
            ] : null,
        ];
    }
}
