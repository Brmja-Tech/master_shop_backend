<?php

namespace App\Repositories\Api\Auth;

use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;

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

    public function logout(): array
    {
        $vendor = Auth::guard('sanctum')->user();

        if ($vendor) {
            $vendor->currentAccessToken()?->delete();

            return [
                'status'  => 200,
                'message' => __('vendor.logout-successfully'),
                'data'    => [],
            ];
        }

        return [
            'status'  => 422,
            'message' => __('vendor.logout-failed'),
            'data'    => [],
        ];
    }
}
