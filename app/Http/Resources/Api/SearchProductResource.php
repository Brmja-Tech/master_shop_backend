<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonException;

class SearchProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $image = $this->whenLoaded('images', fn () => optional($this->images->firstWhere('is_main', true) ?? $this->images->first())->image);

        if ($image && ! str_starts_with($image, 'http://') && ! str_starts_with($image, 'https://')) {
            $image = url($image);
        }

        return [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'vendor_name' => $this->whenLoaded('vendor', fn () => $this->resolveVendorName()),
            'store_type_id' => $this->whenLoaded('vendor', fn () => $this->vendor?->store_type_id),
            'store_type_name' => $this->whenLoaded('vendor', fn () => $this->vendor?->storeType?->name),
            'subcategory_id' => $this->subcategory_id,
            'subcategory_name' => $this->whenLoaded('subcategory', fn () => $this->subcategory?->name),
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'discount' => (float) $this->discount,
            'price_after_discount' => $this->price_after_discount,
            'remaining_quantity' => $this->remaining_quantity,
            'is_available' => $this->is_available,
            'expiry_date' => optional($this->expiry_date)->format('Y-m-d'),
            'image' => $image,
        ];
    }

    private function resolveVendorName(): ?string
    {
        $storeName = $this->vendor?->store_name;

        if (! is_string($storeName) || $storeName === '') {
            return $storeName;
        }

        try {
            $decoded = json_decode($storeName, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $storeName;
        }

        if (! is_array($decoded)) {
            return $storeName;
        }

        $locale = app()->getLocale();

        return $decoded[$locale]
            ?? $decoded['ar']
            ?? $storeName;
    }
}
