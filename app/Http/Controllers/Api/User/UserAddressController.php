<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\UserAddressStoreRequest;
use App\Http\Requests\Api\User\UserAddressUpdateRequest;
use App\Http\Resources\Api\User\UserAddressResource;
use App\Models\UserAddress;

class UserAddressController extends Controller
{
    public function index()
    {
        $addresses = auth('sanctum')->user()->addresses()->latest()->get();

        return ApiResponse::sendResponse(
            200,
            __('front.addresses-retrieved-successfully'),
            UserAddressResource::collection($addresses)
        );
    }

    public function store(UserAddressStoreRequest $request)
    {
        $user = auth('sanctum')->user();
        $data = $request->validated();

        if (($data['is_default'] ?? false) || ! $user->addresses()->exists()) {
            $user->addresses()->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        $address = $user->addresses()->create($data);

        return ApiResponse::sendResponse(
            201,
            __('front.user-address-add-successfully'),
            UserAddressResource::make($address)
        );
    }

    public function show(UserAddress $address)
    {
        abort_if($address->user_id !== auth('sanctum')->id(), 403);

        return ApiResponse::sendResponse(
            200,
            __('front.address-retrieved-successfully'),
            UserAddressResource::make($address)
        );
    }

    public function update(UserAddressUpdateRequest $request, UserAddress $address)
    {
        $user = auth('sanctum')->user();

        abort_if($address->user_id !== $user->id, 403);

        $data = $request->validated();

        if (($data['is_default'] ?? false) === true) {
            $user->addresses()->whereKeyNot($address->id)->update(['is_default' => false]);
        }

        $address->update($data);

        return ApiResponse::sendResponse(
            200,
            __('front.user-address-update-successfully'),
            UserAddressResource::make($address->fresh())
        );
    }

    public function destroy(UserAddress $address)
    {
        $user = auth('sanctum')->user();

        abort_if($address->user_id !== $user->id, 403);

        $wasDefault = $address->is_default;
        $address->delete();

        return ApiResponse::sendResponse(
            200,
            __('front.user-address-delete-successfully'),
            []
        );
    }
}
