<?php
namespace Klamo\ProfilingSystem\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Klamo\ProfilingSystem\Models\ConsumerProfile;

class ConsumerProfileFactory extends Factory
{
    protected $model = ConsumerProfile::class;

    public function definition()
    {
        return [
           'consumer_id' => $this->faker->unique()->randomNumber(),
        ];
    }
}