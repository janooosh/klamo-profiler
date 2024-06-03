<?php

namespace Klamo\ProfilingSystem\Facades;

use Illuminate\Support\Facades\Facade;

class KlamoProfiler extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Klamo\ProfilingSystem\ProfilingSystem::class;
    }
}