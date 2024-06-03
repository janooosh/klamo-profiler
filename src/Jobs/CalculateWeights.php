<?php

namespace Klamo\ProfilingSystem\Jobs;

use Throwable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CalculateWeights implements ShouldQueue
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
        
        //Get the sum of all points
        $sum = $profiling_tags->sum('pivot.points');

        //Calculate weight for each profiling tag
        foreach($profiling_tags as $profiling_tag){
            //$normalized_value = $profiling_tag->value
            $weight = (int) ($profiling_tag->pivot->points/$sum * 10000);
            
            $this->profile->profilingTags()->updateExistingPivot($profiling_tag->id, [
                'weight' => $weight,
            ]);
        }
    }

    public function failed(Throwable $exception)
    {
        //TODO Log failure
    }
}