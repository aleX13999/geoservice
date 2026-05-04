<?php

namespace App\Console\Commands;

use App\Repositories\GeoPointRepository;
use App\Services\DistanceService;
use App\Services\TrackService;
use Illuminate\Console\Command;

class GeoHandleCommand extends Command
{
    protected $signature = 'geo:handle';

    public function __construct(
        private readonly GeoPointRepository $geoPointRepository,
        private readonly TrackService       $trackService,
        private readonly DistanceService    $distanceService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $rawPoints = $this->geoPointRepository->getRawPoints();
        if (!count($rawPoints)) {
            return;
        }

        $deviceIds = array_unique($rawPoints->pluck('device_id')->toArray());

        $this->info('Get raw points device ids: '.implode(', ', $deviceIds));
    }
}
