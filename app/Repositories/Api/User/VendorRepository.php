<?php

namespace App\Repositories\Api\User;

use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class VendorRepository
{
    public function getTopRatedPaginated(int $perPage): LengthAwarePaginator
    {
        return Vendor::query()
            ->where('is_active', true)
            ->where('is_verified', true)
            ->orderByDesc('rate')
            ->orderByDesc('id')
            ->paginate($perPage, ['id', 'store_name', 'logo', 'rate', 'is_active', 'is_verified'])
            ->appends(request()->query());
    }

    public function getNearbyPaginated(
        float $latitude,
        float $longitude,
        int $perPage,
        ?int $storeTypeId = null,
        string $sortDirection = 'asc'
    ): LengthAwarePaginator {
        $distanceExpression = '(6371000 * ACOS(
            COS(RADIANS(?)) *
            COS(RADIANS(latitude)) *
            COS(RADIANS(longitude) - RADIANS(?)) +
            SIN(RADIANS(?)) *
            SIN(RADIANS(latitude))
        ))';

        $sortDirection = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';

        return Vendor::query()
            ->where('is_active', true)
            ->where('is_verified', true)
            ->when($storeTypeId, fn (Builder $query) => $query->where('store_type_id', $storeTypeId))
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('storeType:id,name')
            ->withMax([
                'products as max_discount' => function (Builder $query) {
                    $query->where('is_available', true)
                        ->where('remaining_quantity', '>', 0);
                },
            ], 'discount')
            ->select([
                'id',
                'store_name',
                'logo',
                'rate',
                'delivery_fee',
                'latitude',
                'longitude',
                'store_type_id',
            ])
            ->selectRaw(
                $distanceExpression . ' as distance_in_meters',
                [$latitude, $longitude, $latitude]
            )
            ->orderBy('distance_in_meters', $sortDirection)
            ->orderByDesc('rate')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends(request()->query());
    }
}
