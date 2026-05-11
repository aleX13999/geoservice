<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeoSegment extends Model
{
    protected $table        = 'geo_segment';
    protected $keyType      = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;
    protected $fillable     = [
        'id',
        'track_id',
        'prev_id',
        'next_id',
        'device_id',
        'zone_id',
        'dt_begin',
        'dt_end',
        'distance',
    ];
    protected $casts        = [
        'id'        => 'string',
        'track_id'  => 'string',
        'prev_id'   => 'string',
        'next_id'   => 'string',
        'device_id' => 'integer',
        'zone_id'   => 'integer',
        'dt_begin'  => 'datetime',
        'dt_end'    => 'datetime',
        'distance'  => 'decimal:2',
    ];

    public function startPoint(): BelongsTo
    {
        return $this->belongsTo(GeoPoint::class, 'id', 'id');
    }

    public function nextSegment(): BelongsTo
    {
        return $this->belongsTo(GeoSegment::class, 'next_id', 'id');
    }

    public function prevSegment(): BelongsTo
    {
        return $this->belongsTo(GeoSegment::class, 'prev_id', 'id');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(GeoDevice::class, 'device_id', 'id');
    }
}
