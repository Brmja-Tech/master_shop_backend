<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'store_name' => $this->store_name,
            'description' => $this->description,
            'store_type_id' => $this->store_type_id,
            'logo' => $this->logo ? asset($this->logo) : null,
            'banner' => $this->banner ? asset($this->banner) : null,
            'rate' => $this->rate,
            'delivery_fee' => $this->delivery_fee,
            'distance_in_km' => $this->distance_in_km,
            'is_store_open' => $this->is_store_open,
            'subcategories' => $this->profile_subcategories,
            'products' => $this->products->map(function ($product) {
                $mainImage = $product->images->where('is_main', true)->first();
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => (float) $product->price,
                    'discount' => (float) $product->discount,
                    'price_after_discount' => (float) $product->price_after_discount,
                    'main_image' => $mainImage ? asset($mainImage->image) : null,
                ];
            }),
        ];
    }
}
