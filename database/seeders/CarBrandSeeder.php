<?php

namespace Database\Seeders;

use App\Models\CarBrand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            'Toyota',
            'Honda',
            'BMW',
            'Mercedes-Benz',
            'Audi',
            'Volkswagen',
            'Ford',
            'Nissan',
            'Hyundai',
            'Kia',
            'Mazda',
            'Chevrolet',
            'Subaru',
            'Lexus',
            'Volvo',
            'Peugeot',
            'Renault',
            'Skoda',
            'Mitsubishi',
            'Suzuki',
        ];

        foreach ($brands as $brand) {
            CarBrand::firstOrCreate(['name' => $brand]);
        }
    }
}
