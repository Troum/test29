<?php

namespace Database\Factories;

use App\Models\CarBrand;
use App\Models\CarModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CarModel>
 */
class CarModelFactory extends Factory
{
    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'car_brand_id' => CarBrand::factory(),
            'name' => $this->faker->unique()->bothify('Model-####'),
        ];
    }
}
