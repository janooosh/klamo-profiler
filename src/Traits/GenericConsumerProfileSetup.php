<?php

namespace Klamo\ProfilingSystem\Traits;

use Illuminate\Support\Facades\Config;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;

trait GenericConsumerProfileSetup{

    public function genericConsumerProfileSetup()
    {
        return KlamoProfiler::setup()->genericConsumerProfile()->generate();
    }
}