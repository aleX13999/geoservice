<?php

namespace App\Services;

use App\Models\GeoPoint;

readonly class DistanceService
{
    public function __construct(
        private HaversineService $haversine,
    ) {}

    public function calc(): void
    {
        $deviceIds = GeoPoint::select('device_id')->distinct()->pluck('device_id');

        foreach ($deviceIds as $deviceId) {
            $points = GeoPoint::where('device_id', $deviceId)
                              ->orderBy('ts_device')
                              ->cursor();

            $prevPoint = null;
            $updates = [];

            foreach ($points as $point) {
                if ($point->lat == 0 && $point->lon == 0) {
                    $prevPoint = null;
                    continue;
                }

                $dist = 0;
                if ($prevPoint) {
                    $dist =  $this->haversine->calc($prevPoint->lat, $prevPoint->lon, $point->lat, $point->lon);
                }

                $updates[] = [
                    'id' => $point->id,
                    'distance' => round($dist, 2)
                ];

                $prevPoint = $point;

                if (count($updates) >= 500) {
                    $this->bulkUpdateDistance($updates);
                    $updates = [];
                }
            }

            if (!empty($updates)) {
                $this->bulkUpdateDistance($updates);
            }
        }
    }

    private function bulkUpdateDistance(array $updates) {}
}
