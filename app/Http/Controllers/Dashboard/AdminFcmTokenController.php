<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminFcmTokenController extends Controller
{
    public function serviceWorker()
    {
        return response(
            view('dashboard.firebase-messaging-sw', [
                'firebaseWebConfig' => config('services.firebase.web', []),
            ])->render(),
            200,
            ['Content-Type' => 'application/javascript']
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'fcm_token' => ['required', 'string'],
        ]);

        $admin = $request->user('admin');

        $admin->update([
            'fcm_token' => $data['fcm_token'],
        ]);

        return response()->json([
            'status' => true,
            'message' => __('validation.successfully'),
        ]);
    }
}
