<?php

namespace App\Services\Api\User;

use App\Helpers\ApiResponse;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorRate;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class VendorRateService
{
    /**
     * Submit a new rating for a vendor.
     */
    public function store(User $user, int $vendorId, float $rateValue): array
    {
        $vendor = $this->findActiveVendor($vendorId);

        if (VendorRate::where('user_id', $user->id)->where('vendor_id', $vendorId)->exists()) {
            throw new HttpResponseException(
                ApiResponse::sendResponse(422, __('vendor.already_rated'), [])
            );
        }

        $vendorRate = DB::transaction(function () use ($user, $vendor, $rateValue) {
            $rate = VendorRate::create([
                'user_id' => $user->id,
                'vendor_id' => $vendor->id,
                'rate' => $rateValue,
            ]);

            $vendor->updateAverageRating();

            return $rate;
        });

        return [
            'message' => __('vendor.rate_submitted'),
            'rate' => $vendorRate,
        ];
    }

    /**
     * Update an existing rating for a vendor.
     */
    public function update(User $user, int $vendorId, float $rateValue): array
    {
        $vendor = $this->findActiveVendor($vendorId);

        $existingRate = VendorRate::where('user_id', $user->id)
            ->where('vendor_id', $vendorId)
            ->first();

        if (!$existingRate) {
            throw new HttpResponseException(
                ApiResponse::sendResponse(404, __('vendor.rate_not_found'), [])
            );
        }

        $vendorRate = DB::transaction(function () use ($vendor, $existingRate, $rateValue) {
            $existingRate->update(['rate' => $rateValue]);

            $vendor->updateAverageRating();

            return $existingRate;
        });

        return [
            'message' => __('vendor.rate_updated'),
            'rate' => $vendorRate,
        ];
    }

    /**
     * Find active and verified vendor.
     */
    private function findActiveVendor(int $vendorId): Vendor
    {
        return Vendor::query()
            ->where('is_active', true)
            ->where('is_verified', true)
            ->findOrFail($vendorId);
    }
}
