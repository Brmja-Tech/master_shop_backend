<?php

namespace App\Repositories\Api\Auth;

use App\Http\Resources\UserResource;
use App\Models\User;
use Fisal\Otp\Otp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    protected $otp;

    public function __construct()
    {
        $this->otp = new Otp();
    } // End constructor

    public function register($credentials)
    {
        $user = User::create([
            'name'     => $credentials['name'],
            'phone'    => $credentials['phone'],
            'password' => Hash::make($credentials['password']),
        ]);

        if (!$user) {
            return false;
        }

        // Create authentication token
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Update fcm_token if provided
        if (!empty($credentials['fcm_token'])) {
            $user->update(['fcm_token' => $credentials['fcm_token']]);
        }

        return [
            'status'  => 201,
            'message' => __('front.user-registered-successfully'),
            'data'    => [
                'user'     => UserResource::make($user),
                'token'    => $token,
            ]
        ];
    } // End method register



    public function verifyOtp($data)
    {
        $user = User::where('phone', $data['phone'])->first();

        if (!$user) {
            return [
                'status'  => 422,
                'message' => __('front.user-not-found'),
                'data'    => []
            ];
        }

        // Verify OTP code
        $otp = $this->otp->validate($user->phone, $data['code']);
        if (!$otp->status) {
            return [
                'status'  => 422,
                'message' => __('front.invalid-otp'),
                'data'    => []
            ];
        }

        if (!empty($data['fcm_token'])) {
            $user->update(['fcm_token' => $data['fcm_token']]);
        }


        $user->update([
            'email_verified_at' => now()
        ]);

        $user->currentAccessToken()?->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'status'  => 200,
            'message' => __('front.otp-verified'),
            'data'    => [
                'user'  => UserResource::make($user),
                'token' => $token
            ]
        ];
    } // End verifyOtp Method



    public function login($credentials, $guard, $remember = false)
    {
        if (auth('sanctum')->check()) {
            return [
                'status'  => 403,
                'message' => __('front.already-logged-in'),
                'data'    => []
            ];
        }

        $user = User::where('phone', $credentials['phone'])->first();

        if (!$user) {
            return [
                'status'  => 422,
                'message' => __('front.user-not-found'),
                'data'    => []
            ];
        }


        if ($user->email_verified_at == null) {
            $otp = $this->otp->generate($user->phone, 'numeric', 5, 20);
            $user->notify(new SendOtpNotify());

            return [
                'status'  => 415,
                'message' => __('front.verify-account-first'),
                'data'    => [
                    'phone'    => $user->phone,
                    'otp_code' => $otp->token,
                ]
            ];
        }



        if (!Hash::check($credentials['password'], $user->password)) {
            return [
                'status'  => 422,
                'message' => __('front.invalid-credentials'),
                'data'    => []
            ];
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        if (!empty($credentials['fcm_token'])) {
            $user->update(['fcm_token' => $credentials['fcm_token']]);
        }

        return [
            'status'  => 200,
            'message' => __('front.login-success'),
            'data'    => [
                'user'  => UserResource::make($user),
                'token' => $token
            ]
        ];
    } // End login Method



    public function resendOtp($data)
    {
        $user = User::where('phone', $data['phone'])->first();

        if (!$user) {
            return [
                'status'  => 422,
                'message' => __('front.user-not-found'),
                'data'    => []
            ];
        }

        // Generate OTP code
        $otp = $this->otp->generate($user->phone, 'numeric', 5, 20);

        // Send new OTP
        $user->notify(new SendOtpNotify());

        return [
            'status'  => 200,
            'message' => __('front.otp-resent-successfully'),
            'data'    => [
                'phone'    => $user->phone,
                'otp_code' => $otp->token, // For testing only
            ]
        ];
    } // End resendOtp Method



    public function logout($guard = null)
    {
        $user = $guard ? Auth::guard($guard)->user() : Auth::user();

        if ($user) {
            $user->currentAccessToken()?->delete();
            return [
                'status'  => 200,
                'message' => __('front.logout-success'),
                'data'    => []
            ];
        }

        return [
            'status'  => 422,
            'message' => __('front.logout-failed'),
            'data'    => []
        ];
    } // End logout Method

    public function updateLocation(User $user, array $data): array
    {
        $user->update([
            'latitude' => (float) $data['latitude'],
            'longitude' => (float) $data['longitude'],
        ]);

        return [
            'status' => 200,
            'message' => __('front.location-updated-successfully'),
            'data' => [
                'user' => UserResource::make($user->fresh()),
            ],
        ];
    } // End updateLocation Method

    public function profile(User $user): array
    {
        return [
            'status' => 200,
            'message' => __('front.profile-retrieved-successfully'),
            'data' => [
                'user' => UserResource::make($user->fresh()),
            ],
        ];
    } // End profile Method

    public function updateProfile(User $user, array $data): array
    {
        $user->update([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'image' => $data['image'] ?? $user->image,
        ]);

        return [
            'status' => 200,
            'message' => __('front.profile-updated-successfully'),
            'data' => [
                'user' => UserResource::make($user->fresh()),
            ],
        ];
    } // End updateProfile Method
}
