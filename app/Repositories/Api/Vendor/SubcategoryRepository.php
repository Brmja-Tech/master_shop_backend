<?php

namespace App\Repositories\Api\Vendor;

use App\Models\Subcategory;

class SubcategoryRepository
{
    public function format(Subcategory $subcategory): array
    {
        return [
            'id' => $subcategory->id,
            'store_type_id' => $subcategory->store_type_id,
            'name' => $subcategory->getTranslation('name', app()->getLocale()),
        ];
    }

    public function getAll(int $storeTypeId)
    {
        return Subcategory::query()
            ->where('store_type_id', $storeTypeId)
            ->get(['id', 'store_type_id', 'name'])
            ->map(fn (Subcategory $subcategory) => $this->format($subcategory))
            ->sortBy('name')
            ->values();
    }

    public function lookup(int $storeTypeId)
    {
        return Subcategory::query()
            ->where('store_type_id', $storeTypeId)
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

    public function findForStoreType(int $id, int $storeTypeId): Subcategory
    {
        return Subcategory::query()
            ->where('store_type_id', $storeTypeId)
            ->findOrFail($id);
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
