<?php
namespace Klamo\ProfilingSystem\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Klamo\ProfilingSystem\Models\GenericConsumerProfile;

class GenericConsumerProfileFactory extends Factory
{
    protected $model = GenericConsumerProfile::class;

    public function definition()
    {
        return [
           'month' => $this->faker->month,
           'year' => $this->faker->year,
        ];
    }
}