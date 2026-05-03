<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeoDevice extends Model
{
    protected $table    = 'geo_device';
    protected $fillable = [
        'name',
    ];

    public function points(): HasMany
    {
        return $this->hasMany(GeoPoint::class, 'device_id', 'id');
    }

    public function segments(): HasMany
    {
        return $this->hasMany(GeoSegment::class, 'device_id', 'id');
    }
}
