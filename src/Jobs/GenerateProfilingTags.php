<?php

namespace Klamo\ProfilingSystem\Jobs;

use Throwable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Klamo\ProfilingSystem\Models\ProfilingTag;

class GenerateProfilingTags implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $attribute_class;
    public $column_name;
    public $profiling_tag_type_name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($attribute_class, $column_name, $profiling_tag_type_name)
    {
        $this->attribute_class = $attribute_class;
        $this->column_name = $column_name;
        $this->profiling_tag_type_name = $profiling_tag_type_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dispatcher = ProfilingTag::getEventDispatcher();
        ProfilingTag::unsetEventDispatcher();

        $profiling_tag_type_name = $this->profiling_tag_type_name;
        $attribute = new $this->attribute_class;
        $attribute->pluck($this->column_name)->each(function($item) use($profiling_tag_type_name) {
            KlamoProfiler::setup()->ProfilingTag()->create(profiling_tag_name: $item, profiling_tag_type_name: $profiling_tag_type_name );

            //TODO Log creation
        });

        ProfilingTag::setEventDispatcher($dispatcher);
    }

    public function failed(Throwable $exception)
    {
        //TODO Log failure
    }
}