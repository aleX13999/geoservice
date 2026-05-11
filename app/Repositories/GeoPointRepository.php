<?php

namespace App\Repositories;

use App\Models\GeoPoint;
use Illuminate\Support\LazyCollection;

readonly class GeoPointRepository
{
    public function getRawPoints(): LazyCollection
    {
        return GeoPoint::query()
            ->orWhereNull('track_id')
            ->orWhereNull('zone_id')
            ->orWhereNull('distance')
            ->orderBy('device_id')
            ->orderBy('id')
            ->distinct()
            ->cursor();
    }
}
