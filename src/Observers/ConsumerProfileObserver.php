<?php

namespace Klamo\ProfilingSystem\Observers;

use Klamo\ProfilingSystem\Jobs\SyncProductProfilesToProfile;
use Klamo\ProfilingSystem\Jobs\AddProfilingTagsToConsumerProfile;
use Klamo\ProfilingSystem\Jobs\SyncProfilingTagsToProfile;
use Klamo\ProfilingSystem\Models\ConsumerProfile;

class ConsumerProfileObserver{

    /**
     * Handle the ConsumerProfile "created" event.
     *
     * @param  \Klamo\ProfilingSystem\Models\ConsumerProfile  $consumer_profile
     * @return void
     */
    public function created(ConsumerProfile $consumer_profile)
    {
        //Add all product profiles to the newly created consumer profile
        SyncProductProfilesToProfile::dispatch(profile: $consumer_profile)->onQueue('ProfilingSetup');
        //Add all profiling tags to the newly created consumer profile
        SyncProfilingTagsToProfile::dispatch(profile: $consumer_profile)->onQueue('ProfilingSetup');
    }

    /**
     * Handle the ConsumerProfile "deleted" event.
     *
     * @param  \Klamo\ProfilingSystem\Models\ConsumerProfile  $consumer_profile
     * @return void
     */
    public function deleted(ConsumerProfile $consumer_profile)
    {
        
    }
}