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

class SyncProductProfilesToProfile implements ShouldQueue
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
        //Add all product profiles
        $product_profile_ids = ProductProfile::pluck('id');
        $this->profile->productProfiles()->syncWithoutDetaching($product_profile_ids);
    }

    public function failed(Throwable $exception)
    {
        //TODO Log failure
    }
}