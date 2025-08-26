<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\CarBrand;
use App\Models\CarModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'car_brand_id' => CarBrand::factory(),
            'car_model_id' => CarModel::factory(),
            'year' => fake()->year(),
            'color' => fake()->colorName(),
            'mileage' => fake()->numberBetween(0, 300000),
        ];
    }
}
