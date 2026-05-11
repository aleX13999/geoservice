<?php

namespace App\Services;

use App\Models\GeoPoint;
use App\Repositories\GeoPointRepository;
use Illuminate\Support\Facades\DB;

readonly class PointHandleService
{
    public function __construct(
        private GeoPointRepository $geoPointRepository,
        private TrackService       $trackService,
        private ZoneService        $zoneService,
        private DistanceService    $distanceService,
        private SegmentService     $segmentService,
    ) {}

    public function handle(): void
    {
        DB::disableQueryLog();

        $prevPoint    = null;
        $prevTs       = null;
        $currentTrack = null;
        $currentDev   = null;

        $updatePoints = [];

        $rawPoints = $this->geoPointRepository->getRawPoints();
        /** @var GeoPoint $point */
        foreach ($rawPoints as $point) {
            if ($currentDev !== null && $point->device_id !== $currentDev) {
                $this->save($updatePoints);
                $prevPoint    = null;
                $prevTs       = null;
                $currentTrack = null;
                $updatePoints = [];
            }

            $currentDev = $point->device_id;

            if ((float)$point->lat === 0.0 && (float)$point->lon === 0.0) {
                $prevPoint = null;
                $prevTs    = null;
                continue;
            }

            $trackId  = $this->trackService->calculate($point, $prevTs, $currentTrack);
            $zoneId   = $this->zoneService->findZone($point->lon, $point->lat);
            $distance = $this->distanceService->calculate($point, $prevPoint);

            $prevPoint    = $point;
            $prevTs       = $point->ts_device;
            $currentTrack = $trackId;

            $updatePoints[$point->id] = [
                'id'        => $point->id,
                'device_id' => $point->device_id,
                'track_id'  => $trackId,
                'zone_id'   => $zoneId,
                'ts_device' => $point->ts_device,
                'dt_device' => $point->dt_device,
                'dt_gps'    => $point->dt_gps,
                'lat'       => $point->lat,
                'lon'       => $point->lon,
                'speed'     => $point->speed,
                'alt'       => $point->alt,
                'distance'  => $distance,
                'bearing'   => $point->bearing,
            ];

            if (count($updatePoints) >= 500) {
                $this->save($updatePoints);
                $updatePoints = [];
            }
        }

        $this->save($updatePoints);

        if ($currentDev !== null) {
            $this->segmentService->generateForDevice($currentDev);
        }

        $this->zoneService->clearCache();
    }

    private function save(array $updatePoints): void
    {
        GeoPoint::upsert(
            $updatePoints,
            ['id'],
            ['track_id', 'zone_id', 'distance'],
        );
    }
}
