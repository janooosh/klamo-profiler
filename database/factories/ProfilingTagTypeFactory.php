<?php
namespace Klamo\ProfilingSystem\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Klamo\ProfilingSystem\Models\ProfilingTagType;

class ProfilingTagTypeFactory extends Factory
{
    protected $model = ProfilingTagType::class;

    public function definition()
    {
        return [
           'name' => $this->faker->unique()->asciify('*************************************'),
           'weight' => $this->faker->numberBetween(1,5),
        ];
    }
}