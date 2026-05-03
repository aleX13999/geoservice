<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeoZonePoint extends Model
{
    protected $table    = 'geo_zone_point';
    protected $fillable = [
        'zone_id',
        'serial_index',
        'lat',
        'lon',
        'radius',
    ];
    protected $casts    = [
        'zone_id'      => 'integer',
        'serial_index' => 'integer',
        'lat'          => 'decimal:7',
        'lon'          => 'decimal:7',
        'radius'       => 'decimal:2',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(GeoZone::class, 'zone_id', 'id');
    }
}
