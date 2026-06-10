<?php

namespace App\Http\Resources\Api\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailableProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $image = $this->whenLoaded('images', fn () => optional($this->images->firstWhere('is_main', true) ?? $this->images->first())->image);

        if ($image && ! str_starts_with($image, 'http://') && ! str_starts_with($image, 'https://')) {
            $image = url($image);
        }

        return [
            'id' => $this->id,
            'subcategory_id' => $this->subcategory_id,
            'subcategory_name' => $this->whenLoaded('subcategory', fn () => $this->subcategory?->getTranslation('name', $locale)),
            'name' => $this->getTranslation('name', $locale),
            'description' => $this->getTranslation('description', $locale),
            'brand' => $this->brand ? $this->getTranslation('brand', $locale) : null,
            'price' => (float) $this->price,
            'discount' => (float) $this->discount,
            'price_after_discount' => $this->price_after_discount,
            'remaining_quantity' => $this->remaining_quantity,
            'is_available' => $this->is_available,
            'expiry_date' => optional($this->expiry_date)->format('Y-m-d'),
            'image' => $image,
        ];
    }
}
