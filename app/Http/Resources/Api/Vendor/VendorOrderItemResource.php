<?php

namespace App\Http\Resources\Api\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorOrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $mainImage = $this->product?->images?->firstWhere('is_main', true)
            ?? $this->product?->images?->first();

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'product_unit' => $this->product_unit,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'discount' => $this->discount,
            'total_price' => $this->total_price,
            'image' => $mainImage ? asset($mainImage->image) : null,
        ];
    }
}
