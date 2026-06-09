<?php

namespace App\Services\Dashboard;

use App\Repositories\StoreTypeRepository;

class StoreTypeService
{
    protected StoreTypeRepository $repository;

    public function __construct(StoreTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return $this->repository->getAll();
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }

    public function show(int $id)
    {
        return $this->repository->find($id);
    }

    public function update(int $id, array $data)
    {
        $storeType = $this->repository->find($id);

        return $this->repository->update($storeType, $data);
    }

    public function destroy(int $id)
    {
        $storeType = $this->repository->find($id);

        return $this->repository->delete($storeType);
    }
    public function lookup()
{
    return $this->repository->lookup();
}
}
