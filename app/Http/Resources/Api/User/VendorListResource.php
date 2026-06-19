<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonException;

class VendorListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $distanceInMeters = $this->distance_in_meters !== null
            ? round((float) $this->distance_in_meters, 2)
            : null;

        return [
            'id' => $this->id,
            'store_name' => $this->resolveStoreName(),
            'store_type_id' => $this->store_type_id,
            'store_type_name' => $this->whenLoaded('storeType', fn () => $this->storeType?->name),
            'logo' => $this->logo ? url($this->logo) : null,
            'rate' => (float) $this->rate,
            'distance_in_meters' => $distanceInMeters,
            'distance_in_kilometers' => $distanceInMeters !== null ? round($distanceInMeters / 1000, 2) : null,
            'vendor_latitude' => $this->latitude !== null ? (float) $this->latitude : null,
            'vendor_longitude' => $this->longitude !== null ? (float) $this->longitude : null,
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
