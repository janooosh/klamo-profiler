<?php

namespace Klamo\ProfilingSystem\Console;

use Illuminate\Console\Command;

class KlamoProfilerInit extends Command{

    protected $signature = 'klamoProfiler:init';

    protected $description = 'Initial setup for the Klamo Profiler system. Publishes files necessary for the klamo profiling system';

    public function handle()
    {
        //Publish the config file
        $this->call('vendor:publish', [
            '--provider' => 'Klamo\ProfilingSystem\ProfilingServiceProvider',
            '--tag' => 'config',
        ]);

        $this->info('Config was published');

        //Publish migration files
        $this->call('klamoProfiler:publish-migrations');
    
    }
}