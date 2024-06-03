<?php

namespace Klamo\ProfilingSystem\Database\Seeds;

use Illuminate\Database\Seeder;
use Klamo\ProfilingSystem\Models\ProfilingTag;

class RandomableTableSeeder extends Seeder
{
    public function run()
    {
        /** Create a profiling tag 
        *   with name trending 
        *   with type id for trending
        */
        $dispatcher = ProfilingTag::getEventDispatcher();
        ProfilingTag::unsetEventDispatcher();

        ProfilingTag::create([
            'name' => 'trending',
            'type_id' => ProfilingTag::getTypeID('trending'),
        ]);

        ProfilingTag::setEventDispatcher($dispatcher);
    }
}