<?php

namespace Klamo\ProfilingSystem\Jobs;

use Throwable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Klamo\ProfilingSystem\Models\ProductProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Klamo\ProfilingSystem\Models\ProfilingTagType;

use function PHPUnit\Framework\isEmpty;

class UpdateProfilingTagStatus implements ShouldQueue
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
        //Get all profiling tag types from config
        $profiling_tag_types = Config::get('ProfilingSystem.models.profiling_tag_types');

        foreach ($this->products as $product) {

            //Grab product profile
            $product_profile = KlamoProfiler::setup()->ProductProfile()->read($product->id);
            
            if(is_null($product_profile)){

                Log::info("Error during updating product profile tags.");
                Log::info("A product profile does not exist for product with id: $product->id");
                continue;
                //TO DO: Call some method that creates a new product Profile and then triggers the relationships through the observers (separate queue, but just to have the profile in case some creation method failed)
            }

            //Disable all tags
            $product_profile->profilingTags()
                            ->newPivotStatement()
                            ->where('product_profile_id', '=', $product_profile->id)
                            ->update([
                                'enabled' => 0,
                            ]);

            //Enable all active tags
            foreach ($profiling_tag_types as $profiling_tag_type) {
                
                //Grab relationship from product
                $product_relationship = $profiling_tag_type['type'];
                $relationship = $product->$product_relationship;
                
                if(is_null($relationship) || !isEmpty($relationship)){
                    continue;
                }

                //Assign profiling tag type name
                $profiling_tag_type_model = KlamoProfiler::setup()->ProfilingTagType()->read($profiling_tag_type['type']);

                if($profiling_tag_type['relationship'] === 'one'){
                    $column_name = $profiling_tag_type['column'];
                    $tag_name = $relationship->$column_name;

                    $this->enableTag($tag_name, $profiling_tag_type_model, $product_profile);
                    continue;
                }
                //Grab labels from collection
                $tag_names = $relationship->pluck($profiling_tag_type['column']);
                

                //Use product id, profiling tag type name and the labels to update product profile with profiling tags
                foreach($tag_names as $tag_name){

                    $this->enableTag($tag_name, $profiling_tag_type_model, $product_profile);
                    
                }
            }
        }
    }

    private function enableTag($tag_name, $profiling_tag_type_model, $product_profile)
    {
        //For each tag name, find a profiling tag with the name and the profiling tag type
        $profiling_tag = ProfilingTag::where('name', $tag_name)->where('profiling_tag_type_id', $profiling_tag_type_model->id)->first();
                    
        //If it does not exist, log it
        if(!$profiling_tag){
            Log::debug("Attempt to update a product profile with non-existent profiling tag: $tag_name");
            return;
        }
        
        //If it does exist, enable the profiling tag on the product profile
        $product_profile->profilingTags()->updateExistingPivot($profiling_tag->id, [
            'enabled' => 1,
        ]);
    }

    public function failed(Throwable $exception)
    {
        //TODO Log failure
    }
}