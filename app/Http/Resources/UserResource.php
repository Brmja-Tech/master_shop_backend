<?php

namespace App\Http\Resources;

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
        return [
            'id'         => $this->id,
            'image'      => $this->image ? asset($this->image) : null,
            'name'       => $this->name,
            'email'      => $this->email?? null,
            'phone'      => $this->phone,
            'fcm_token'  => $this->fcm_token ?? null,
            'address'    => $this->address ?? null,
            'area'       => $this->area ?? null,
            'latitude'   => $this->latitude ?? null,
            'longitude'  => $this->longitude ?? null,
        ];
    }
}
