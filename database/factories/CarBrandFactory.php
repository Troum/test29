<?php

namespace Database\Factories;

use App\Models\CarBrand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CarBrand>
 */
class CarBrandFactory extends Factory
{
    /**
     * @return array
     */
    public function definition(): array
    {
        $brands = [
            'Toyota', 'Honda', 'BMW', 'Mercedes-Benz', 'Audi',
            'Volkswagen', 'Ford', 'Nissan', 'Hyundai', 'Kia',
            'Mazda', 'Chevrolet', 'Subaru', 'Lexus', 'Volvo'
        ];

        return [
            'name' => $this->faker->unique()->randomElement($brands),
        ];
    }
}
