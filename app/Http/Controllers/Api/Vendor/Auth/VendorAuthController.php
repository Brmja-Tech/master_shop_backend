<?php

namespace App\Http\Controllers\Api\Vendor\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\Auth\RegisterRequest;
use App\Http\Requests\Vendor\Auth\LoginRequest;
use App\Services\Api\Auth\VendorAuthService;

class VendorAuthController extends Controller
{
    public function __construct(
        protected VendorAuthService $vendorAuthService
    ) {}

    public function register(RegisterRequest $request)
    {
        $vendor = $this->vendorAuthService->register(
            $request->validated()
        );

        return ApiResponse::sendResponse(
            201,
            __('vendor.register_success'),
            [
                'vendor' => $vendor,
            ]
        );
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['phone', 'password', 'fcm_token']);

        $response = $this->vendorAuthService->login(
            $credentials
        );

        return ApiResponse::sendResponse(
            $response['status'],
            $response['message'],
            $response['data']
        );
    }
}
