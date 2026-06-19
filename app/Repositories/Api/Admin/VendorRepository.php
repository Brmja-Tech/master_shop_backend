<?php

namespace App\Repositories\Api\Admin;

use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VendorRepository
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Vendor::query()
            ->with('storeType')
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Vendor
    {
        return Vendor::create($data);
    }

    public function find(int $id): Vendor
    {
        return Vendor::query()->findOrFail($id);
    }

    public function update(Vendor $vendor, array $data): Vendor
    {
        $vendor->update($data);
        return $vendor->fresh();
    }
}
