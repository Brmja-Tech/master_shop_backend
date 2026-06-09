<?php

namespace App\Repositories\Dashboard\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthRepository
{
    public function register($credentials)
    {
        $user = User::create([
            'name'     => $credentials['name'],
            'email'    => $credentials['email'],
            'password' => Hash::make($credentials['password']),
        ]);
        return $user;
    } // End register Method

    public function login($credentials, $guard, $remember = false)
    {
        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            $user->currentAccessToken()?->delete();
        }
        if (Auth::guard($guard)->attempt($credentials, $remember)) {
            $user = Auth::guard($guard)->user();
            if ($guard === 'web') {
                $token = $user->createToken('auth_token')->plainTextToken;
                return [
                    'user' => $user,
                    'token' => $token,
                ];
            }
            return [
                'user' => $user,
            ];
        }
        return false;
    } // End login Method

    public function logout($guard = null)
    {
        return  Auth::guard($guard)->logout();
    } // End logout Method




}
