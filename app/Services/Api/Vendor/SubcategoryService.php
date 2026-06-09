<?php

namespace App\Services\Api\Vendor;

use App\Models\Vendor;
use App\Repositories\Api\Vendor\SubcategoryRepository;

class SubcategoryService
{
    public function __construct(
        protected SubcategoryRepository $repository
    ) {}

    public function index(Vendor $vendor)
    {
        return $this->repository->getAll($vendor->store_type_id);
    }

    public function lookup(Vendor $vendor)
    {
        return $this->repository->lookup($vendor->store_type_id);
    }

    public function store(Vendor $vendor, array $data)
    {
        $data['store_type_id'] = $vendor->store_type_id;

        return $this->repository->format(
            $this->repository->create($data)
        );
    }

    public function show(Vendor $vendor, int $id)
    {
        return $this->repository->format(
            $this->repository->findForStoreType($id, $vendor->store_type_id)
        );
    }

    public function update(Vendor $vendor, int $id, array $data)
    {
        $subcategory = $this->repository->findForStoreType($id, $vendor->store_type_id);

        return $this->repository->format(
            $this->repository->update($subcategory, $data)
        );
    }

    public function destroy(Vendor $vendor, int $id)
    {
        $subcategory = $this->repository->findForStoreType($id, $vendor->store_type_id);

        return $this->repository->delete($subcategory);
    }
}
