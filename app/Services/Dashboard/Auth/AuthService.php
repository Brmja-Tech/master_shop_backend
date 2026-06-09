<?php

namespace App\Services\Dashboard\Auth;

use App\Repositories\Dashboard\Auth\AuthRepository;

class AuthService
{
    protected $authRepository;
    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    } //End constructor Method


    public function register($credentials)
    {
        $user = $this->authRepository->register($credentials);
        if (!$user) {
            return false;
        }
        $user->tokens()->delete(); // Delete old tokens
        $token = $user->createToken('auth_token')->plainTextToken; // Create a new token for the user
        return $user ? [
            'token' => $token,
            'name'  => $user->name,
            'email' => $user->email,
        ] : false;
    } //End register Method


    public function login($credenshais, $guard, $remember = false)
    {
        return $this->authRepository->login($credenshais, $guard, $remember);
    } //End login Method

    public function logout($guard = null)
    {
        return $this->authRepository->logout($guard);
    } //End logout Method



}
