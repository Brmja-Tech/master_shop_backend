<?php

namespace App\Services\Dashboard\Auth;

use App\Repositories\Dashboard\Auth\ForgotRepository;
use App\Notifications\SendOtpNotify;

class ForgotService
{
    /**
     * Create a new class instance.
     */
    protected $forgotRepository;
    public function __construct(ForgotRepository $forgotRepository)
    {
        $this->forgotRepository = $forgotRepository;
    } // End __construct

    public function sendOTP($email)
    {
        $admin = $this->forgotRepository->getAdminByEmail($email);
        if (!$admin) {
            return false;
        }
        $admin->notify(new SendOtpNotify());
        return $admin;
    } //End sendOTP method


    public function verifyOtp($data)
    {
        $otp = $this->forgotRepository->verifyOtp($data);
        return $otp->status;
    } // End verifyOtp method
}
