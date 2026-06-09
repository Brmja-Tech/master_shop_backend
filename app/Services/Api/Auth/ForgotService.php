<?php

namespace App\Services\Api\Auth;

use App\Repositories\Api\Auth\ForgotRepository;

class ForgotService
{
    protected $forgotRepository;

    public function __construct(ForgotRepository $forgotRepository)
    {
        $this->forgotRepository = $forgotRepository;
    }

    public function sendOTP($phone)
    {
        return $this->forgotRepository->sendOTP($phone);
    }

    public function verifyOtp($data)
    {
        return $this->forgotRepository->verifyOtp($data);
    }

    public function resetPassword($data)
    {
        return $this->forgotRepository->resetPassword($data);
    }

    public function resendOtp($phone)
    {
        return $this->forgotRepository->resendOtp($phone);
    }
}
