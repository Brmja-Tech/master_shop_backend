<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\ProfileUpdateRequest;
use App\Http\Resources\Api\Vendor\Auth\VendorResource;
use App\Services\Api\Vendor\VendorProfileService;

class ProfileController extends Controller
{
    public function __construct(
        protected VendorProfileService $service
    ) {}

    public function show()
    {
        return ApiResponse::sendResponse(
            200,
            __('vendor.profile_retrieved'),
            new VendorResource($this->service->show(auth('sanctum')->user()))
        );
    }

    public function update(ProfileUpdateRequest $request)
    {
        return ApiResponse::sendResponse(
            200,
            __('vendor.profile_updated'),
            new VendorResource($this->service->update(auth('sanctum')->user(), $request->validated()))
        );
    }
}
