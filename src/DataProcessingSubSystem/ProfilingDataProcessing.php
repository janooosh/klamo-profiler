<?php

namespace Klamo\ProfilingSystem\DataProcessingSubSystem;

use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Klamo\ProfilingSystem\Jobs\CalculateConsumerProfilePoints;
use Klamo\ProfilingSystem\Jobs\CalculateProductPreferences;
use Klamo\ProfilingSystem\Jobs\CalculateWeights;

class ProfilingDataProcessing implements ProfilingDataProcessingInterface{


    /**
     * CalculateConsumerProfilePoints
     * 
     * Given a consumer id, creates and returns job which calculates consumer profile points for a consumer profile
     * 
     *  @param consumer_id
     * 
     *  @return Klamo\ProfilingSystem\Jobs\CalculateConsumerProfilePoints
     */
    public function calculateConsumerProfilePoints(?Int $consumer_id)
    {
        $consumer_profile = KlamoProfiler::setup()->ConsumerProfile()->read($consumer_id);

        if(!$consumer_profile){
            //TODO log error
            return;
        }

        return new CalculateConsumerProfilePoints($consumer_profile);
    }

    /**
     * CalculateGenericConsumerProfilePoints
     * 
     * Creates and returns a job which calculates generic consumer profile points for a generic consumer profile
     * 
     *  @return Klamo\ProfilingSystem\Jobs\CalculateConsumerProfilePoints
     */
    public function calculateGenericConsumerProfilePoints()
    {
        $generic_consumer_profile = KlamoProfiler::setup()->GenericConsumerProfile()->getLatest();

        if(!$generic_consumer_profile){
            //TODO log error
            return;
        }

        return new CalculateConsumerProfilePoints($generic_consumer_profile);
    }

    /**
     * CalculateConsumerProfileWeights
     * 
     * Creates and returns a job which calculates consumer profile weights for a consumer profile
     * 
     * @param consumer_id
     * 
     * @return Klamo\ProfilingSystem\Jobs\CalculateWeights
     */
    public function calculateConsumerProfileWeights($consumer_id)
    {
        $consumer_profile = KlamoProfiler::setup()->ConsumerProfile()->read($consumer_id);
        if(!$consumer_profile){
            //TODO log error
            return;
        }

        return new CalculateWeights(profile: $consumer_profile);
    }

    /**
     * CalculateGenericConsumerProfileWeights
     * 
     * Creates and returns a job which calculates generic consumer profile weights for a generic consumer profile
     * 
     *  @return Klamo\ProfilingSystem\Jobs\CalculateWeights
     */
    public function calculateGenericConsumerProfileWeights()
    {
        $generic_consumer_profile = KlamoProfiler::setup()->GenericConsumerProfile()->getLatest();

        if(!$generic_consumer_profile){
            //TODO log error
            return;
        }

        return new CalculateWeights(profile: $generic_consumer_profile);
    }

    /**
     * CalculateProductPreferences
     * 
     * Creates and returns a job which calculates product preferences for a consumer profile
     * 
     * @param consumer_id
     * 
     * @return Klamo\ProfilingSystem\Jobs\CalculateProductPreferences
     */
    public function calculateProductPreferences($consumer_id)
    {
        $consumer_profile = KlamoProfiler::setup()->ConsumerProfile()->read($consumer_id);
        
        if(!$consumer_profile){
            //TODO log error
            return;
        }

        $product_profiles = $consumer_profile->productProfiles;

        if(!$product_profiles){
            //TODO log error
            return;
        }
       
        return new CalculateProductPreferences(consumer_profile: $consumer_profile, product_profiles: $product_profiles);
    }

    /**
     * CalculateGenericProductPreferences
     * 
     * Creates and returns a job which calculates product preferences for a generic consumer profile
     * 
     *  @return Klamo\ProfilingSystem\Jobs\CalculateProductPreferences
     */
    public function calculateGenericProductPreferences()
    {
        $generic_consumer_profile = KlamoProfiler::setup()->GenericConsumerProfile()->getLatest();

        if(!$generic_consumer_profile){
            //TODO log error
            return;
        }

        $product_profiles = $generic_consumer_profile->productProfiles;

        if(!$product_profiles){
            //TODO log error
            return;
        }
        
        return new CalculateProductPreferences(consumer_profile: $generic_consumer_profile, product_profiles: $product_profiles);
    }
}