<?php

namespace App\Services;

use App\Models\GeoPoint;

readonly class DistanceService
{
    public function __construct(
        private HaversineService $haversine,
    ) {}

    public function calculate(GeoPoint $point, ?GeoPoint $prevPoint): float
    {
        if ($prevPoint === null) {
            return 0;
        }

        return round($this->haversine->calc($prevPoint->lon, $prevPoint->lat, $point->lon, $point->lat), 2);
    }
}
