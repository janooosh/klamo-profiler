<?php

namespace Klamo\ProfilingSystem\Jobs;

use Throwable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Config;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Klamo\ProfilingSystem\Models\ConsumerProfile;
use Klamo\ProfilingSystem\Models\ProductProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;

class GenerateProfilingTagTypes implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dispatcher = ConsumerProfile::getEventDispatcher();
        ConsumerProfile::unsetEventDispatcher();

        //Get profiling tag types from config
        $profiling_tag_types = Config::get('ProfilingSystem.models.profiling_tag_types');

        //Use the klamo profiler method to create profiling tag types based on the array

        foreach ($profiling_tag_types as $profiling_tag_type) {
            KlamoProfiler::setup()->profilingTagType()->create(profiling_tag_type_name: $profiling_tag_type['type']);
        }

        ConsumerProfile::setEventDispatcher($dispatcher);
    }

    public function failed(Throwable $exception)
    {
        //TODO Log failure
    }
}