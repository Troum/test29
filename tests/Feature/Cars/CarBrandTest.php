<?php

namespace Tests\Feature\Cars;

use App\Models\CarBrand;
use App\Models\CarModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarBrandTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_list_of_car_brands(): void
    {
        $brands = CarBrand::factory()->count(3)->create();

        $response = $this->getJson('/api/car-brands');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'created_at', 'updated_at']
                ]
            ]);
    }

    public function test_car_brands_endpoint_is_public(): void
    {
        CarBrand::factory()->create(['name' => 'Toyota']);

        $response = $this->getJson('/api/car-brands');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_can_get_list_of_car_models(): void
    {
        $brand = CarBrand::factory()->create(['name' => 'Toyota']);
        CarModel::factory()->count(3)->create([
            'car_brand_id' => $brand->id
        ]);

        $response = $this->getJson('/api/car-models');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'car_brand_id',
                        'name',
                        'created_at',
                        'updated_at',
                        'car_brand'
                    ]
                ]
            ]);
    }

    public function test_can_filter_car_models_by_brand(): void
    {
        $toyotaBrand = CarBrand::factory()->create(['name' => 'Toyota']);
        $hondaBrand = CarBrand::factory()->create(['name' => 'Honda']);

        CarModel::factory()->count(2)->create([
            'car_brand_id' => $toyotaBrand->id
        ]);
        CarModel::factory()->count(3)->create([
            'car_brand_id' => $hondaBrand->id
        ]);

        $response = $this->getJson("/api/car-models?car_brand_id={$toyotaBrand->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(2, 'data');

        $responseData = $response->json('data');
        foreach ($responseData as $model) {
            $this->assertEquals($toyotaBrand->id, $model['car_brand_id']);
        }
    }

    public function test_car_models_without_filter_returns_all_models(): void
    {
        $brand1 = CarBrand::factory()->create();
        $brand2 = CarBrand::factory()->create();

        CarModel::factory()->count(2)->create(['car_brand_id' => $brand1->id]);
        CarModel::factory()->count(3)->create(['car_brand_id' => $brand2->id]);

        $response = $this->getJson('/api/car-models');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_car_models_endpoint_is_public(): void
    {
        $brand = CarBrand::factory()->create();
        CarModel::factory()->create(['car_brand_id' => $brand->id]);

        $response = $this->getJson('/api/car-models');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_car_models_includes_brand_relationship(): void
    {
        $brand = CarBrand::factory()->create(['name' => 'Toyota']);
        CarModel::factory()->create([
            'car_brand_id' => $brand->id,
            'name' => 'Camry'
        ]);

        $response = $this->getJson('/api/car-models');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.name', 'Camry')
            ->assertJsonPath('data.0.car_brand.id', $brand->id)
            ->assertJsonPath('data.0.car_brand.name', 'Toyota');
    }

    public function test_empty_car_brands_returns_empty_array(): void
    {
        $response = $this->getJson('/api/car-brands');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => []
            ]);
    }

    public function test_empty_car_models_returns_empty_array(): void
    {
        $response = $this->getJson('/api/car-models');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => []
            ]);
    }

    public function test_invalid_brand_filter_returns_empty_results(): void
    {
        $brand = CarBrand::factory()->create();
        CarModel::factory()->create(['car_brand_id' => $brand->id]);

        $response = $this->getJson('/api/car-models?car_brand_id=99999');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => []
            ]);
    }
}
