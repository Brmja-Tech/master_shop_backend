<?php

namespace App\Repositories\Api\Vendor\Auth;

use App\Models\Vendor;
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

    public function getVendorByPhone($phone)
    {
        return Vendor::where('phone', $phone)->first();
    }

    public function sendOTP($phone)
    {
        $vendor = $this->getVendorByPhone($phone);

        if (!$vendor) {
            return [
                'status'  => 422,
                'message' => __('front.user-not-found'),
                'data'    => []
            ];
        }

        // Generate OTP
        $otp = $this->otp->generate($vendor->phone, 'numeric', 5, 20);

        // Send OTP Notification
        $vendor->notify(new SendOtpNotify());

        return [
            'status'  => 200,
            'message' => 'OTP sent successfully',
            'data'    => [
                'phone' => $vendor->phone,
            ]
        ];
    }

    public function verifyOtp($data)
    {
        $vendor = $this->getVendorByPhone($data['phone']);

        if (!$vendor) {
            return [
                'status'  => 422,
                'message' => __('front.user-not-found'),
                'data'    => []
            ];
        }

        $otp = $this->otp->validate(
            $vendor->phone,
            $data['code']
        );

        if (!$otp->status) {
            return [
                'status'  => 422,
                'message' => 'Invalid OTP',
                'data'    => []
            ];
        }

        return [
            'status'  => 200,
            'message' => 'OTP verified successfully',
            'data'    => []
        ];
    }

    public function resetPassword($data)
    {
        $vendor = $this->getVendorByPhone($data['phone']);

        if (!$vendor) {
            return [
                'status'  => 422,
                'message' => __('front.user-not-found'),
                'data'    => []
            ];
        }

        $vendor->update([
            'password' => Hash::make($data['password'])
        ]);

        return [
            'status'  => 200,
            'message' => 'Password reset successfully',
            'data'    => []
        ];
    }

    public function resendOtp($phone)
    {
        return $this->sendOTP($phone);
    }
    
}
