<?php

namespace App\Repositories\Api\Vendor;

use App\Models\Vendor;

class VendorProfileRepository
{
    public function update(Vendor $vendor, array $data): Vendor
    {
        $vendor->update($data);

        return $vendor->fresh('storeType');
    }
}
