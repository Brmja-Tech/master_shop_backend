<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Delivery\ProfileUpdateRequest;
use App\Http\Requests\Delivery\UpdateLocationRequest;
use App\Http\Resources\Api\Delivery\Auth\DeliveryUserResource;
use App\Services\Api\Delivery\DeliveryProfileService;

class ProfileController extends Controller
{
    public function __construct(
        protected DeliveryProfileService $service
    ) {}

    public function show()
    {
        $deliveryUser = auth('sanctum')->user();

        return ApiResponse::sendResponse(
            200,
            __('delivery.profile_retrieved'),
            new DeliveryUserResource($this->service->show($deliveryUser))
        );
    }

    public function update(ProfileUpdateRequest $request)
    {
        $deliveryUser = auth('sanctum')->user();
        $updatedUser = $this->service->update($deliveryUser, $request->validated());

        return ApiResponse::sendResponse(
            200,
            __('delivery.profile_updated'),
            new DeliveryUserResource($updatedUser)
        );
    }

    public function updateLocation(UpdateLocationRequest $request)
    {
        $deliveryUser = auth('sanctum')->user();
        $updatedUser = $this->service->updateLocation($deliveryUser, $request->validated());

        return ApiResponse::sendResponse(
            200,
            __('delivery.profile_updated'),
            new DeliveryUserResource($updatedUser)
        );
    }
}
