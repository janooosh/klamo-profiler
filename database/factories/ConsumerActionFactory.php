<?php
namespace Klamo\ProfilingSystem\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Klamo\ProfilingSystem\Models\ConsumerAction;

class ConsumerActionFactory extends Factory
{
    protected $model = ConsumerAction::class;

    public function definition()
    {
        return [
           'name' => $this->faker->unique()->asciify('*************************************'),
           'weight' => $this->faker->randomNumber(3),
        ];
    }
}