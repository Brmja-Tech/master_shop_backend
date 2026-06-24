<?php

namespace App\Http\Controllers\Api\Delivery\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Delivery\Auth\LoginRequest;
use App\Http\Requests\Delivery\Auth\RegisterRequest;
use App\Http\Resources\Api\Delivery\Auth\DeliveryUserResource;
use App\Services\Api\Auth\DeliveryAuthService;
use App\Notifications\Admin\NewDeliveryRegistrationNotification;
use App\Models\Admin;
use Illuminate\Http\Request;

class DeliveryAuthController extends Controller
{
    public function __construct(
        protected DeliveryAuthService $deliveryAuthService
    ) {}

    public function delivery_regist(RegisterRequest $request)
    {
        $deliveryUser = $this->deliveryAuthService->register($request->validated());

        // إخطار المشرفين ذوي الصلاحية بتسجيل المندوب الجديد
        try {
            $admins = Admin::all()->filter(function ($admin) {
                return $admin->hasAccess('deliveries');
            });
            foreach ($admins as $admin) {
                $admin->notify(new NewDeliveryRegistrationNotification($deliveryUser));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('فشل إرسال إشعار تسجيل مندوب للمشرفين: ' . $e->getMessage());
        }

        return ApiResponse::sendResponse(
            201,
            __('delivery.register_success'),
            [
                'delivery_user' => new DeliveryUserResource($deliveryUser),
            ]
        );
    }

    public function login(LoginRequest $request)
    {
        $response = $this->deliveryAuthService->login(
            $request->only(['phone', 'password', 'fcm_token'])
        );

        return ApiResponse::sendResponse(
            $response['status'],
            $response['message'],
            $response['data']
        );
    }

    public function logout(Request $request)
    {
        $response = $this->deliveryAuthService->logout();

        return ApiResponse::sendResponse(
            $response['status'],
            $response['message'],
            $response['data']
        );
    }
}
