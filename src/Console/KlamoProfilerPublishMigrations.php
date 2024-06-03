<?php

namespace Klamo\ProfilingSystem\Console;

use Illuminate\Console\Command;

class KlamoProfilerPublishMigrations extends Command{

    protected $signature = 'klamoProfiler:publish-migrations';

    protected $description = 'Publishes all migrations required by the Klamo profiling system';

    public function handle()
    {
        //Force publishing of essential migrations
        $this->call('vendor:publish', [
            '--provider' => 'Klamo\ProfilingSystem\ProfilingServiceProvider',
            '--tag' => 'migrations',
            '--force' => true,
        ]);

        //Confirm whether to publish job table(in case it already exists)
        $answer = $this->confirm('Do you want to publish the jobs table?');
        if ($answer) {
            $this->call('vendor:publish', [
                '--provider' => 'Klamo\ProfilingSystem\ProfilingServiceProvider',
                '--tag' => 'job-migration',
            ]);
            $this->info('Jobs table was published');
        }
    }
}