<?php

namespace App\Services;

use App\Models\GeoPoint;
use App\Models\GeoSegment;
use App\Repositories\GeoPointRepository;

readonly class SegmentService
{
    public function __construct(
        public GeoPointRepository $geoPointRepository,
    ) {}

    public function generateForDevice(int $deviceId): void
    {
        $tracks = GeoPoint::where('device_id', $deviceId)
            ->whereNotNull('track_id')
            ->distinct()
            ->pluck('track_id');

        foreach ($tracks as $trackId) {
            $segments = $this->buildSegmentsForTrack($trackId);
            if (!empty($segments)) {
                GeoSegment::upsert($segments, ['id'], ['track_id', 'prev_id', 'next_id', 'device_id', 'geo_zone_id', 'dt_begin', 'dt_end', 'distance']);
            }
        }
    }

    private function buildSegmentsForTrack(string $trackId): array
    {
        $points = GeoPoint::where('track_id', $trackId)
            ->orderBy('id')
            ->cursor();

        $segments   = [];
        $currentSeg = null;

        foreach ($points as $point) {
            $isNew = !$currentSeg || $point->zone_id !== $currentSeg['geo_zone_id'];

            if ($isNew && $currentSeg !== null) {
                $currentSeg['dt_end']   = $point->dt_device;
                $currentSeg['distance'] += $point->distance;
                $segments[]             = $currentSeg;
            }

            if ($isNew) {
                $currentSeg = [
                    'id'          => $point->id,
                    'track_id'    => $trackId,
                    'device_id'   => $point->device_id,
                    'geo_zone_id' => $point->zone_id,
                    'dt_begin'    => $point->dt_device,
                    'dt_end'      => null,
                    'distance'    => $point->distance,
                    'prev_id'     => null,
                    'next_id'     => null,
                ];
            } else {
                $currentSeg['distance'] += $point->distance;
            }
        }

        if ($currentSeg !== null) {
            $currentSeg['dt_end'] = $currentSeg['dt_begin'];
            $segments[]           = $currentSeg;
        }

        for ($i = 0; $i < count($segments); $i++) {
            if ($i > 0) $segments[$i]['prev_id'] = $segments[$i - 1]['id'];
            if ($i < count($segments) - 1) $segments[$i]['next_id'] = $segments[$i + 1]['id'];
        }

        return $segments;
    }
}
