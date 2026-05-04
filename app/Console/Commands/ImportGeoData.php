<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('app:import-geo-data')]
#[Description('Command description')]
class ImportGeoData extends Command
{
    protected $signature = 'geo:import {path}';
    protected $description = 'Import geo-data to database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = $this->argument('path');

        if (!File::isDirectory($path)) {
            $this->error("Directory {$path} not found");
            return 1;
        }

        $files = File::files($path);

        foreach ($files as $file) {
            $this->info("Reading file: " . $file->getFilename());
            $this->processSqlFile($file->getPathname());
        }

        $this->info('Imported successfully!');

        return 0;
    }

    private function processSqlFile(string $filePath): void
    {
        $content = File::get($filePath);
    }
}
