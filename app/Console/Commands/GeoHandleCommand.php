<?php

namespace App\Console\Commands;

use App\Services\PointHandleService;
use Illuminate\Console\Command;

class GeoHandleCommand extends Command
{
    protected $signature = 'geo:handle';

    public function __construct(
        private readonly PointHandleService $pointHandleService,
    )
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('Geo handle started');

        $this->pointHandleService->handle();

        $this->info('Geo handle finished');
    }
}
