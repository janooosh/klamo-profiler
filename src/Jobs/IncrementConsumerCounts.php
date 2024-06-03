<?php

namespace Klamo\ProfilingSystem\Jobs;

use Throwable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Klamo\ProfilingSystem\Models\ConsumerAction;
use Klamo\ProfilingSystem\Models\ProductProfile;

class IncrementConsumerCounts implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ProductProfile $product_profile;
    public ConsumerAction $consumer_action;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ProductProfile $product_profile, ConsumerAction $consumer_action)
    {
        $this->product_profile = $product_profile;
        $this->consumer_action = $consumer_action;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch($this->consumer_action->name){
            case "VIEWED":
                $this->product_profile->viewed++;
                $this->product_profile->save();
                $this->incrementProfilingTagCounts('viewed');
                break;
            case "ADDED_TO_CART":
                $this->product_profile->added_to_cart++;
                $this->product_profile->save();
                $this->incrementProfilingTagCounts('added_to_cart');
                break;
            case "PURCHASED":
                $this->product_profile->purchased++;
                $this->product_profile->save();
                $this->incrementProfilingTagCounts('purchased');
                break;
            default:
                //TODO log attempt
                //Do we add points for other cases?
                break;
        }
    }

    public function failed(Throwable $exception)
    {
        //TODO Log failure
    }

    private function incrementProfilingTagCounts($column_name)
    {
        $item_profiling_tags = $this->product_profile->profilingTags()->where('enabled', 1)->get();

        foreach($item_profiling_tags as $item_profiling_tag){
            $item_profiling_tag->$column_name++;
            $item_profiling_tag->save();
        }
    }
}