<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'subcategory_id' => $this->subcategory_id,
            'subcategory_name' => $this->whenLoaded('subcategory', fn () => $this->subcategory?->name),
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'discount' => (float) $this->discount,
            'price_after_discount' => (float) $this->price_after_discount,
            'quantity' => $this->quantity,
            'remaining_quantity' => $this->remaining_quantity,
            'is_available' => $this->is_available,
            'unit' => $this->unit,
            'expiry_date' => optional($this->expiry_date)->format('Y-m-d'),
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(function ($image) {
                    $url = $image->image;
                    if ($url && ! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
                        $url = url($url);
                    }
                    return [
                        'id' => $image->id,
                        'url' => $url,
                        'is_main' => $image->is_main,
                    ];
                });
            }),
        ];
    }
}
