<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FirebaseLoginRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\User\UpdateLocationRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Api\Auth\AuthService;
use App\Services\Api\Auth\FirebaseAuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $credentials = $request->validated();
        $response = $this->authService->register($credentials);

        if (!$response) {
            return ApiResponse::sendResponse(422, __('front.user-registration-failed'), []);
        }

        return ApiResponse::sendResponse($response['status'], $response['message'], $response['data']);
    }




    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'phone'         => 'required|string|exists:users,phone',
            'code'          => 'required|string',
            'fcm_token'     => 'nullable|string',
        ]);

        $response = $this->authService->verifyOtp($data);
        return ApiResponse::sendResponse($response['status'], $response['message'], $response['data']);
    }




    public function login(LoginRequest $request)
    {
        $credenshais = $request->only('phone', 'password', 'fcm_token');
        $response = $this->authService->login($credenshais, 'web');
        return ApiResponse::sendResponse($response['status'], $response['message'], $response['data']);
    }




    public function resendOtp(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string|exists:users,phone',
        ]);
        $response = $this->authService->resendOtp($data);
        return ApiResponse::sendResponse($response['status'], $response['message'], $response['data']);
    }




    public function logout(Request $request)
    {
        $response = $this->authService->logout();
        return ApiResponse::sendResponse($response['status'], $response['message'], $response['data']);
    }


    public function firebaseLogin(FirebaseLoginRequest $request, FirebaseAuthService $service)
    {
        return $service->loginWithFirebase(
            $request->input('id_token'),
            $request->input('device_name'),
            $request->input('fcm_token')
        );
    }

    public function updateLocation(UpdateLocationRequest $request)
    {
        $response = $this->authService->updateLocation(
            auth('sanctum')->user(),
            $request->validated()
        );

        return ApiResponse::sendResponse($response['status'], $response['message'], $response['data']);
    }

    public function profile()
    {
        $response = $this->authService->profile(auth('sanctum')->user());

        return ApiResponse::sendResponse($response['status'], $response['message'], $response['data']);
    }
}
