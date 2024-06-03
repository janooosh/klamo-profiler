<?php

namespace Klamo\ProfilingSystem\Jobs;

use Throwable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Klamo\ProfilingSystem\Models\ProfilingTag;

class DeleteAllProfilingTagsOfAType implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $profiling_tag_type_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($profiling_tag_type_id)
    {
        $this->profiling_tag_type_id = $profiling_tag_type_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $profiling_tags = ProfilingTag::where('profiling_tag_type_id', $this->profiling_tag_type_id)->get();

        foreach($profiling_tags as $profiling_tag){
            $profiling_tag->delete();
        }
    }

    public function failed(Throwable $exception)
    {
        //TODO Log failure
    }
}