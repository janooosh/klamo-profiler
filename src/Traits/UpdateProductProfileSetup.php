<?php

namespace Klamo\ProfilingSystem\Traits;

use Illuminate\Support\Facades\Config;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Illuminate\Support\Facades\Log;

trait UpdateProductProfileSetup{

    public function updateProductProfileSetup()
    {
        $chunk_size = config('ProfilingSystem.chunk_size',10);

        //Get product class from config and get all published products
        $product_class = Config::get('ProfilingSystem.models.product');
        if(!$product_class) {
            Log::error("Missing product class while updating productProfiles in updateProductProfileSetup");
            return null;
        }


        $product_chunks = $product_class::where('is_published', true)->get()->chunk($chunk_size);

        $jobs = [];
        foreach($product_chunks as $chunk) {
            $job = KlamoProfiler::setup()->ProductProfile()->updateWithProfilingTags($chunk);
            array_push($jobs, $job);
        }   
        
        return $jobs;
    }
}