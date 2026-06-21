<?php

namespace App\Services\Api\Delivery;

use App\Models\DeliveryUser;
use App\Repositories\Api\Delivery\DeliveryProfileRepository;
use App\Utils\ImageManger;
use Illuminate\Support\Facades\Hash;

class DeliveryProfileService
{
    public function __construct(
        protected DeliveryProfileRepository $repository,
        protected ImageManger $imageManger
    ) {}

    public function show(DeliveryUser $deliveryUser): DeliveryUser
    {
        return $deliveryUser;
    }

    public function update(DeliveryUser $deliveryUser, array $data): DeliveryUser
    {
        if (isset($data['password']) && ! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        foreach (['img', 'front_ident', 'back_ident', 'personal_deriving_license', 'machine_license'] as $field) {
            if (array_key_exists($field, $data)) {
                if ($data[$field] !== null) {
                    if (! empty($deliveryUser->$field)) {
                        $this->imageManger->deleteImage($deliveryUser->$field);
                    }
                    $data[$field] = $this->imageManger->uploadImage('delivaries', $data[$field]);
                } else {
                    // If explicitly set to null, we could delete it, but typically we keep the existing one if null, or delete.
                    // Since it is an image, let's only delete/replace if a new file is provided.
                    unset($data[$field]);
                }
            }
        }

        return $this->repository->update($deliveryUser, $data);
    }
}
