<?php

namespace Klamo\ProfilingSystem\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;

trait ProfilingTagSetup{

    public function profilingTagSetup()
    {
        //Get profiling tag types
        $profiling_tag_types = Config::get('ProfilingSystem.models.profiling_tag_types');

        if(!$profiling_tag_types) {
            Log::error("Missing Profiling Tag Types in profilingTagSetup.");
            return null;
        }

        $jobs = [];

        foreach($profiling_tag_types as $profiling_tag_type){
            $job = KlamoProfiler::setup()->profilingTag()->generate(
                attribute_class: $profiling_tag_type['class'], 
                column_name: $profiling_tag_type['column'], 
                profiling_tag_type_name: $profiling_tag_type['type']);

            array_push($jobs, $job);
        }

        return $jobs;
    }
}