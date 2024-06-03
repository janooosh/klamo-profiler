<?php

namespace Klamo\ProfilingSystem\Repositories\ConsumerProfile;

use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Klamo\ProfilingSystem\Jobs\GenerateConsumerProfiles;
use Klamo\ProfilingSystem\Models\ConsumerProfile;

class ConsumerProfileRepository implements IConsumerProfileRepository{

    /**
     * Generate
     * 
     * Mass Generates consumer profiles based on:
     *  - An array of of objects of type Consumer (based on config)
     * 
     * The mass generation is passed to a batch of jobs.
     * Returns the batch object.
     * 
     * @param consumers
     * 
     * @return Klamo\ProfilingSystem\Jobs\GenerateConsumerProfiles
     */
    public function generate($consumers)
    {
        return new GenerateConsumerProfiles($consumers);
    }

    /**
     * Create
     * 
     * Given the id of a consumer
     * 
     * Create a consumer profile if it doesn't exist or update the existing one.
     * Return the consumer profile
     * 
     * @param consumer_id
     * 
     * @return \Klamo\ProfilingSystem\Models\ConsumerProfile
     */
    public function create(?Int $consumer_id)
    {
        //If consumer profile id is not null, then create a profile and return it
        if($consumer_id){
            $consumer_profile = ConsumerProfile::updateOrCreate([
                'consumer_id' => $consumer_id
            ]);
            return $consumer_profile;
        }
        return null;
    }

    /**
     * Given a consumer id, return the consumer profile
     * 
     * @param consumer_id
     * 
     * @return \Klamo\ProfilingSystem\Models\ConsumerProfile
     */
    public function read($consumer_id)
    {
        //If consumer profile id is not null, then find its profile and return it
        if($consumer_id){
            $profile = ConsumerProfile::where('consumer_id', $consumer_id)->first();
            return $profile;
        }
        return null;
        

    }

    /**
     * FlagForProcess
     * 
     * Given a consumer id flag its consumer profile for process
     * 
     * @param consumer_id
     * @param flagForProcess
     * 
     * @return void
     */
    public function flagForProcess(?Int $consumer_id, Bool $flagForProcess)
    {
        $consumer_profile = $this->read($consumer_id);

        //If consumer profile exists, then update its processing status
        if($consumer_profile){
            $consumer_profile_update_status = $consumer_profile->update([
                'needs_processing' => $flagForProcess,
            ]);
    
            return $consumer_profile_update_status;
        }
    }

    /**
     * GetFlaggedForProcessing
     * 
     * Returns all consumer profiles flagged for processing
     * 
     * @return Collection
     */
    public function getFlaggedForProcessing()
    {
        return ConsumerProfile::where('needs_processing', true)->get();
    }

    /**
     * GetRecommendations
     * 
     * Given a consumer id
     * Return a sorted by preference array holding the product ids
     * 
     * @param consumer_id
     * 
     * @return array
     */
    public function getRecommendations(?Int $consumer_id)
    {
        $consumer_profile = $this->read($consumer_id);
        
        //If the consumer profile exists, then check that there are product preference above 0
        if($consumer_profile){
            if($consumer_profile->productProfiles()->where('preference','>',0)->exists()){
                return $consumer_profile->productProfiles()->orderByPivot('preference', 'DESC')->pluck('product_id')->toArray();
            }
        }
        return KlamoProfiler::setup()->genericConsumerProfile()->getRecommendations();
    }

    /**
     * Delete
     * 
     * Given a consumer id, delete the consumer profile
     * 
     * @param consumer_id
     * 
     * @return boolean
     */
    public function delete(?Int $consumer_id)
    {
        //Grab consumer profile based on consumer id and return false if it doesn't exist
        $consumer_profile = $this->read($consumer_id);
        if($consumer_profile){
            //Attempt to delete the consumer profile and return the result
            return $consumer_profile->delete();
        }
        return false;
    }
}