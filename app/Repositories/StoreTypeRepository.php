<?php

namespace App\Repositories;

use App\Models\StoreType;

class StoreTypeRepository
{
    public function getAll()
    {
        return StoreType::latest()->get();
    }

    public function create(array $data)
    {
        return StoreType::create($data);
    }

    public function find(int $id)
    {
        return StoreType::findOrFail($id);
    }

    public function update(StoreType $storeType, array $data)
    {
        $storeType->update($data);

        return $storeType->fresh();
    }

    public function delete(StoreType $storeType)
    {
        return $storeType->delete();
    }
    public function lookup()
    {
        $locale = app()->getLocale();

        return StoreType::query()
            ->get(['id', 'name'])
            ->map(fn (StoreType $storeType) => [
                'id' => $storeType->id,
                'name' => $storeType->getTranslation('name', $locale),
            ])
            ->sortBy('name')
            ->values();
    }
}
