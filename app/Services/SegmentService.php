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

    private function handleTrack(string $trackId): void
    {
        $tracks = GeoPoint::select('track_id', 'device_id')
                          ->whereNotNull('track_id')
                          ->groupBy('track_id', 'device_id')
                          ->get();

        foreach ($tracks as $track) {
            $points = $this->geoPointRepository->getTrackPoints($track->track_id);

            if ($points->isEmpty()) {
                continue;
            }

            $segments = [];
            $currentSeg = null;

            foreach ($points as $point) {
                $isNew = !$currentSeg || $currentSeg['zone_id'] != $point->zone_id;

                if ($isNew && $currentSeg) {
                    $currentSeg['dt_end'] = $point->dt_device;
                    $segments[] = $currentSeg;
                }

                if ($isNew) {
                    $currentSeg = [
                        'id' => $point->id,
                        'track_id' => $point->track_id,
                        'device_id' => $point->device_id,
                        'zone_id' => $point->zone_id,
                        'dt_begin' => $point->dt_device,
                        'dt_end' => null,
                        'distance' => 0,
                        'prev_id' => null,
                        'next_id' => null
                    ];
                }

                $currentSeg['distance'] += $point->distance;
            }

            if ($currentSeg) {
                $lastPointInTrack = $points->last();
                $currentSeg['dt_end'] = $lastPointInTrack->dt_device;

                $segments[] = $currentSeg;
            }

            for ($i = 0; $i < count($segments); $i++) {
                if ($i > 0) {
                    $segments[$i]['prev_id'] = $segments[$i-1]['id'];
                }

                if ($i < count($segments) - 1) {
                    $segments[$i]['next_id'] = $segments[$i+1]['id'];
                }
            }

            foreach ($segments as $seg) {
                GeoSegment::updateOrCreate(['id' => $seg['id']], $seg);
            }
        }
    }
}
