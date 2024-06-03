<?php

namespace Klamo\ProfilingSystem\Observers;

use Klamo\ProfilingSystem\Jobs\RemoveProductProfileFromConsumerProfiles;
use Klamo\ProfilingSystem\Jobs\RemoveProductProfileFromGenericConsumerProfiles;
use Klamo\ProfilingSystem\Jobs\RemoveProductProfileFromProfiles;
use Klamo\ProfilingSystem\Jobs\SyncProductProfileToProfiles;
use Klamo\ProfilingSystem\Jobs\SyncProfilingTagsToProfile;
use Klamo\ProfilingSystem\Models\ConsumerProfile;
use Klamo\ProfilingSystem\Models\GenericConsumerProfile;
use Klamo\ProfilingSystem\Models\ProductProfile;

class ProductProfileObserver{

    /**
     * Handle the ProductProfile "created" event.
     *
     * @param  \Klamo\ProfilingSystem\Models\ProductProfile  $product_profile
     * @return void
     */
    public function created(ProductProfile $product_profile)
    {
        //Sync profiling tags to newly created product profile
        SyncProfilingTagsToProfile::dispatch(profile: $product_profile)->onQueue('ProfilingSetup');

        //Add newly created product profile to all consumer profiles
        $consumer_profiles = ConsumerProfile::all();

        SyncProductProfileToProfiles::dispatch(product_profile_id: $product_profile->id, profiles: $consumer_profiles)->onQueue('ProfilingSetup');
        
        //Add newly created product profile to all generic consumer profiles
        $generic_consumer_profiles = GenericConsumerProfile::all();

        SyncProductProfileToProfiles::dispatch(product_profile_id: $product_profile->id, profiles: $generic_consumer_profiles)->onQueue('ProfilingSetup');
    }

    /**
     * Handle the ProductProfile "deleted" event.
     *
     * @param  \Klamo\ProfilingSystem\Models\ProductProfile  $product_profile
     * @return void
     */
    public function deleted(ProductProfile $product_profile)
    {
        //Remove deleted product profile from all consumer profiles
        $consumer_profiles = ConsumerProfile::all();
        RemoveProductProfileFromProfiles::dispatch(product_profile_id: $product_profile->id, profiles: $consumer_profiles)->onQueue('ProfilingSetup');
        
        //Remove deleted product profile from all generic consumer profiles
        $generic_consumer_profiles = GenericConsumerProfile::all();
        RemoveProductProfileFromProfiles::dispatch(product_profile_id: $product_profile->id, profiles: $generic_consumer_profiles)->onQueue('ProfilingSetup');
    }
}