<?php

namespace App\Services\Api\Vendor;

use App\Models\Vendor;
use App\Repositories\Api\Vendor\VendorProfileRepository;
use App\Utils\ImageManger;

class VendorProfileService
{
    public function __construct(
        protected VendorProfileRepository $repository,
        protected ImageManger $imageManger
    ) {}

    public function show(Vendor $vendor): Vendor
    {
        return $vendor->load('storeType');
    }

    public function update(Vendor $vendor, array $data): Vendor
    {
        if (isset($data['logo'])) {
            if (! empty($vendor->logo)) {
                $this->imageManger->deleteImage($vendor->logo);
            }

            $data['logo'] = $this->imageManger->uploadImage('/uploads/vendors', $data['logo']);
        }

        if (isset($data['banner'])) {
            if (! empty($vendor->banner)) {
                $this->imageManger->deleteImage($vendor->banner);
            }

            $data['banner'] = $this->imageManger->uploadImage('/uploads/vendors', $data['banner']);
        }

        return $this->repository->update($vendor, $data);
    }
}
