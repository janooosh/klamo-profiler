<?php

namespace Klamo\ProfilingSystem\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Illuminate\Support\Facades\Config;

class KlamoProfilerProductProfileSetup extends Command{

    protected $signature = 'klamoProfiler:product-profile-setup';

    protected $description = 'Generic consumer profile setup for the Klamo Profiler system.';

    public function handle()
    {
        $answer = $this->confirm('Do you want to set the product profiles up?');
        if ($answer) {
            $this->setup();
        }
    }

    /**
     * This function creates a product profile for each product
     */
    private function setup()
    {
        //Chunk size
        $chunk_size = 20;

        //Get queue from config
        $queue = Config::get('ProfilingSystem.queues.setup');

        //Create a batch where jobs will be added
        $batch = Bus::batch([])->onQueue($queue)->dispatch();

        //Get product model from config and get products in chunks
        $product_class = Config::get('ProfilingSystem.models.product');

        $product_chunks = $product_class::where('is_published', true)->get()->chunk($chunk_size);
        foreach ($product_chunks as $product_chunk) {
            //Use the klamo profiler method to generate product profiles
            $job = KlamoProfiler::setup()->productProfile()->generate(products: $product_chunk);
            $batch->add($job);
        }
    }
} 