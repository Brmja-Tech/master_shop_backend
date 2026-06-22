<?php

namespace App\Repositories\Api\Delivery;

use App\Models\DeliveryUser;

class DeliveryProfileRepository
{
    public function update(DeliveryUser $deliveryUser, array $data): DeliveryUser
    {
        $deliveryUser->update($data);

        return $deliveryUser->fresh();
    }

    public function updateLocation(DeliveryUser $deliveryUser, float $lat, float $lng): DeliveryUser
    {
        $deliveryUser->update([
            'lat' => $lat,
            'lng' => $lng,
        ]);

        return $deliveryUser->fresh();
    }
}
