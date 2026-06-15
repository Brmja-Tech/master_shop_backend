<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status?->value,
            'payment_method' => $this->payment_method?->value,
            'payment_status' => $this->payment_status?->value,
            'payment_url' => $this->payment_url,
            'total' => $this->total,
            'delivery_address' => $this->delivery_address,
            'notes' => $this->notes,
        ];
    }
}
