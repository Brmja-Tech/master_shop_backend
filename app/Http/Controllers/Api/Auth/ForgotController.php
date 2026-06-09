<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Api\Auth\ForgotService;
use App\Helpers\ApiResponse;

class ForgotController extends Controller
{
    protected $forgotService;

    public function __construct(ForgotService $forgotService)
    {
        $this->forgotService = $forgotService;
    }

    public function forgotPassword(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string|exists:users,phone',
        ]);

        $response = $this->forgotService->sendOTP($data['phone']);
        return ApiResponse::sendResponse($response['status'], $response['message'], $response['data']);
    }

    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string|exists:users,phone',
            'code'  => 'required|string',
        ]);

        $response = $this->forgotService->verifyOtp($data);
        return ApiResponse::sendResponse($response['status'], $response['message'], $response['data']);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'phone'    => 'required|string|exists:users,phone',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $response = $this->forgotService->resetPassword($data);
        return ApiResponse::sendResponse($response['status'], $response['message'], $response['data']);
    }

    public function resendOtp(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string|exists:users,phone',
        ]);

        $response = $this->forgotService->resendOtp($data['phone']);
        return ApiResponse::sendResponse($response['status'], $response['message'], $response['data']);
    }
}
