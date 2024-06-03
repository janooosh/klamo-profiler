<?php

namespace Klamo\ProfilingSystem\Console;

use Illuminate\Console\Command;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;

class KlamoProfilerGenericConsumerProfileSetup extends Command{

    protected $signature = 'klamoProfiler:generic-consumer-profile-setup';

    protected $description = 'Generic consumer profile setup for the Klamo Profiler system.';

    public function handle()
    {
        //Setup of consumer profiles, customize inside this method
        $answer = $this->confirm('Do you want to set the generic consumer profile up?');
        if ($answer) {
            $this->setup();
        }
    }

    private function setup()
    {
        //Use the klamo profiler method to create the initial generic consumer profile
        KlamoProfiler::setup()->genericConsumerProfile()->create();
    }

}