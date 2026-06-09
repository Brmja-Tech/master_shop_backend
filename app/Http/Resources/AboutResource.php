<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'banner'         => asset($this->banner),
            'title'          => $this->title,
            'description'    => $this->desc,
            'image'          => asset($this->image),
            'updated_at'     => $this->updated_at?->format('Y-m-d'),
        ];
    }
}
