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
}
