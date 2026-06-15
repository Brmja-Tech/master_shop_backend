<?php

namespace App\Http\Resources;

use App\Http\Resources\Api\User\UserAddressResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $defaultAddress = $this->whenLoaded('addresses', function () {
            return $this->addresses->firstWhere('is_default', true);
        }, function () {
            return $this->addresses()->where('is_default', true)->latest()->first();
        });

        return [
            'id'         => $this->id,
            'image'      => $this->image ? asset($this->image) : null,
            'name'       => $this->name,
            'email'      => $this->email?? null,
            'phone'      => $this->phone,
            'fcm_token'  => $this->fcm_token ?? null,
            'address'    => $defaultAddress?->address ?? $this->address ?? null,
            'area'       => $defaultAddress?->area ?? $this->area ?? null,
            'latitude'   => $this->latitude ?? null,
            'longitude'  => $this->longitude ?? null,
            
        ];
    }
}
