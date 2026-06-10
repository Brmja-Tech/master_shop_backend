<?php

namespace App\Services\Api\Admin;

use App\Repositories\Api\Admin\SubcategoryRepository;

class SubcategoryService
{
    public function __construct(
        protected SubcategoryRepository $repository
    ) {}

    public function index(?int $storeTypeId = null)
    {
        return $this->repository->getAll($storeTypeId);
    }

    public function lookup(?int $storeTypeId = null)
    {
        return $this->repository->lookup($storeTypeId);
    }

    public function store(array $data)
    {
        $data['vendor_id'] = null;

        return $this->repository->format(
            $this->repository->create($data)
        );
    }

    public function show(int $id)
    {
        return $this->repository->format(
            $this->repository->find($id)
        );
    }

    public function update(int $id, array $data)
    {
        $subcategory = $this->repository->find($id);
        $data['vendor_id'] = null;

        return $this->repository->format(
            $this->repository->update($subcategory, $data)
        );
    }

    public function destroy(int $id)
    {
        return $this->repository->delete(
            $this->repository->find($id)
        );
    }
}
