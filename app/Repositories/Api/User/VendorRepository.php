<?php

namespace App\Repositories\Api\User;

use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class VendorRepository
{
    public function searchByName(string $search)
    {
        return Vendor::query()
            ->where('is_active', true)
            ->where('is_verified', true)
            ->with('storeType:id,name')
            ->select([
                'id',
                'store_name',
                'logo',
                'rate',
                'latitude',
                'longitude',
                'store_type_id',
            ])
            ->where(function (Builder $query) use ($search) {
                $like = '%' . $search . '%';

                $query->where('store_name', 'like', $like)
                    ->orWhereRaw("JSON_VALID(store_name) AND JSON_UNQUOTE(JSON_EXTRACT(store_name, '$.ar')) LIKE ?", [$like])
                    ->orWhereRaw("JSON_VALID(store_name) AND JSON_UNQUOTE(JSON_EXTRACT(store_name, '$.en')) LIKE ?", [$like])
                    ->orWhereHas('storeType', function (Builder $storeTypeQuery) use ($like) {
                        $storeTypeQuery->where('name', 'like', $like)
                            ->orWhereRaw("JSON_VALID(name) AND JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) LIKE ?", [$like])
                            ->orWhereRaw("JSON_VALID(name) AND JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", [$like]);
                    });
            })
            ->orderByDesc('rate')
            ->orderByDesc('id')
            ->get();
    }

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
        ?string $search = null,
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
            ->when($search, function (Builder $query) use ($search) {
                $like = '%' . $search . '%';

                $query->where(function (Builder $query) use ($like) {
                    $query->where('store_name', 'like', $like)
                        ->orWhereRaw("JSON_VALID(store_name) AND JSON_UNQUOTE(JSON_EXTRACT(store_name, '$.ar')) LIKE ?", [$like])
                        ->orWhereRaw("JSON_VALID(store_name) AND JSON_UNQUOTE(JSON_EXTRACT(store_name, '$.en')) LIKE ?", [$like])
                        ->orWhereHas('storeType', function (Builder $storeTypeQuery) use ($like) {
                            $storeTypeQuery->where('name', 'like', $like)
                                ->orWhereRaw("JSON_VALID(name) AND JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) LIKE ?", [$like])
                                ->orWhereRaw("JSON_VALID(name) AND JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", [$like]);
                        });
                });
            })
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
