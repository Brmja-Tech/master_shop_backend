<?php

namespace App\Services\Api\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;
use Kreait\Firebase\Factory;
use App\Http\Resources\UserResource;

class FirebaseAuthService
{
    protected function auth()
    {
        $credentialsPath = config('services.firebase.credentials');

        return (new Factory)
            ->withServiceAccount($credentialsPath)
            ->createAuth();
    }

    public function loginWithFirebase(string $idToken, ?string $deviceName = null, ?string $fcmToken = null)
    {
        try {
            $auth = $this->auth();
            $verifiedToken = $auth->verifyIdToken($idToken);

            $claims = $verifiedToken->claims();

            $uid   = (string) $claims->get('sub');
            $email = $claims->has('email') ? (string) $claims->get('email') : null;
            $name  = $claims->has('name') ? (string) $claims->get('name') : null;
            $pic   = $claims->has('picture') ? (string) $claims->get('picture') : null;

            $firebase = $claims->has('firebase') ? (array) $claims->get('firebase') : [];
            $provider = $firebase['sign_in_provider'] ?? null;

            $emailVerified = $claims->has('email_verified') ? (bool) $claims->get('email_verified') : false;

            $user = User::where('firebase_uid', $uid)->first();

            if (! $user && $email) {
                $user = User::where('email', $email)->first();
            }

            if (! $user) {
                if (! $email) {
                    return ApiResponse::sendResponse(422 , 'front.invalid-credentials', []);
                }

                $user = User::create([
                    'name'                  => $name ?: 'User',
                    'email'                 => $email,
                    'password'              => Str::random(32),
                    'firebase_uid'          => $uid,
                    'auth_provider'         => $provider,
                    'image'                 => $pic,
                    'email_verified_at'     => now(),
                    'fcm_token'             => $fcmToken,
                ]);
            } else {
                $dirty = false;

                if (! $user->firebase_uid) {
                    $user->firebase_uid = $uid;
                    $dirty = true;
                }

                if ($provider && $user->auth_provider !== $provider) {
                    $user->auth_provider = $provider;
                    $dirty = true;
                }

                if ($pic && $user->image !== $pic) {
                    $user->image = $pic;
                    $dirty = true;
                }

                if ($emailVerified && is_null($user->email_verified_at)) {
                    $user->email_verified_at = now();
                    $dirty = true;
                }

                if ($name && $user->name !== $name) {
                    $user->name = $name;
                    $dirty = true;
                }

                if ($dirty) $user->save();
            }

            $tokenName = $deviceName ?: 'mobile';
            $token = $user->createToken($tokenName)->plainTextToken;

            return ApiResponse::sendResponse(200 , 'front.login-success', [
                'user'  => new UserResource($user),
                'token' => $token,
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::sendResponse(401 , 'front.invalid-credentials', []);
        }
    }
}
