<?php

namespace Klamo\ProfilingSystem\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Illuminate\Support\Facades\Config;

class KlamoProfilerProcessGeneric extends Command{

    protected $signature = 'klamoProfiler:process-generic';

    protected $description = 'Process the latest generic consumer profile in the Klamo Profiler system.';

    public function handle()
    {
        //Get queue from config
        $queue = Config::get('ProfilingSystem.queues.process');

        Bus::batch([
            KlamoProfiler::process()->calculateGenericConsumerProfilePoints(),
            KlamoProfiler::process()->calculateGenericConsumerProfileWeights(),
            KlamoProfiler::process()->calculateGenericProductPreferences(),
        ])
        //Choose the queue on which the batch of jobs will run
        ->onQueue($queue) 
        ->dispatch();
    }
}