<?php

namespace App\Http\Controllers;

use App\Models\GeoSegment;
use App\Models\GeoPoint;
use Illuminate\Http\Request;

class SegmentController extends Controller
{
    public function index(Request $request)
    {
        $segmentQuery = GeoSegment::query();
        if ($request->device_id) {
            $segmentQuery->where('device_id', $request->device_id);
        }
        if ($request->date) {
            $segmentQuery->whereDate('dt_begin', $request->date);
        }

        $segments = $segmentQuery
            ->orderBy('dt_begin')
            ->get();

        $result = [];

        foreach ($segments as $seg) {
            $zoneId = $seg->geo_zone_id;

            $points = GeoPoint::query()
                ->where('track_id', $seg->track_id)
                ->where('dt_device', '>=', $seg->dt_begin)
                ->where('dt_device', '<=', $seg->dt_end ?? $seg->dt_begin)
                ->orderBy('dt_device')
                ->pluck('lat', 'lon')
                ->map(fn($lat, $lon) => [(float)$lon, (float)$lat])
                ->values()
                ->toArray();

            $color = $zoneId === 0
                ? '#999999'
                : "hsl(" . (($zoneId * 137) % 360) . ", 70%, 45%)";

            $result[] = [
                'id'      => $seg->id,
                'zone_id' => $zoneId,
                'color'   => $color,
                'points'  => $points,
            ];
        }

        return response()->json(['segments' => $result]);
    }

    public function page(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('geo');
    }
}
