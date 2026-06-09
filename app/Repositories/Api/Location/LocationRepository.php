<?php

namespace App\Repositories\Api\Location;

use App\Models\Country;
use App\Models\Governorate;
use Illuminate\Database\Eloquent\Collection;

class LocationRepository
{
    public function getActiveCountriesWithGovernorates(): Collection
    {
        return Country::query()
            ->where('status', 1)
            ->orderBy('id')
            ->with(['governorates' => function ($q) {
                $q->where('status', 1)->orderBy('id');
            }])
            ->get();
    }

    public function getActiveGovernoratesByCountry(int $countryId): Collection
    {
        return Governorate::query()
            ->where('country_id', $countryId)
            ->where('status', 1)
            ->orderBy('id')
            ->get();
    }
}
