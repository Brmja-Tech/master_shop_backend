<?php

namespace App\Http\Controllers\Api\Vendor\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Api\Vendor\Auth\ForgotService;
use App\Helpers\ApiResponse;
use Illuminate\Validation\Rule;

class ForgotController extends Controller
{
    protected $forgotService;

    public function __construct(ForgotService $forgotService)
    {
        $this->forgotService = $forgotService;
    }

    public function forgotPassword(Request $request)
    {
        $request->merge([
            'phone' => trim((string) $request->input('phone')),
        ]);

        $data = $request->validate([
            'phone' => [
                'required',
                'string',
                Rule::exists('vendors', 'phone')->whereNull('deleted_at'),
            ],
        ]);

        $response = $this->forgotService->sendOTP($data['phone']);

        return ApiResponse::sendResponse(
            $response['status'],
            $response['message'],
            $response['data']
        );
    }

    public function verifyOtp(Request $request)
    {
        $request->merge([
            'phone' => trim((string) $request->input('phone')),
            'code'  => trim((string) $request->input('code')),
        ]);

        $data = $request->validate([
            'phone' => [
                'required',
                'string',
                Rule::exists('vendors', 'phone')->whereNull('deleted_at'),
            ],
            'code' => [
                'required',
                'string',
            ],
        ]);

        $response = $this->forgotService->verifyOtp($data);

        return ApiResponse::sendResponse(
            $response['status'],
            $response['message'],
            $response['data']
        );
    }

    public function resetPassword(Request $request)
    {
        $request->merge([
            'phone' => trim((string) $request->input('phone')),
        ]);

        $data = $request->validate([
            'phone' => [
                'required',
                'string',
                Rule::exists('vendors', 'phone')->whereNull('deleted_at'),
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $response = $this->forgotService->resetPassword($data);

        return ApiResponse::sendResponse(
            $response['status'],
            $response['message'],
            $response['data']
        );
    }

    public function resendOtp(Request $request)
    {
        $request->merge([
            'phone' => trim((string) $request->input('phone')),
        ]);

        $data = $request->validate([
            'phone' => [
                'required',
                'string',
                Rule::exists('vendors', 'phone')->whereNull('deleted_at'),
            ],
        ]);

        $response = $this->forgotService->resendOtp($data['phone']);

        return ApiResponse::sendResponse(
            $response['status'],
            $response['message'],
            $response['data']
        );
    }
}
