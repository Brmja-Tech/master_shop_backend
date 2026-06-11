<?php

namespace App\Http\Resources\Api\Vendor\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner_name' => $this->owner_name,
            'phone' => $this->phone,
            'store_name' => $this->store_name,
            'description' => $this->description,
            'store_type_id' => $this->store_type_id,
            'store_type_name' => $this->whenLoaded('storeType', fn () => $this->storeType?->name),
            'logo' => $this->logo ? url($this->logo) : null,
            'banner' => $this->banner ? url($this->banner) : null,
            'delivery_fee' => (float) $this->delivery_fee,
            'rate' => (float) $this->rate,
            'is_active' => $this->is_active,
            'work_from' => $this->work_from,
            'work_to' => $this->work_to,
            'is_verified' => $this->is_verified,
        ];
    }
}
