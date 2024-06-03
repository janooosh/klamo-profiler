<?php

namespace Klamo\ProfilingSystem\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Illuminate\Support\Facades\Config;

class KlamoProfilerTagSetup extends Command{

    protected $signature = 'klamoProfiler:profiling-tag-setup';

    protected $description = 'Profiling tag type setup for the Klamo Profiler system.';

    public function handle()
    {
        //Set Profiling Tags up
        /** Choose the following: 
         *   - The class where the profiling tags of a specific type will come from
         *   - The column name from which the profiling tag name will come from
         *   - The profiling tag type name
         *   e.g.
         */
         $answer = $this->confirm('Do you want to set the profiling tags up?');
        //Get profiling tag types
        $profiling_tag_types = Config::get('ProfilingSystem.models.profiling_tag_types');

        if ($answer) {
            foreach($profiling_tag_types as $profiling_tag_type){
                $this->setup(attribute_class: $profiling_tag_type['class'], column_name: $profiling_tag_type['column'], profiling_tag_type_name: $profiling_tag_type['type']);
            }
        }
        
    }

    private function setup($attribute_class, $column_name, $profiling_tag_type_name)
    {
        //Use the klamo profiler method to generate profiling tags based on the above
        //Get queue
        $queue = Config::get('ProfilingSystem.queues.setup');

        Bus::batch([
                    KlamoProfiler::setup()->profilingTag()->generate(attribute_class: $attribute_class, column_name: $column_name, profiling_tag_type_name: $profiling_tag_type_name)
                ])
                //Choose the queue on which the batch of jobs will run
                ->onQueue($queue) 
                ->dispatch();
    }

}