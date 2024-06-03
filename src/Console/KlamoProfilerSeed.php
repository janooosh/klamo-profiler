<?php

namespace Klamo\ProfilingSystem\Console;

use Illuminate\Console\Command;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;

class KlamoProfilerSeed extends Command{

    protected $signature = 'klamoProfiler:seed';

    protected $description = 'Initial seed for the Klamo Profiler system.';

    public function handle()
    {
        $answer = $this->confirm('Do you want to seed the default consumer actions?');
        if ($answer) {
            $this->seedDefaultConsumerActions();
        }

        $answer = $this->confirm('Do you want to seed the default profiling tags?');
        if ($answer) {
            $this->seedDefaultProfilingTags();
        }
        
    }

    private function seedDefaultConsumerActions()
    {
        //Default consumers actions
        $action_one = "VIEWED";
        $action_two = "ADDED_TO_CART";
        $action_three = "PURCHASED";

        //With the default weights
        $weight_one = 1;
        $weight_two = 2;
        $weight_three = 3;

        KlamoProfiler::setup()->ConsumerAction()->create(consumer_action_name: $action_one, consumer_action_weight: $weight_one);
        KlamoProfiler::setup()->ConsumerAction()->create(consumer_action_name: $action_two, consumer_action_weight: $weight_two);
        KlamoProfiler::setup()->ConsumerAction()->create(consumer_action_name: $action_three, consumer_action_weight: $weight_three);
    }

    private function seedDefaultProfilingTags()
    {
        //Default trending profiling tag
        $profiling_tag_type_name = 'custom';
        $profiling_tag_name = 'trending';

        KlamoProfiler::setup()->ProfilingTagType()->create(profiling_tag_type_name: $profiling_tag_type_name);
        KlamoProfiler::setup()->ProfilingTag()->create(profiling_tag_name: $profiling_tag_name, profiling_tag_type_name: $profiling_tag_type_name);
    }
}