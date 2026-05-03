<?php

namespace App\Services;

use App\Models\GeoPoint;

readonly class TrackService
{
    private const int TRACK_PAUSE_DIVIDE = 600000;

    public function build()
    {

    }

    public function save(int $deviceId): int
    {
        return 0;
    }
}
