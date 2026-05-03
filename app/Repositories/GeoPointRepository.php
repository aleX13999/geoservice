<?php

namespace App\Repositories;

use App\Models\GeoPoint;

readonly class GeoPointRepository
{
    public function getTrackPoints(string $trackId)
    {
        return GeoPoint::where('track_id', $trackId)
            ->orderBy('id')
            ->get();
    }
}
