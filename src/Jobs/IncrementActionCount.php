<?php

namespace Klamo\ProfilingSystem\Jobs;

use Throwable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Klamo\ProfilingSystem\Models\ProductProfile;

class IncrementActionCount implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ProductProfile $product_profile;
    private $profile;
    private $points;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ProductProfile $product_profile, $consumer_profile, $points)
    {
        $this->product_profile = $product_profile;
        $this->profile = $consumer_profile;
        $this->points = $points;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $item_profiling_tags = $this->product_profile->profilingTags()->where('enabled', 1)->get();

        foreach($item_profiling_tags as $item_profiling_tag){
            $pivot = $this->profile->profilingTags->find($item_profiling_tag->id)->pivot;
            $pivot->increment('actions', $this->points);
        }
    }

    public function failed(Throwable $exception)
    {
        //TODO Log failure
    }
}