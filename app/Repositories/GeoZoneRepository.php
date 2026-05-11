<?php

namespace App\Repositories;

use App\Models\GeoZone;
use Illuminate\Database\Eloquent\Collection;

readonly class GeoZoneRepository
{
    public function getWithZonePoints(): Collection
    {
        return GeoZone::with('polygonPoints')->get();
    }
}
