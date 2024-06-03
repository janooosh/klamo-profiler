<?php

namespace Klamo\ProfilingSystem\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Illuminate\Support\Facades\Config;
use Klamo\ProfilingSystem\Traits\UpdateProductProfileSetup;

class KlamoProfilerUpdateProductProfile extends Command
{
    protected $signature = 'klamoProfiler:update-product-profiles';

    protected $description = 'Initial setup for the Klamo Profiler system. Generates ...TODO';

    use UpdateProductProfileSetup;

    public function handle()
    {
        $queue = config('ProfilingSystem.queues.setup','ProfilingSetup');

        $jobs = $this->updateProductProfileSetup();

        Bus::batch($jobs)->onQueue($queue)->dispatch();

        $this->info("Done");
    }
}