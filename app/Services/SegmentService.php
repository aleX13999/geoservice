<?php

namespace App\Services;

use App\Repositories\GeoPointRepository;

readonly class SegmentService
{
    public function __construct(
        public GeoPointRepository $geoPointRepository,
    ) {}

    private function handleTrack(string $trackId): int
    {
        $points = $this->geoPointRepository->getTrackPoints($trackId);

        if ($points->isEmpty()) {
            return 0;
        }

        $segments = [];
        $currentSegment = null;
    }
}
