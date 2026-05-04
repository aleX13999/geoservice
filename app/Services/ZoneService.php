<?php

namespace App\Services;

use App\Models\GeoPoint;
use App\Models\GeoZone;
use App\Models\GeoZonePoint;
use Illuminate\Support\Facades\DB;

class ZoneService
{
    private array $zones = [];

    public function __construct(
        readonly private PointService $pointService,
    ) {}

    public function calc(): void
    {
        $this->loadZones();

        $deviceIds = GeoPoint::select('device_id')->distinct()->pluck('device_id');

        foreach ($deviceIds as $deviceId) {
            $points = GeoPoint::where('device_id', $deviceId)
                              ->whereNull('zone_id')
                              ->orderBy('ts_device')
                              ->cursor();

            $updates = [];
            foreach ($points as $point) {
                if ($point->lat == 0 && $point->lon == 0)
                    continue;

                $zoneId    = $this->findZone($point->lat, $point->lon);
                $updates[] = ['id' => $point->id, 'zone_id' => $zoneId];

                if (count($updates) >= 500) {
                    $this->save($updates);
                    $updates = [];
                }
            }

            if (count($updates) > 0) {
                $this->save($updates);
            }
        }
    }

    private function loadZones(): void
    {
        $zones = GeoZone::all();
        foreach ($zones as $zone) {
            $data = ['id' => $zone->id, 'geometry' => $zone->geometry];
            if ($zone->geometry === 'circle') {
                $data['center'] = [$zone->lat_center, $zone->lon_center];
                $data['radius'] = $zone->radius;
            } else {
                $data['polygon'] = GeoZonePoint::where('zone_id', $zone->id)
                                               ->orderBy('serial_index')
                                               ->get()
                                               ->map(fn($p) => [$p->lat, $p->lon])
                                               ->toArray();
            }

            $this->zones[] = $data;
        }
    }

    private function findZone($lat, $lon): ?int
    {
        $matchedCircles = [];
        $matchedPolys   = [];

        foreach ($this->zones as $zone) {
            if ($zone['geometry'] === 'circle') {
                if ($this->pointService->isInCircle($lat, $lon, $zone['center'][0], $zone['center'][1], $zone['radius'])) {
                    $matchedCircles[] = $zone['id'];
                }
            } else {
                if ($this->pointService->isInPolygon($lat, $lon, $zone['polygon'])) {
                    $matchedPolys[] = $zone['id'];
                }
            }
        }

        if (!empty($matchedCircles)) {
            return $matchedCircles[0];
        }

        if (!empty($matchedPolys)) {
            return $matchedPolys[0];
        }

        return null;
    }

    private function save(array $updates): void
    {
        $ids      = array_column($updates, 'id');
        $cases    = '';
        $bindings = [];

        foreach ($updates as $u) {
            $cases      .= "WHEN ? THEN ? ";
            $bindings[] = $u['id'];
            $bindings[] = $u['zone_id'];
        }

        $sql = "UPDATE geo_point SET zone_id = CASE id {$cases} END WHERE id IN (".implode(',', array_fill(0, count($ids), '?')).")";
        DB::update($sql, array_merge($bindings, $ids));
    }
}
