<?php

namespace App\Repositories\Dashboard\Auth;

use App\Models\Admin;
use Fisal\Otp\Otp;

class ForgotRepository
{
    /**
     * Create a new class instance.
     */
    protected $otp2;
    public function __construct()
    {
        $this->otp2 = new Otp();
    } // End constructor

    public function getAdminByEmail($email)
    {
        $admin = Admin::where('email', $email)->first();
        return $admin;
    } // End sendOTP method

    public function verifyOtp($data)
    {
        $otp = $this->otp2->validate($data['email'], $data['token']);
        return $otp;
    } // End verifyOtp method
}
