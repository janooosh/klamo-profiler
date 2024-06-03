<?php

namespace Klamo\ProfilingSystem\Traits;

use Illuminate\Support\Facades\Config;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Illuminate\Support\Facades\Log;

trait ProductProfileSetup{

    public function productProfileSetup()
    {
        //Chunk size
        $chunk_size = config('ProfilingSystem.chunk_size',10);

        //Create an array of jobs
        $jobs = [];

        //Get product model from config and get products in chunks
        $product_class = Config::get('ProfilingSystem.models.product');
        if(!$product_class) {
            Log::error("Missing ProductClass in Config during productProfileSetup");
            return null;
        }

        $product_chunks = $product_class::where('is_published', true)->get()->chunk($chunk_size);
        foreach ($product_chunks as $product_chunk) {
            //Use the klamo profiler method to generate product profiles
            $job = KlamoProfiler::setup()->productProfile()->generate(products: $product_chunk);
            array_push($jobs, $job);
        }
        return $jobs;
    }
}