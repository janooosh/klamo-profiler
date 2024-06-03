<?php

namespace Klamo\ProfilingSystem\Jobs;

use Throwable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class CalculateConsumerProfilePoints implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $profile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($profile)
    {
        $this->profile = $profile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Get all the profiling tags of the updated user profile
        $profiling_tags = $this->profile->profilingTags;

        //Calculate points for each profiling tag
        foreach($profiling_tags as $profiling_tag){
            //$normalized_value = $profiling_tag->value
            $actions = $profiling_tag->pivot->actions;
            $profiling_tag_type_weight = $profiling_tag->profilingTagType->weight;
            //Encounter profiling tag weight_factor
            $profiling_tag_weight_factor = $profiling_tag->weight_factor;
            $points = $actions * $profiling_tag_type_weight * $profiling_tag_weight_factor;
            $this->profile->profilingTags()->updateExistingPivot($profiling_tag->id, [
                'points' => $points,
            ]);
        }
    }

    public function failed(Throwable $exception)
    {
        //TODO Log failure
    }
}