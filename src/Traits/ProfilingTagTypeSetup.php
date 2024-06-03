<?php

namespace Klamo\ProfilingSystem\Traits;

use Illuminate\Support\Facades\Config;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;

trait ProfilingTagTypeSetup{

    public function profilingTagTypeSetup()
    {
        return KlamoProfiler::setup()->profilingTagType()->generate();
    }
}