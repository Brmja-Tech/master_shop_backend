<?php

namespace App\Http\Controllers\Api\Vendor\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\Auth\RegisterRequest;
use App\Http\Requests\Vendor\Auth\LoginRequest;
use App\Services\Api\Auth\VendorAuthService;
use Illuminate\Http\Request;

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

        // إخطار المشرفين ذوي الصلاحية بتسجيل المتجر الجديد
        try {
            $admins = \App\Models\Admin::all()->filter(function ($admin) {
                return $admin->hasAccess('vendors');
            });
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\Admin\NewVendorRegistrationNotification($vendor));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('فشل إرسال إشعار تسجيل متجر للمشرفين: ' . $e->getMessage());
        }

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

    public function logout(Request $request)
    {
        $response = $this->vendorAuthService->logout();

        return ApiResponse::sendResponse(
            $response['status'],
            $response['message'],
            $response['data']
        );
    }
}
