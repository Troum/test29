<?php

namespace Tests\Feature\Cars;

use App\Models\Car;
use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private CarBrand $carBrand;
    private CarModel $carModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->carBrand = CarBrand::factory()->create(['name' => 'Toyota']);
        $this->carModel = CarModel::factory()->create([
            'car_brand_id' => $this->carBrand->id,
            'name' => 'Camry'
        ]);
    }

    private function authenticatedHeaders(): array
    {
        $token = $this->user->createToken('test-token')->plainTextToken;
        return ['Authorization' => 'Bearer ' . $token];
    }

    public function test_authenticated_user_can_create_car_with_all_fields(): void
    {
        $response = $this->withHeaders($this->authenticatedHeaders())
            ->postJson('/api/cars', [
                'car_brand_id' => $this->carBrand->id,
                'car_model_id' => $this->carModel->id,
                'year' => '2020',
                'color' => 'Красный',
                'mileage' => 50000,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Автомобиль успешно добавлен',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'car_brand_id',
                    'car_model_id',
                    'year',
                    'color',
                    'mileage',
                    'car_brand',
                    'car_model'
                ]
            ]);

        $this->assertDatabaseHas('cars', [
            'car_brand_id' => $this->carBrand->id,
            'car_model_id' => $this->carModel->id,
            'year' => '2020',
            'color' => 'Красный',
            'mileage' => 50000,
        ]);

        $car = Car::where('car_brand_id', $this->carBrand->id)
            ->where('car_model_id', $this->carModel->id)
            ->first();
        $this->assertDatabaseHas('user_car', [
            'user_id' => $this->user->id,
            'car_id' => $car->id,
        ]);
    }

    public function test_authenticated_user_can_create_car_with_minimal_fields(): void
    {
        $response = $this->withHeaders($this->authenticatedHeaders())
            ->postJson('/api/cars', [
                'car_brand_id' => $this->carBrand->id,
                'car_model_id' => $this->carModel->id,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Автомобиль успешно добавлен',
            ]);

        $this->assertDatabaseHas('cars', [
            'car_brand_id' => $this->carBrand->id,
            'car_model_id' => $this->carModel->id,
            'year' => null,
            'color' => null,
            'mileage' => null,
        ]);

        $car = Car::where('car_brand_id', $this->carBrand->id)
            ->where('car_model_id', $this->carModel->id)
            ->first();
        $this->assertDatabaseHas('user_car', [
            'user_id' => $this->user->id,
            'car_id' => $car->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_car(): void
    {
        $response = $this->postJson('/api/cars', [
            'car_brand_id' => $this->carBrand->id,
            'car_model_id' => $this->carModel->id,
        ]);

        $response->assertStatus(401);
    }

    public function test_car_creation_requires_mandatory_fields(): void
    {
        $response = $this->withHeaders($this->authenticatedHeaders())
            ->postJson('/api/cars', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['car_brand_id', 'car_model_id']);
    }

    public function test_car_creation_validates_foreign_keys(): void
    {
        $response = $this->withHeaders($this->authenticatedHeaders())
            ->postJson('/api/cars', [
                'car_brand_id' => 99999,
                'car_model_id' => 99999,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['car_brand_id', 'car_model_id']);
    }

    public function test_user_can_view_their_cars(): void
    {
        $car = Car::factory()->create([
            'car_brand_id' => $this->carBrand->id,
            'car_model_id' => $this->carModel->id,
        ]);

        $this->user->cars()->attach($car->id);

        $response = $this->withHeaders($this->authenticatedHeaders())
            ->getJson('/api/cars');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(1, 'data');
    }

    public function test_user_cannot_see_other_users_cars(): void
    {
        $otherUser = User::factory()->create();
        $car = Car::factory()->create([
            'car_brand_id' => $this->carBrand->id,
            'car_model_id' => $this->carModel->id,
        ]);

        $otherUser->cars()->attach($car->id);

        $response = $this->withHeaders($this->authenticatedHeaders())
            ->getJson('/api/cars');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(0, 'data');
    }

    public function test_user_can_view_specific_car(): void
    {
        $car = Car::factory()->create([
            'car_brand_id' => $this->carBrand->id,
            'car_model_id' => $this->carModel->id,
        ]);

        $this->user->cars()->attach($car->id);

        $response = $this->withHeaders($this->authenticatedHeaders())
            ->getJson("/api/cars/{$car->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $car->id,
                ]
            ]);
    }

    public function test_user_cannot_view_other_users_car(): void
    {
        $otherUser = User::factory()->create();
        $car = Car::factory()->create([
            'car_brand_id' => $this->carBrand->id,
            'car_model_id' => $this->carModel->id,
        ]);

        $otherUser->cars()->attach($car->id);

        $response = $this->withHeaders($this->authenticatedHeaders())
            ->getJson("/api/cars/{$car->id}");

        $response->assertStatus(403);
    }

    public function test_user_can_update_their_car(): void
    {
        $car = Car::factory()->create([
            'car_brand_id' => $this->carBrand->id,
            'car_model_id' => $this->carModel->id,
            'year' => '1978',
            'color' => 'Черный',
            'mileage' => 10000,
        ]);

        $this->user->cars()->attach($car->id);

        $response = $this->withHeaders($this->authenticatedHeaders())
            ->putJson("/api/cars/{$car->id}", [
                'year' => '2021',
                'color' => 'Красный',
                'mileage' => 15000,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Автомобиль успешно обновлен',
            ]);

        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'year' => '2021',
            'color' => 'Красный',
            'mileage' => 15000,
        ]);
    }

    public function test_user_cannot_update_other_users_car(): void
    {
        $otherUser = User::factory()->create();
        $car = Car::factory()->create([
            'car_brand_id' => $this->carBrand->id,
            'car_model_id' => $this->carModel->id,
        ]);

        $otherUser->cars()->attach($car->id);

        $response = $this->withHeaders($this->authenticatedHeaders())
            ->putJson("/api/cars/{$car->id}", [
                'color' => 'Красный',
            ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_car(): void
    {
        $car = Car::factory()->create([
            'car_brand_id' => $this->carBrand->id,
            'car_model_id' => $this->carModel->id,
        ]);

        $this->user->cars()->attach($car->id);

        $response = $this->withHeaders($this->authenticatedHeaders())
            ->deleteJson("/api/cars/{$car->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Автомобиль успешно удален',
            ]);

        $this->assertDatabaseMissing('cars', ['id' => $car->id]);
        $this->assertDatabaseMissing('user_car', [
            'user_id' => $this->user->id,
            'car_id' => $car->id,
        ]);
    }

    public function test_user_cannot_delete_other_users_car(): void
    {
        $otherUser = User::factory()->create();
        $car = Car::factory()->create([
            'car_brand_id' => $this->carBrand->id,
            'car_model_id' => $this->carModel->id,
        ]);

        $otherUser->cars()->attach($car->id);

        $response = $this->withHeaders($this->authenticatedHeaders())
            ->deleteJson("/api/cars/{$car->id}");

        $response->assertStatus(403);
    }

    public function test_car_endpoints_require_authentication(): void
    {
        $car = Car::factory()->create([
            'car_brand_id' => $this->carBrand->id,
            'car_model_id' => $this->carModel->id,
        ]);

        $this->user->cars()->attach($car->id);

        $this->getJson('/api/cars')->assertStatus(401);

        $this->getJson("/api/cars/{$car->id}")->assertStatus(401);

        $this->putJson("/api/cars/{$car->id}")->assertStatus(401);

        $this->deleteJson("/api/cars/{$car->id}")->assertStatus(401);
    }
}
