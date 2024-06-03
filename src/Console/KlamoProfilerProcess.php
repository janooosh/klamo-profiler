<?php

namespace Klamo\ProfilingSystem\Console;

use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Illuminate\Support\Facades\Config;

class KlamoProfilerProcess extends Command{

    protected $signature = 'klamoProfiler:process';

    protected $description = 'Process all consumer profiles flags for processing in the Klamo Profiler system.';

    public function handle()
    {
        //Get all consumer profiles flagged for processing
        $consumer_profiles = KlamoProfiler::setup()->ConsumerProfile()->getFlaggedForProcessing();

        //Get queue from config
        $queue = Config::get('ProfilingSystem.queues.process');

        foreach($consumer_profiles as $consumer_profile){

            Bus::batch([
                KlamoProfiler::process()->calculateConsumerProfilePoints($consumer_profile->consumer_id),
                KlamoProfiler::process()->calculateConsumerProfileWeights($consumer_profile->consumer_id),
                KlamoProfiler::process()->calculateProductPreferences($consumer_profile->consumer_id),
            ])->then(function(Batch $batch) use ($consumer_profile){
                //Update process flag for consumer profile if there were no failures
                if(!$batch->hasFailures()){
                    KlamoProfiler::setup()->ConsumerProfile()->flagForProcess($consumer_profile->consumer_id, false);
                }
            }
            //Choose the queue on which the batch of jobs will run
            )->onQueue($queue) 
            ->dispatch();
        }
        
    }
}