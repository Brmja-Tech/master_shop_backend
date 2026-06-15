<?php

namespace App\Services\Api\Auth;

use App\Models\User;
use App\Utils\ImageManger;
use App\Notifications\SendOtpNotify;
use App\Repositories\Api\Auth\AuthRepository;
use Fisal\Otp\Otp;

class AuthService
{
    protected $authRepository, $imageManager;


    public function __construct(AuthRepository $authRepository, ImageManger $imageManager)
    {
        $this->imageManager = $imageManager;
        $this->authRepository = $authRepository;
    } //End constructor Method





    public function register($credentials)
    {
        $response = $this->authRepository->register($credentials);

        if (!$response) {
            return false;
        }

        // Generate and send the OTP only after registration succeeds بالكامل.
        $user = User::find($response['data']['user']->id ?? null);
        if ($user) {
            $otp = (new Otp())->generate($user->phone, 'numeric', 5, 20);
            $user->notify(new SendOtpNotify($otp->token));
        }

        return $response;
    } //End register Method




    public function verifyOtp($data)
    {
        return $this->authRepository->verifyOtp($data);
    } //End verifyOtp Method


    public function login($credenshais, $guard, $remember = false)
    {
        return $this->authRepository->login($credenshais, $guard, $remember);
    } //End login Method



    public function resendOtp($data)
    {
        return $this->authRepository->resendOtp($data);
    } //End resendOtp Method



    public function logout($guard = null)
    {
        return $this->authRepository->logout($guard);
    } //End logout Method

    public function updateLocation(User $user, array $data)
    {
        return $this->authRepository->updateLocation($user, $data);
    } //End updateLocation Method

    public function profile(User $user)
    {
        return $this->authRepository->profile($user);
    } //End profile Method
}
