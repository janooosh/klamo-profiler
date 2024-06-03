<?php

namespace Klamo\ProfilingSystem\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Illuminate\Support\Facades\Config;

class KlamoProfilerConsumerProfileSetup extends Command{

    protected $signature = 'klamoProfiler:consumer-profile-setup';

    protected $description = 'Consumer profile setup for the Klamo Profiler system.';

    public function handle()
    {
        //Setup of consumer profiles, customize inside this method
        $answer = $this->confirm('Do you want to set the consumer profiles up?');
        if ($answer) {
            $this->setup();
        }
    }

    private function setup()
    {
        //Chunk size
        $chunk_size = 50;

        //Get queue from config
        $queue = Config::get('ProfilingSystem.queues.setup');

        //Create a batch where jobs will be added
        $batch = Bus::batch([])->onQueue($queue)->dispatch();
        
        //Get consumer model from config and get consumers in chunks
        $consumer_class = Config::get('ProfilingSystem.models.consumer');

        $consumer_chunks = $consumer_class::get()->chunk($chunk_size);
        foreach($consumer_chunks as $consumer_chunk){
            //Use the klamo profiler method to generate consumer profiles
            $job = KlamoProfiler::setup()->consumerProfile()->generate(consumers: $consumer_chunk);
            $batch->add($job);
        }
    }
}