<?php

namespace App\Services;

use App\Models\GeoPoint;

readonly class TrackService
{
    private const int TRACK_PAUSE_DIVIDE = 600000;

    public function build(): void
    {
        $deviceIds = GeoPoint::select('device_id')->distinct()->pluck('device_id');

        foreach ($deviceIds as $deviceId) {
            $points = GeoPoint::where('device_id', $deviceId)
                              ->whereNull('track_id')
                              ->orderBy('ts_device')
                              ->cursor();


            $currentTrackId = null;
            $prevTs = null;
            $updates = [];

            foreach ($points as $point) {
                if ($prevTs === null || ($point->ts_device - $prevTs) > self::TRACK_PAUSE_DIVIDE) {
                    $currentTrackId = $point->id;
                }

                $updates[] = ['id' => $point->id, 'track_id' => $currentTrackId];
                $prevTs = $point->ts_device;

                if (count($updates) >= 500) {
                    $this->save($updates);
                    $updates = [];
                }
            }

            if (!empty($updates)) {
                $this->save($updates);
            }
        }
    }

    private function save(array $updates) {}
}
