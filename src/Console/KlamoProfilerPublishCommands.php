<?php

namespace Klamo\ProfilingSystem\Console;

use Illuminate\Console\Command;

class KlamoProfilerPublishCommands extends Command{

    protected $signature = 'klamoProfiler:publish-commands';

    protected $description = 'Publishes all commands required by the Klamo profiling system';

    public function handle()
    {
        $answer = $this->confirm('Do you want to publish the commands?');
        
        if ($answer) {
            $this->call('vendor:publish', [
                '--provider' => 'Klamo\ProfilingSystem\ProfilingServiceProvider',
                '--tag' => 'commands',
            ]);
        }
    }
}