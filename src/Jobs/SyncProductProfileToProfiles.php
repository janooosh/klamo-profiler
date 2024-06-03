<?php

namespace Klamo\ProfilingSystem\Jobs;

use Throwable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncProductProfileToProfiles implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $product_profile_id;
    private $profiles;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($product_profile_id, $profiles)
    {
        $this->profiles = $profiles;
        $this->product_profile_id = $product_profile_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Sync new product profile
        foreach ($this->profiles as $profile) {
            $profile->productProfiles()->syncWithoutDetaching($this->product_profile_id);
        }
    }

    public function failed(Throwable $exception)
    {
        //TODO Log failure
    }
}