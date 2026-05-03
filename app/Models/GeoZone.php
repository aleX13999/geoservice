<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeoZone extends Model
{
    protected $table    = 'geo_zone';
    protected $fillable = [
        'object_type',
        'object_id',
        'name',
        'geometry',
        'lat_center',
        'lon_center',
        'radius',
    ];
    protected $casts    = [
        'object_type' => 'integer',
        'object_id'   => 'integer',
        'lat_center'  => 'decimal:7',
        'lon_center'  => 'decimal:7',
        'radius'      => 'decimal:2',
    ];

    public function polygonPoints(): HasMany
    {
        return $this->hasMany(GeoZonePoint::class, 'zone_id', 'id')
            ->orderBy('serial_index');
    }

    public function points(): HasMany
    {
        return $this->hasMany(GeoPoint::class, 'zone_id', 'id');
    }
}
