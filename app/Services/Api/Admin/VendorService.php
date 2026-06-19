<?php

namespace App\Services\Api\Admin;

use App\Models\Vendor;
use App\Repositories\Api\Admin\VendorRepository;
use App\Utils\ImageManger;
use Illuminate\Support\Facades\Hash;

class VendorService
{
    public function __construct(
        protected VendorRepository $repository,
        protected ImageManger $imageManger
    ) {}

    public function index(int $perPage = 15)
    {
        return $this->repository->getAllPaginated($perPage);
    }

    public function show(int $id): Vendor
    {
        return $this->repository->find($id)->load('storeType');
    }

    public function store(array $data): Vendor
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        if (isset($data['logo']) && $data['logo'] !== null) {
            $data['logo'] = $this->imageManger->uploadImage('/uploads/vendors', $data['logo']);
        }

        if (isset($data['banner']) && $data['banner'] !== null) {
            $data['banner'] = $this->imageManger->uploadImage('/uploads/vendors', $data['banner']);
        }

        return $this->repository->create($data)->load('storeType');
    }

    public function update(int $id, array $data): Vendor
    {
        $vendor = $this->repository->find($id);

        if (isset($data['password']) && $data['password'] !== null && $data['password'] !== '') {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (isset($data['logo']) && $data['logo'] !== null) {
            if (! empty($vendor->logo)) {
                $this->imageManger->deleteImage($vendor->logo);
            }
            $data['logo'] = $this->imageManger->uploadImage('/uploads/vendors', $data['logo']);
        }

        if (isset($data['banner']) && $data['banner'] !== null) {
            if (! empty($vendor->banner)) {
                $this->imageManger->deleteImage($vendor->banner);
            }
            $data['banner'] = $this->imageManger->uploadImage('/uploads/vendors', $data['banner']);
        }

        return $this->repository->update($vendor, $data)->load('storeType');
    }
}
