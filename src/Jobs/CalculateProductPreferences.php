<?php

namespace Klamo\ProfilingSystem\Jobs;

use Throwable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CalculateProductPreferences implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $consumer_profile;
    public $product_profiles;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($consumer_profile, $product_profiles)
    {
        $this->consumer_profile = $consumer_profile;
        $this->product_profiles = $product_profiles;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $consumer_profile_weights = $this->consumer_profile->profilingTags()->pluck('weight')->toArray();

        foreach($this->product_profiles as $product_profile){
            $product_profile_weights = $product_profile->profilingTags()->pluck('enabled')->toArray();

            $new_array = array_map(function ($x, $y) {
                return $x*$y;
            }, $product_profile_weights, $consumer_profile_weights);

            $sum = array_sum($new_array);
            $this->consumer_profile->productProfiles()->updateExistingPivot($product_profile->id, [
                'preference' => $sum
            ]);
        }
    }

    public function failed(Throwable $exception)
    {
        //TODO Log failure
    }
}