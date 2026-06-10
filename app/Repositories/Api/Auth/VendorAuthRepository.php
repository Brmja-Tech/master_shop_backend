<?php

namespace App\Repositories\Api\Auth;

use App\Models\Vendor;

class VendorAuthRepository
{
    public function create(array $data): Vendor
    {
        return Vendor::create($data);
    }

    public function findByPhone(string $phone): ?Vendor
    {
        return Vendor::where('phone', $phone)->first();
    }

    public function findByTempToken(string $token): ?Vendor
    {
        return Vendor::where('temp_token', $token)->first();
    }

    public function updateFcmToken(Vendor $vendor, string $fcmToken): void
    {
        $vendor->update([
            'fcm_token' => $fcmToken,
        ]);
    }
}
