<?php

namespace App\Repositories\Api\Auth;

use App\Models\DeliveryUser;
use Illuminate\Support\Facades\Auth;

class DeliveryAuthRepository
{
    public function create(array $data): DeliveryUser
    {
        return DeliveryUser::create($data);
    }

    public function findByPhone(string $phone): ?DeliveryUser
    {
        return DeliveryUser::where('phone', $phone)->first();
    }

    public function updateFcmToken(DeliveryUser $deliveryUser, string $fcmToken): void
    {
        $deliveryUser->update([
            'fcm_token' => $fcmToken,
        ]);
    }

    public function logout(): array
    {
        $deliveryUser = Auth::guard('sanctum')->user();

        if ($deliveryUser instanceof DeliveryUser) {
            $deliveryUser->currentAccessToken()?->delete();

            return [
                'status' => 200,
                'message' => __('delivery.logout_successfully'),
                'data' => [],
            ];
        }

        return [
            'status' => 422,
            'message' => __('delivery.logout_failed'),
            'data' => [],
        ];
    }
}
