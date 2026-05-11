<?php

namespace App\Services;

use App\Models\GeoPoint;

readonly class TrackService
{
    private const int TRACK_PAUSE_DIVIDE = 600000;

    public function calculate(GeoPoint $point, ?int $prevTs, ?string $currentTrackId): string
    {
        if ($prevTs === null || ($point->ts_device - $prevTs) > self::TRACK_PAUSE_DIVIDE) {
            $currentTrackId = $point->id;
        }

        return $currentTrackId;
    }
}
