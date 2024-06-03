<?php

namespace Klamo\ProfilingSystem\DataGatheringSubSystem;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Klamo\ProfilingSystem\Jobs\IncrementActionCount;
use Klamo\ProfilingSystem\Jobs\IncrementConsumerCounts;
use Klamo\ProfilingSystem\Models\ConsumerAction;
use Klamo\ProfilingSystem\Models\ConsumerProfile;
use Klamo\ProfilingSystem\Models\GenericConsumerProfile;
use Klamo\ProfilingSystem\Models\ProductProfile;

class ProfilingDataGathering implements ProfilingDataGatheringInterface{

    /**
     * ConsumerProductAction
     * 
     * Gathers data:
     *  - When a consumer with a profile in the system
     *  - On a product with a profile in the system
     *  - Performs an action which is setup in the system
     * 
     * The data are gathered and saved via jobs in the system
     * 
     * @param consumer_id
     * @param product_id
     * @param consumer_action
     * 
     * @return void
     */
    public function consumerProductAction(?Int $consumer_id, ?Int $product_id, ?String $consumer_action)
    {
        $consumer_profile = ConsumerProfile::with('profilingTags', 'productProfiles')->where('consumer_id', $consumer_id)->first();
        $product_profile = ProductProfile::where('product_id',$product_id)->first();
        $consumer_action = ConsumerAction::where('name',$consumer_action)->first();
        
        //If any of the consumer/product profile or the consumer action with the given values are null, then return
        if(($product_profile === null) || ($consumer_profile === null) || ($consumer_action === null)){
            //Log error
            return;
        }

        //Get queue from config
        $queue = Config::get('ProfilingSystem.queues.process');

        //Add actions to consumer profile
        IncrementActionCount::dispatch(product_profile: $product_profile, consumer_profile: $consumer_profile, points: $consumer_action->weight)->onQueue($queue);
        
        //Increment consumer counts for product profile and their enabled profiling tags
        IncrementConsumerCounts::dispatch(product_profile: $product_profile, consumer_action: $consumer_action)->onQueue($queue);
        
        //Update process flag for consumer profile
        KlamoProfiler::setup()->ConsumerProfile()->flagForProcess($consumer_id, true);
        
        //Grab latest generic consumer profile as well and increment count if it exists
        $generic_consumer_profile = KlamoProfiler::setup()->GenericConsumerProfile()->getLatest();
        if(($generic_consumer_profile === null)){ return; }        
        IncrementActionCount::dispatch(product_profile: $product_profile, consumer_profile: $generic_consumer_profile, points: $consumer_action->weight)->onQueue($queue);

        $this->process($consumer_id);
        $this->processGeneric();
    }

    /**
     * Generic Product action
     *  Gathers data:
     *  - When a generic consumer
     *  - On a product with a profile in the system
     *  - Performs an action which is setup in the system
     * 
     * The data are gathered and saved via jobs in the system
     * 
     * @param product_id
     * @param consumer_action
     * 
     * @return void
     */
    public function genericProductAction(Int $product_id, String $consumer_action)
    {
        $product_profile = ProductProfile::where('product_id',$product_id)->first();
        $consumer_action = ConsumerAction::where('name',$consumer_action)->first();
        $generic_consumer_profile = GenericConsumerProfile::with('profilingTags', 'productProfiles')->latest()->first();
        
        //If any of the generic consumer/product profile or the consumer action with the given values are null, then return
        if(($product_profile === null) || ($generic_consumer_profile === null) || ($consumer_action === null)){
            //Log error
            return;
        }

        //Get queue
        $queue = Config::get('ProfilingSystem.queues.process');

        IncrementActionCount::dispatch(product_profile: $product_profile, consumer_profile: $generic_consumer_profile, points: $consumer_action->weight)->onQueue($queue);
        //Increment consumer counts for product profile and their enabled profiling tags
        IncrementConsumerCounts::dispatch(product_profile: $product_profile, consumer_action: $consumer_action)->onQueue($queue);

        $this->processGeneric();
    }

    // public function consumerTagAction()
    // {
    //     //TODO
    // }

    // public function genericTagActions()
    // {
    //     //TODO
    // }

    private function process($consumer_id)
    {
        //Get queue from config
        $queue = Config::get('ProfilingSystem.queues.process');

            Bus::batch([
                KlamoProfiler::process()->calculateConsumerProfilePoints($consumer_id),
                KlamoProfiler::process()->calculateConsumerProfileWeights($consumer_id),
                KlamoProfiler::process()->calculateProductPreferences($consumer_id),
            ])->then(function(Batch $batch) use ($consumer_id){
                //Update process flag for consumer profile if there were no failures
                if(!$batch->hasFailures()){
                    KlamoProfiler::setup()->ConsumerProfile()->flagForProcess($consumer_id, false);
                }
            }
            //Choose the queue on which the batch of jobs will run
            )->onQueue($queue) 
            ->dispatch();
    }

    private function processGeneric()
    {
        //Get queue from config
        $queue = Config::get('ProfilingSystem.queues.process');

        Bus::batch([
            KlamoProfiler::process()->calculateGenericConsumerProfilePoints(),
            KlamoProfiler::process()->calculateGenericConsumerProfileWeights(),
            KlamoProfiler::process()->calculateGenericProductPreferences(),
        ])
        //Choose the queue on which the batch of jobs will run
        ->onQueue($queue) 
        ->dispatch();
    }
}