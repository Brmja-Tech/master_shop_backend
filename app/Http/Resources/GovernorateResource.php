<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GovernorateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'country_id'     => $this->country_id,
            'name'           => $this->name,
            'shipping_price' => (float) $this->shipping_price,
            'is_active'      => (bool) $this->is_active,
        ];
    }
}
