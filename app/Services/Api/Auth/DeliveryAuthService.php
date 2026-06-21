<?php

namespace App\Services\Api\Auth;

use App\Http\Resources\Api\Delivery\Auth\DeliveryUserResource;
use App\Models\DeliveryUser;
use App\Repositories\Api\Auth\DeliveryAuthRepository;
use App\Utils\ImageManger;
use Illuminate\Support\Facades\Hash;

class DeliveryAuthService
{
    public function __construct(
        protected DeliveryAuthRepository $repository,
        protected ImageManger $imageManger
    ) {}

    public function register(array $data): DeliveryUser
    {
        $data['password'] = Hash::make($data['password']);
        $data['approval_status'] = 'pending';
        $data['active_status'] = false;
        $data['ban'] = false;

        foreach (['img', 'front_ident', 'back_ident', 'personal_deriving_license', 'machine_license'] as $field) {
            if (isset($data[$field]) && $data[$field] !== null) {
                $data[$field] = $this->imageManger->uploadImage('delivaries', $data[$field]);
            }
        }

        return $this->repository->create($data);
    }

    public function login(array $data): array
    {
        $deliveryUser = $this->repository->findByPhone($data['phone']);

        if (! $deliveryUser || ! Hash::check($data['password'], $deliveryUser->password)) {
            return [
                'status' => 401,
                'message' => __('delivery.invalid_credentials'),
                'data' => [],
            ];
        }

        if ($deliveryUser->ban) {
            return [
                'status' => 403,
                'message' => __('delivery.account_banned'),
                'data' => [],
            ];
        }

        if ($deliveryUser->approval_status !== 'approved') {
            return [
                'status' => 403,
                'message' => __('delivery.not_approved'),
                'data' => [
                    'approval_status' => $deliveryUser->approval_status,
                ],
            ];
        }

        if (! empty($data['fcm_token'])) {
            $this->repository->updateFcmToken($deliveryUser, $data['fcm_token']);
            $deliveryUser->refresh();
        }

        $token = $deliveryUser->createToken('delivery-token')->plainTextToken;

        return [
            'status' => 200,
            'message' => __('delivery.login_successfully'),
            'data' => [
                'delivery_user' => new DeliveryUserResource($deliveryUser),
                'token' => $token,
            ],
        ];
    }

    public function logout(): array
    {
        return $this->repository->logout();
    }
}
