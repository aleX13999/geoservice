<?php

namespace App\Services;

readonly class PointService
{
    public function __construct(
        public HaversineService $haversine,
    ) {}

    public function isInPolygon(float $lat, float $lon, array $poly): bool
    {
        $inside = false;
        $count = count($poly);

        if ($count < 3) {
            return false;
        }

        for ($i = 0, $j = $count - 1; $i < $count; $i++) {
            $xi = $poly[$i]['lon'];
            $yi = $poly[$i]['lat'];

            $xj = $poly[$j]['lat'];
            $yj = $poly[$j]['lat'];

            $intersect = (($yi > $lat) != ($yj > $lat))
                && ($lon < ($xj - $xi) * ($lat - $yi) / ($yj - $yi + 0.0000001) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }

            $j = $i;
        }

        return $inside;
    }

    public function isInCircle(float $pointLat, float $pointLon,   float $centerLat, float $centerLon, float $radius): bool
    {
        return $this->haversine->calc($pointLat, $pointLon, $centerLat, $centerLon) <= $radius;
    }
}
