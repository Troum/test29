<?php

namespace Database\Seeders;

use App\Models\CarBrand;
use App\Models\CarModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modelsData = [
            'Toyota' => ['Camry', 'Corolla', 'RAV4', 'Prius', 'Highlander', 'Sienna', 'Tacoma', 'Tundra'],
            'Honda' => ['Civic', 'Accord', 'CR-V', 'Pilot', 'Odyssey', 'Fit', 'HR-V', 'Ridgeline'],
            'BMW' => ['3 Series', '5 Series', '7 Series', 'X3', 'X5', 'X7', 'i3', 'Z4'],
            'Mercedes-Benz' => ['C-Class', 'E-Class', 'S-Class', 'GLC', 'GLE', 'GLS', 'A-Class', 'CLA'],
            'Audi' => ['A3', 'A4', 'A6', 'A8', 'Q3', 'Q5', 'Q7', 'Q8'],
            'Volkswagen' => ['Golf', 'Jetta', 'Passat', 'Tiguan', 'Atlas', 'Arteon', 'ID.4', 'Beetle'],
            'Ford' => ['F-150', 'Mustang', 'Escape', 'Explorer', 'Expedition', 'Fusion', 'Edge', 'Bronco'],
            'Nissan' => ['Altima', 'Sentra', 'Rogue', 'Murano', 'Pathfinder', 'Titan', '370Z', 'Leaf'],
            'Hyundai' => ['Elantra', 'Sonata', 'Tucson', 'Santa Fe', 'Palisade', 'Genesis', 'Veloster', 'Kona'],
            'Kia' => ['Forte', 'Optima', 'Sorento', 'Sportage', 'Telluride', 'Stinger', 'Soul', 'Niro'],
        ];

        foreach ($modelsData as $brandName => $models) {
            $brand = CarBrand::where('name', $brandName)->first();
            
            if ($brand) {
                foreach ($models as $modelName) {
                    CarModel::firstOrCreate([
                        'car_brand_id' => $brand->id,
                        'name' => $modelName,
                    ]);
                }
            }
        }
    }
}
