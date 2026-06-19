<?php

namespace App\Http\Resources\Api\Admin;

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
            'rate' => (float) $this->rate,
            'is_active' => (bool) $this->is_active,
            'is_store_open' => (bool) $this->is_store_open,
            'is_accepting_orders' => (bool) $this->is_accepting_orders,
            'working_hours' => $this->working_hours,
            'work_from' => $this->work_from,
            'work_to' => $this->work_to,
            'is_verified' => (bool) $this->is_verified,
            'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
            'address_description' => $this->address_description,
        ];
    }
}
