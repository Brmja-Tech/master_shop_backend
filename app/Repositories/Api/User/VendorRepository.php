<?php

namespace App\Repositories\Api\User;

use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
}
