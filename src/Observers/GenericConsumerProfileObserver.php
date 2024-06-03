<?php

namespace Klamo\ProfilingSystem\Observers;

use Klamo\ProfilingSystem\Jobs\SyncProductProfilesToProfile;
use Klamo\ProfilingSystem\Jobs\SyncProfilingTagsToProfile;
use Klamo\ProfilingSystem\Models\GenericConsumerProfile;

class GenericConsumerProfileObserver{

    /**
     * Handle the GenericConsumerProfile "created" event.
     *
     * @param  \Klamo\ProfilingSystem\Models\GenericConsumerProfile  $generic_consumer_profile
     * @return void
     */
    public function created(GenericConsumerProfile $generic_consumer_profile)
    {
        //Add all product profiles to the newly created generic consumer profile
        SyncProductProfilesToProfile::dispatch(profile: $generic_consumer_profile)->onQueue('ProfilingSetup');
        //Add all profiling tags to the newly created generic consumer profile
        SyncProfilingTagsToProfile::dispatch(profile: $generic_consumer_profile)->onQueue('ProfilingSetup');
    }

    /**
     * Handle the GenericConsumerProfile "deleted" event.
     *
     * @param  \Klamo\ProfilingSystem\Models\GenericConsumerProfile  $generic_consumer_profile
     * @return void
     */
    public function deleted(GenericConsumerProfile $generic_consumer_profile)
    {
        
    }
}