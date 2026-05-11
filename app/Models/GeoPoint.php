<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeoPoint extends Model
{
    protected $table        = 'geo_point';
    protected $keyType      = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;
    protected $fillable     = [
        'id',
        'device_id',
        'track_id',
        'zone_id',
        'ts_device',
        'dt_device',
        'dt_gps',
        'lon',
        'lat',
        'distance',
        'speed',
        'alt',
        'bearing',
    ];
    protected $casts        = [
        'id'        => 'string',
        'track_id'  => 'string',
        'device_id' => 'integer',
        'zone_id'   => 'integer',
        'ts_device' => 'integer',
        'dt_device' => 'datetime:Y-m-d H:i:s.u',
        'dt_gps'    => 'date',
        'lon'       => 'decimal:7',
        'lat'       => 'decimal:7',
        'distance'  => 'decimal:2',
        'speed'     => 'decimal:2',
        'alt'       => 'decimal:2',
        'bearing'   => 'decimal:2',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(GeoDevice::class, 'device_id', 'id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(GeoZone::class, 'zone_id', 'id');
    }

    public function isInvalid(): bool
    {
        return $this->lat == 0 && $this->lon == 0;
    }
}
