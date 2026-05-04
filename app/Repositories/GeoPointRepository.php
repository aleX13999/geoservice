<?php

namespace App\Repositories;

use App\Models\GeoPoint;
use Illuminate\Database\Eloquent\Collection;

readonly class GeoPointRepository
{
    public function getRawPoints(): Collection
    {
        return GeoPoint::query()
            ->orWhereNull('track_id')
            ->orWhereNull('zone_id')
            ->orWhereNull('distance')
            ->distinct()
            ->get();
    }

    public function getTrackPoints(string $trackId): Collection
    {
        return GeoPoint::query()
            ->where('track_id', $trackId)
            ->orderBy('id')
            ->get();
    }
}
