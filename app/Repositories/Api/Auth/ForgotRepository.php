<?php

namespace App\Repositories\Api\Auth;

use App\Models\User;
use App\Notifications\SendOtpNotify;
use Fisal\Otp\Otp;
use Illuminate\Support\Facades\Hash;

class ForgotRepository
{
    protected $otp;

    public function __construct()
    {
        $this->otp = new Otp();
    }

    public function getUserByPhone($phone)
    {
        return User::where('phone', $phone)->first();
    }

    public function sendOTP($phone)
    {
        $user = $this->getUserByPhone($phone);

        if (!$user) {
            return [
                'status'  => 422,
                'message' => __('front.user-not-found'),
                'data'    => []
            ];
        }

        // Generate OTP
        $otp = $this->otp->generate($user->phone, 'numeric', 5, 20);

        // Send OTP Notification
        $user->notify(new SendOtpNotify());

        return [
            'status'  => 200,
            'message' => __('front.otp-sent'),
            'data'    => [
                'phone'    => $user->phone,
            ]
        ];
    }

    public function verifyOtp($data)
    {
        $user = $this->getUserByPhone($data['phone']);

        if (!$user) {
            return [
                'status'  => 422,
                'message' => __('front.user-not-found'),
                'data'    => []
            ];
        }

        $otp = $this->otp->validate(
            $user->phone,
            $data['code']
        );

        if (!$otp->status) {
            return [
                'status'  => 422,
                'message' => __('front.invalid-otp'),
                'data'    => []
            ];
        }

        return [
            'status'  => 200,
            'message' => __('front.otp-verified-successfully'),
            'data'    => []
        ];
    }

    public function resetPassword($data)
    {
        $user = $this->getUserByPhone($data['phone']);

        if (!$user) {
            return [
                'status'  => 422,
                'message' => __('front.user-not-found'),
                'data'    => []
            ];
        }

        $user->update([
            'password' => Hash::make($data['password'])
        ]);

        return [
            'status'  => 200,
            'message' => __('front.password-reset-successful'),
            'data'    => []
        ];
    }

    public function resendOtp($phone)
    {
        return $this->sendOTP($phone);
    }
}
