<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarBrand;
use App\Models\CarModel;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $brands = CarBrand::with('carModels')->get();

        if ($brands->isEmpty()) {
            $this->command->error('Нет доступных брендов автомобилей. Запустите CarBrandSeeder и CarModelSeeder сначала.');
            return;
        }

        $colors = ['Белый', 'Черный', 'Серый', 'Красный', 'Синий', 'Зеленый', 'Желтый', 'Серебристый'];
        $years = ['2018-01-01', '2019-01-01', '2020-01-01', '2021-01-01', '2022-01-01', '2023-01-01', '2024-01-01'];

        foreach (range(1, 20) as $i) {
            $brand = $brands->random();

            if ($brand->carModels->isEmpty()) {
                continue;
            }

            $model = $brand->carModels->random();

            Car::create([
                'car_brand_id' => $brand->id,
                'car_model_id' => $model->id,
                'year' => $years[array_rand($years)],
                'color' => $colors[array_rand($colors)],
                'mileage' => rand(0, 200000),
            ]);
        }

        $this->command->info('Создано 20 автомобилей.');
    }
}
