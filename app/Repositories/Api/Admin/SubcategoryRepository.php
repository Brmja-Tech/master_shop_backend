<?php

namespace App\Repositories\Api\Admin;

use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Builder;

class SubcategoryRepository
{
    public function format(Subcategory $subcategory): array
    {
        return [
            'id' => $subcategory->id,
            'store_type_id' => $subcategory->store_type_id,
            'vendor_id' => $subcategory->vendor_id,
            'is_vendor_created' => $subcategory->vendor_id !== null,
            'name' => $subcategory->getTranslation('name', app()->getLocale()),
        ];
    }

    public function getAll(?int $storeTypeId = null)
    {
        return Subcategory::query()
            ->when($storeTypeId, fn (Builder $query) => $query->where('store_type_id', $storeTypeId))
            ->get(['id', 'store_type_id', 'vendor_id', 'name'])
            ->map(fn (Subcategory $subcategory) => $this->format($subcategory))
            ->sortBy('name')
            ->values();
    }

    public function lookup(?int $storeTypeId = null)
    {
        return Subcategory::query()
            ->when($storeTypeId, fn (Builder $query) => $query->where('store_type_id', $storeTypeId))
            ->get(['id', 'name'])
            ->map(fn (Subcategory $subcategory) => [
                'id' => $subcategory->id,
                'name' => $subcategory->getTranslation('name', app()->getLocale()),
            ])
            ->sortBy('name')
            ->values();
    }

    public function create(array $data): Subcategory
    {
        return Subcategory::create($data);
    }

    public function find(int $id): Subcategory
    {
        return Subcategory::query()->findOrFail($id);
    }

    public function update(Subcategory $subcategory, array $data): Subcategory
    {
        $subcategory->update($data);

        return $subcategory->fresh();
    }

    public function delete(Subcategory $subcategory): bool
    {
        return $subcategory->delete();
    }
}
