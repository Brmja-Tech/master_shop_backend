<?php

namespace App\Helpers;

class LocationHelper
{
    /**
     * Calculate the distance between two geographical points in meters using the Haversine formula.
     *
     * @param float|null $lat1
     * @param float|null $lon1
     * @param float|null $lat2
     * @param float|null $lon2
     * @return float|null Distance in meters, or null if any coordinate is missing
     */
    public static function calculateDistanceInMeters(?float $lat1, ?float $lon1, ?float $lat2, ?float $lon2): ?float
    {
        if ($lat1 === null || $lon1 === null || $lat2 === null || $lon2 === null) {
            return null;
        }

        $earthRadius = 6371000; // Radius of earth in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
