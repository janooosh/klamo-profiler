<?php
namespace Klamo\ProfilingSystem\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Klamo\ProfilingSystem\Models\ProfilingTagType;

class ProfilingTagFactory extends Factory
{
    protected $model = ProfilingTag::class;

    public function definition()
    {
        return [
           'name' => $this->faker->unique()->asciify('*************************************'),
           'profiling_tag_type_id' => 1, 
        ];
    }
}