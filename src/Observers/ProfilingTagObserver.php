<?php

namespace Klamo\ProfilingSystem\Observers;

use Illuminate\Support\Facades\Bus;
use Klamo\ProfilingSystem\Jobs\AddProfilingTagToConsumerProfiles;
use Klamo\ProfilingSystem\Jobs\AddProfilingTagToGenericConsumerProfiles;
use Klamo\ProfilingSystem\Jobs\AddProfilingTagToProductProfiles;
use Klamo\ProfilingSystem\Jobs\RemoveProfilingTagFromConsumerProfiles;
use Klamo\ProfilingSystem\Jobs\RemoveProfilingTagFromGenericConsumerProfiles;
use Klamo\ProfilingSystem\Jobs\RemoveProfilingTagFromProductProfiles;
use Illuminate\Support\Facades\Log;
use Klamo\ProfilingSystem\Jobs\RemoveProfilingTagFromProfiles;
use Klamo\ProfilingSystem\Jobs\SyncProfilingTagToProfiles;
use Klamo\ProfilingSystem\Models\ConsumerProfile;
use Klamo\ProfilingSystem\Models\GenericConsumerProfile;
use Klamo\ProfilingSystem\Models\ProductProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;

class ProfilingTagObserver{

    /**
     * Handle the ProfilingTag "created" event.
     *
     * @param  \Klamo\ProfilingSystem\Models\ProfilingTag  $profiling_tag
     * @return void
     */
    public function created(ProfilingTag $profiling_tag)
    {
        //Add newly created profiling tag to all consumer profiles
        $consumer_profiles = ConsumerProfile::all();

        //Get Queue
        $queue = config('ProfilingSystem.queues.setup','ProfilingSetup');
        if(!$queue || $queue=='') {
            $queue = 'ProfilingSetup';
        }

        //Add newly created profiling tag to all consumer profiles, if there are ConsumerProfiles
        $consumer_profiles = ConsumerProfile::all();
        if(!$consumer_profiles->isEmpty()) {
            SyncProfilingTagToProfiles::dispatch(profiling_tag_id: $profiling_tag->id, profiles: $consumer_profiles)->onQueue($queue);
        }
        else {
            Log::debug("Prevented from dispatching SyncProfilingTagToProfiles: No ConsumerProfile.");
        }


        //Add newly created profiling tag to all generic consumer profiles, if there are genericConsumerProfiles
        $generic_consumer_profiles = GenericConsumerProfile::all();
        if(!$generic_consumer_profiles->isEmpty()) {
            SyncProfilingTagToProfiles::dispatch(profiling_tag_id: $profiling_tag->id, profiles: $generic_consumer_profiles)->onQueue($queue);
        }
        else {
            Log::debug("Prevented from dispatching SyncProfilingTagToProfiles: No GenericConsumerProfile.");
        }

        //Add newly created profiling tag to all product profiles, if there are productProfiles
        $product_profiles = ProductProfile::all();
        if(!$product_profiles->isEmpty()) {
            SyncProfilingTagToProfiles::dispatch(profiling_tag_id: $profiling_tag->id, profiles: $product_profiles)->onQueue($queue);
        }
        else {
            Log::debug("Prevented from dispatching SyncProfilingTagToProfiles: No ProductProfiles.");
        }
    }

    /**
     * Handle the ProfilingTag "deleted" event.
     *
     * @param  \Klamo\ProfilingSystem\Models\ProfilingTag  $profiling_tag
     * @return void
     */
    public function deleted(ProfilingTag $profiling_tag)
    {
        //Remove deleted profiling tag from all consumer profiles
        $consumer_profiles = ConsumerProfile::all();
        RemoveProfilingTagFromProfiles::dispatch(profiling_tag_id: $profiling_tag->id, profiles: $consumer_profiles)->onQueue('ProfilingSetup');
        
        //Remove deleted profiling tag from all generic consumer profiles
        $generic_consumer_profiles = GenericConsumerProfile::all();
        RemoveProfilingTagFromProfiles::dispatch(profiling_tag_id: $profiling_tag->id, profiles: $generic_consumer_profiles)->onQueue('ProfilingSetup');

        //Remove deleted profiling tag from all product profiles
        $product_profiles = ProductProfile::all();
        RemoveProfilingTagFromProfiles::dispatch(profiling_tag_id: $profiling_tag->id, profiles: $product_profiles)->onQueue('ProfilingSetup');
    }
}