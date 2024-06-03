<?php

namespace Klamo\ProfilingSystem\Jobs;

use Throwable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncProfilingTagToProfiles implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $profiling_tag_id;
    private $profiles;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($profiling_tag_id, $profiles)
    {
        $this->profiles = $profiles;
        $this->profiling_tag_id = $profiling_tag_id;
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
            $profile->profilingTags()->syncWithoutDetaching($this->profiling_tag_id);
        }
    }

    public function failed(Throwable $exception)
    {
        //TODO Log failure
    }
}