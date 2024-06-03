<?php

namespace Klamo\ProfilingSystem\Jobs;

use Throwable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Klamo\ProfilingSystem\Models\ConsumerProfile;
use Klamo\ProfilingSystem\Models\ProductProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;

class GenerateConsumerProfiles implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $consumers;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($consumers)
    {
        $this->consumers = $consumers;
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

        $product_profile_ids = ProductProfile::pluck('id');
        $profiling_tag_ids = ProfilingTag::pluck('id');

        $this->consumers->each(function($consumer)use($product_profile_ids, $profiling_tag_ids){
            $consumer_profile = KlamoProfiler::setup()->ConsumerProfile()->create($consumer->id);

            //Add all product profiles
            $consumer_profile->productProfiles()->sync($product_profile_ids); //CHANGED TO SYNC (from swd)

            //Add all profiling tags
            $consumer_profile->profilingTags()->sync($profiling_tag_ids); //CHANGED TO SYNC (from swd)
        });

        ConsumerProfile::setEventDispatcher($dispatcher);
    }

    public function failed(Throwable $exception)
    {
        //TODO Log failure
    }
}