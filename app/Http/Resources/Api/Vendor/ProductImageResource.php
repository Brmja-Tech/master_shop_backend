<?php

namespace App\Http\Resources\Api\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $image = $this->image;

        if ($image && ! str_starts_with($image, 'http://') && ! str_starts_with($image, 'https://')) {
            $image = url($image);
        }

        return [
            'id' => $this->id,
            'image' => $image,
            'is_main' => $this->is_main,
        ];
    }
}
