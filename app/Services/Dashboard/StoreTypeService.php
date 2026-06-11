<?php

namespace App\Services\Dashboard;

use App\Repositories\StoreTypeRepository;
use App\Utils\ImageManger;

class StoreTypeService
{
    public function __construct(
        protected StoreTypeRepository $repository,
        protected ImageManger $imageManger
    ) {}

    public function index()
    {
        return $this->repository->getAll();
    }

    public function store(array $data)
    {
        if (isset($data['image'])) {
            $data['image'] = $this->imageManger->uploadImage('/uploads/store-types', $data['image']);
        }

        return $this->repository->create($data);
    }

    public function show(int $id)
    {
        return $this->repository->find($id);
    }

    public function update(int $id, array $data)
    {
        $storeType = $this->repository->find($id);

        if (isset($data['image'])) {
            if (! empty($storeType->image)) {
                $this->imageManger->deleteImage($storeType->image);
            }

            $data['image'] = $this->imageManger->uploadImage('/uploads/store-types', $data['image']);
        }

        return $this->repository->update($storeType, $data);
    }

    public function destroy(int $id)
    {
        $storeType = $this->repository->find($id);

        if (! empty($storeType->image)) {
            $this->imageManger->deleteImage($storeType->image);
        }

        return $this->repository->delete($storeType);
    }

    public function lookup()
    {
        return $this->repository->lookup();
    }
}
