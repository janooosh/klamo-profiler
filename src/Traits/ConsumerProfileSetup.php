<?php

namespace Klamo\ProfilingSystem\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;

trait ConsumerProfileSetup{

    public function consumerProfileSetup()
    {
        //Chunk size
        $chunk_size = config('ProfilingSystem.chunk_size',10);

        //Create an array of jobs
        $jobs = [];

        //Get consumer model from config and get consumers in chunks
        $consumer_class = Config::get('ProfilingSystem.models.consumer');
        if(!$consumer_class) {
            Log::error("Missing consumer_class to create consumerProfiles in consumerProfileSetup");
            return null;
        }

        $consumer_chunks = $consumer_class::get()->chunk($chunk_size);
        foreach($consumer_chunks as $consumer_chunk){
            //Use the klamo profiler method to generate consumer profiles
            $job = KlamoProfiler::setup()->consumerProfile()->generate(consumers: $consumer_chunk);
            array_push($jobs, $job);
        }
        return $jobs;
    }
}