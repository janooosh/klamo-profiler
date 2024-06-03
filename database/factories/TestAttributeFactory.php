<?php
namespace Klamo\ProfilingSystem\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Klamo\ProfilingSystem\Models\TestAttribute;

class TestAttributeFactory extends Factory
{
    protected $model = TestAttribute::class;

    public function definition()
    {
        return [
           'name' => $this->faker->unique()->text(20),
        ];
    }
}