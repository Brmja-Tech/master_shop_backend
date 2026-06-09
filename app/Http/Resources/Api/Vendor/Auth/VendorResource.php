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
            'store_type_id' => $this->store_type_id,
            'is_active' => $this->is_active,
            'is_verified' => $this->is_verified,
        ];
    }
}
