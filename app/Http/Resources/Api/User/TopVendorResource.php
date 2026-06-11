<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonException;

class TopVendorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'store_name' => $this->resolveStoreName(),
            'logo' => $this->logo ? url($this->logo) : null,
            'rate' => (float) $this->rate,
        ];
    }

    private function resolveStoreName(): ?string
    {
        if (! is_string($this->store_name) || $this->store_name === '') {
            return $this->store_name;
        }

        try {
            $decoded = json_decode($this->store_name, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $this->store_name;
        }

        if (! is_array($decoded)) {
            return $this->store_name;
        }

        $locale = app()->getLocale();

        return $decoded[$locale]
            ?? $decoded['ar']
            ?? $this->store_name;
    }
}
