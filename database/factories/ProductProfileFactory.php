<?php
namespace Klamo\ProfilingSystem\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Klamo\ProfilingSystem\Models\ProductProfile;

class ProductProfileFactory extends Factory
{
    protected $model = ProductProfile::class;

    public function definition()
    {
        return [
           'product_id' => $this->faker->unique()->randomNumber(),
        ];
    }
}