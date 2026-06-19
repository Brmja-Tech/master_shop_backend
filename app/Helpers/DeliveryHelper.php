<?php

namespace App\Helpers;

use App\Models\DeliverySetting;
use App\Helpers\LocationHelper;

class DeliveryHelper
{
    /**
     * Calculate delivery fee based on vendor coordinates, delivery coordinates, and settings.
     *
     * @param float|null $vendorLat
     * @param float|null $vendorLon
     * @param float|null $deliveryLat
     * @param float|null $deliveryLon
     * @return array [delivery_fee, distance_km]
     * @throws \Exception
     */
    public static function calculateFee(?float $vendorLat, ?float $vendorLon, ?float $deliveryLat, ?float $deliveryLon): array
    {
        if ($deliveryLat === null || $deliveryLon === null || $vendorLat === null || $vendorLon === null) {
            throw new \Exception(__('validation.accurate_location_required'));
        }

        $distanceInMeters = LocationHelper::calculateDistanceInMeters(
            $vendorLat,
            $vendorLon,
            $deliveryLat,
            $deliveryLon
        );

        if ($distanceInMeters === null) {
            throw new \Exception(__('validation.accurate_location_required'));
        }

        $distanceInKm = round($distanceInMeters / 1000, 2);

        // Retrieve delivery settings
        $settings = DeliverySetting::first();
        $pricePerKm = $settings ? (float) $settings->price_per_km : 0.00;
        $minDeliveryFee = $settings ? (float) $settings->min_delivery_fee : 0.00;

        $deliveryFee = round($distanceInKm * $pricePerKm, 2);
        if ($deliveryFee < $minDeliveryFee) {
            $deliveryFee = $minDeliveryFee;
        }

        return [
            'delivery_fee' => $deliveryFee,
            'distance_km' => $distanceInKm,
        ];
    }
}
