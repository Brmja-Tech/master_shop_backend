<?php

namespace App\Http\Resources\Api\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'subcategory_id' => $this->subcategory_id,
            'subcategory_name' => $this->whenLoaded('subcategory', fn () => $this->subcategory?->getTranslation('name', $locale)),
            'name' => $this->getTranslation('name', $locale),
            'description' => $this->getTranslation('description', $locale),
            'brand' => $this->brand ? $this->getTranslation('brand', $locale) : null,
            'quantity' => $this->quantity,
            'remaining_quantity' => $this->remaining_quantity,
            'price' => (float) $this->price,
            'discount' => (float) $this->discount,
            'price_after_discount' => $this->price_after_discount,
            'is_available' => $this->is_available,
            'unit' => $this->unit,
            'expiry_date' => optional($this->expiry_date)->format('Y-m-d'),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
