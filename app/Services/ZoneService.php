<?php

namespace App\Services;

use App\Repositories\GeoZoneRepository;

class ZoneService
{
    private array $zones = [];

    public function __construct(
        readonly private GeoZoneRepository $zoneRepository,
        readonly private PointService      $pointService,
    ) {}

    public function clearCache(): void
    {
        $this->zones = [];
    }

    public function findZone($lat, $lon): int
    {
        if (empty($this->zones)) {
            $this->loadZones();
        }

        $matchedCircles = [];
        $matchedPolys   = [];

        foreach ($this->zones as $zone) {
            if ($zone['geometry'] === 'circle') {
                if ($this->pointService->isInCircle($lat, $lon, $zone['lat_center'], $zone['lon_center'], $zone['radius'])) {
                    $matchedCircles[] = $zone['id'];
                }
            } else {
                $bounds = $zone['bounds'];
                if ($lat < $bounds['min_lat'] || $lat > $bounds['max_lat'] || $lon < $bounds['min_lon'] || $lon > $bounds['max_lon']) {
                    continue;
                }

                if ($this->pointService->isInPolygon($lat, $lon, $zone['points'])) {
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

        return 0;
    }

    private function loadZones(): void
    {
        if (!empty($this->zones)) {
            return;
        }

        $zones = $this->zoneRepository->getWithZonePoints();

        foreach ($zones as $zone) {
            $data = ['id' => $zone->id, 'geometry' => $zone->geometry];

            if ($zone->geometry === 'circle') {
                $data['lat_center'] = $zone->lat_center;
                $data['lon_center'] = $zone->lon_center;
                $data['radius']     = $zone->radius;

            } else {
                foreach ($zone->polygonPoints as $polygon) {
                    $data['points'][] = ['lat' => $polygon->lat, 'lon' => $polygon->lon];
                }

                $data['bounds'] = [
                    'min_lat' => min(array_column($data['points'], 'lat')),
                    'max_lat' => max(array_column($data['points'], 'lat')),
                    'min_lon' => min(array_column($data['points'], 'lon')),
                    'max_lon' => max(array_column($data['points'], 'lon')),
                ];
            }

            $this->zones[] = $data;
        }
    }
}
