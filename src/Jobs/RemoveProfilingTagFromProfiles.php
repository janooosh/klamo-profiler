<?php

namespace Klamo\ProfilingSystem\Jobs;

use Throwable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RemoveProfilingTagFromProfiles implements ShouldQueue
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
        $this->profiling_tag_id = $profiling_tag_id;
        $this->profiles = $profiles;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach($this->profiles as $profile){
            $profile->profilingTags()->detach($this->profiling_tag_id);
        }
    }

    public function failed(Throwable $exception)
    {
        //TODO Log failure
    }
}