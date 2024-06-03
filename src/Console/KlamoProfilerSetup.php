<?php

namespace Klamo\ProfilingSystem\Console;

use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Klamo\ProfilingSystem\Traits\ConsumerProfileSetup;
use Klamo\ProfilingSystem\Traits\GenericConsumerProfileSetup;
use Klamo\ProfilingSystem\Traits\ProductProfileSetup;
use Klamo\ProfilingSystem\Traits\ProfilingTagSetup;
use Klamo\ProfilingSystem\Traits\ProfilingTagTypeSetup;
use Klamo\ProfilingSystem\Traits\UpdateProductProfileSetup;

class KlamoProfilerSetup extends Command{

    use ProductProfileSetup;
    use ConsumerProfileSetup;
    use UpdateProductProfileSetup;
    use ProfilingTagTypeSetup;
    use ProfilingTagSetup;
    use GenericConsumerProfileSetup;

    protected $signature = 'klamoProfiler:setup';

    protected $description = 'Initial setup for the Klamo Profiler system. Generates data necessary for the Klamo profiling system.';

    //jobs_easy -> Jobs that run fast
    private $jobs = [];

    //jobs_heavy -> Jobs that are heavy

    public function handle()
    {
        //TODO Basic sequence for system setup   

        $queue = config('ProfilingSystem.queues.setup','ProfilingSetup');
        $this->info("Queue set to ".$queue);

        //Call migration command
        $this->call('klamoProfiler:migrate');

        //Call seed command
        $this->call('klamoProfiler:seed');
        
        //Create an array of jobs -> will be filled throughout 

        //ProfilingTagTypes
        $profiling_tag_types_job = $this->profilingTagTypeSetup();
        $this->addJob($profiling_tag_types_job,"Profiling Tag Types");

        //ProfilingTags
        $profiling_tag_jobs = $this->profilingTagSetup();
        $this->addJobs($profiling_tag_jobs);

        //ProductProfile
        $product_profile_jobs = $this->productProfileSetup();
        $this->addJobs($product_profile_jobs);

        //ConsumerProfile
        $consumer_profile_jobs = $this->consumerProfileSetup();
        $this->addJobs($consumer_profile_jobs);

        //GenericConsumerProfile
        $generic_consumer_profile_job = $this->genericConsumerProfileSetup();
        $this->addJob($generic_consumer_profile_job);

        //UpdateProductProfiles
        $update_product_profile_jobs = $this->updateProductProfileSetup();
        $this->addJobs($update_product_profile_jobs);

        //BATCH
        Bus::batch($this->jobs)->onQueue($queue)->dispatch();
        
        $this->info("Done");
    }

    private function addJob($job,$description = "unknown")
    {
        if(!is_null($job)) {
            array_push($this->jobs,$job);
        }
        else {
            Log::error("Trying to pass null to the jobs array. ".$description);
            $this->error("Can not add a job for ".$description);
        }
    }

    private function addJobs($jobs,$description="unknown")
    {
        if(!empty($jobs)) {
            foreach($jobs as $job) {
                $this->addJob($job);
            }
        }
        else {
            Log::error("Empty Job passed to addJobs in setup of ProfilingSystem. ".$description);
            $this->error("Can not add Jobs for ".$description);
        }
    }
}