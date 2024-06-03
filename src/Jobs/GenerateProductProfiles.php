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
use Klamo\ProfilingSystem\Models\ProductProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;

class GenerateProductProfiles implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $products;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($products)
    {
        $this->products = $products;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dispatcher = ProductProfile::getEventDispatcher();
        ProductProfile::unsetEventDispatcher();

        $profiling_tag_ids = ProfilingTag::pluck('id');

        $this->products->each(function($product) use($profiling_tag_ids){
            $product_profile = KlamoProfiler::setup()->productProfile()->create($product->id);

            //Add all profiling tags
            $product_profile->profilingTags()->sync($profiling_tag_ids); //CHANGED TO SYNC from swd
        });

        ProductProfile::setEventDispatcher($dispatcher);
    }

    public function failed(Throwable $exception)
    {
        //TODO Log failure
    }
}