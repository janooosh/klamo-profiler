<?php

namespace Klamo\ProfilingSystem\Console;

use Illuminate\Support\Facades\Config;
use Illuminate\Console\Command;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;

class KlamoProfilerTagTypeSetup extends Command{

    protected $signature = 'klamoProfiler:profiling-tag-type-setup';

    protected $description = 'Profiling tag type setup for the Klamo Profiler system.';

    public function handle()
    {
        //Setup of profiling tag types, customize inside this method
        $answer = $this->confirm('Do you want to set the profiling tag types up?');
        if ($answer) {
            $this->setup();
        }
    }

    private function setup()
    {
        //Get profiling tag types from config
        $profiling_tag_types = Config::get('ProfilingSystem.models.profiling_tag_types');

        //Use the klamo profiler method to create profiling tag types based on the array

        foreach ($profiling_tag_types as $profiling_tag_type) {
            KlamoProfiler::setup()->profilingTagType()->create(profiling_tag_type_name: $profiling_tag_type['type']);
        }
    }
}