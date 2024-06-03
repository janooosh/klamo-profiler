<?php

namespace Klamo\ProfilingSystem\Repositories\GenericConsumerProfile;

use Klamo\ProfilingSystem\Jobs\GenerateGenericConsumerProfile;
use Klamo\ProfilingSystem\Models\GenericConsumerProfile;

class GenericConsumerProfileRepository implements IGenericConsumerProfileRepository{


    /**
     * Generate
     * 
     * Generates generic consumer profile
     * 
     * The generation is passed to a jobs.
     * Returns the job object.
     * 
     * @return Klamo\ProfilingSystem\Jobs\GenerateGenericConsumerProfile
     */
    public function generate()
    {
        return new GenerateGenericConsumerProfile();
    }

    /**
     * Create
     * 
     * Creates a new Generic Consumer Profile up based on:
     *  - On current month
     *  - On current year
     * 
     * Create if it doesn't exist or update the existing one.
     * Regardless return the generic consumer profile
     * 
     * @param month
     * @param year
     * 
     * @return \Klamo\ProfilingSystem\Models\GenericConsumerProfile
     */
    public function create()
    {
        $month = now()->month;
        $year = now()->year;

        $generic_consumer_profile = GenericConsumerProfile::updateOrCreate([
            'month' => $month,
            'year' => $year,
        ]);

        return $generic_consumer_profile;
    }

    /**
     * Read
     * 
     * Given an id, return the generic_consumer profile
     * 
     * @param generic_consumer_profile_id
     * 
     * @return \Klamo\ProfilingSystem\Models\ConsumerProfile
     */
    public function read(?Int $generic_consumer_profile_id)
    {
        //If generic consumer profile id is not null, then find its profile and return it
        if($generic_consumer_profile_id) {
            return GenericConsumerProfile::find($generic_consumer_profile_id);
        }
        return null;
    }

    /**
     * GetLatest
     * 
     * Returns the latest generic consumer profile
     * 
     * @return \Klamo\ProfilingSystem\Models\GenericConsumerProfile
     */
    public function getLatest()
    {
        return GenericConsumerProfile::latest()->first();
    }

    /**
     * GetRecommendations
     * 
     * Given the latest generic consumer profile
     * Return a sorted by preference array holding the product ids
     *
     * @return array
     */
    public function getRecommendations()
    {
        //Grab the latest GenericConsumerProfiles
        $generic_consumer_profile = GenericConsumerProfile::latest()->first();

        //If generic consumerprofile exists and has product profiles, then return the sorted array of product ids
        if($generic_consumer_profile) {
            if($generic_consumer_profile->productProfiles){
                //Get produc_ids, sorted by preference (NOT Profiles)
                return $generic_consumer_profile->productProfiles()->orderByPivot('preference', 'DESC')->pluck('product_id')->toArray();
            }
        }
        return [];
    }

    /**
     * Delete
     * 
     * Given a generic consumer profile id, delete its associated generic consumer profile
     * Return the result of deletion
     * 
     * @param generic_consumer_profile_id
     * 
     * @return boolean
     */
    public function delete(?Int $generic_consumer_profile_id)
    {
        //Grab consumer profile based on consumer id and return false if it doesn't exist
        $generic_consumer_profile = $this->read($generic_consumer_profile_id);
        if($generic_consumer_profile){
            //Attempt to delete the consumer profile and return the result
            return $generic_consumer_profile->delete();
        }
        return false;
    }
}