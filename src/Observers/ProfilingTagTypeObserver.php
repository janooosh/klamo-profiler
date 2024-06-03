<?php

namespace Klamo\ProfilingSystem\Observers;

use Illuminate\Support\Facades\Bus;
use Klamo\ProfilingSystem\Jobs\DeleteAllProfilingTagsOfAType;
use Klamo\ProfilingSystem\Models\ProfilingTagType;

class ProfilingTagTypeObserver{

    /**
     * Handle the ProfilingTagType "created" event.
     *
     * @param  \Klamo\ProfilingSystem\Models\ProfilingTagType  $profiling_tag_type
     * @return void
     */
    public function created(ProfilingTagType $profiling_tag_type)
    {
        //
    }

    /**
     * Handle the ProfilingTagType "deleted" event.
     *
     * @param  \Klamo\ProfilingSystem\Models\ProfilingTagType  $profiling_tag_type
     * @return void
     */
    public function deleted(ProfilingTagType $profiling_tag_type)
    {
        // Create a batch of jobs which will remove all profiling tags that hold the deleted profiling tag type
        DeleteAllProfilingTagsOfAType::dispatch(profiling_tag_type_id: $profiling_tag_type->id)->onQueue('ProfilingSetup');
    }
}