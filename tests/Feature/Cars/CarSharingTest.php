<?php

namespace Tests\Feature\Cars;

use App\Models\Car;
use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarSharingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $otherUser;
    private CarBrand $carBrand;
    private CarModel $carModel;
    private Car $car;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->carBrand = CarBrand::factory()->create(['name' => 'Toyota']);
        $this->carModel = CarModel::factory()->create([
            'car_brand_id' => $this->carBrand->id,
            'name' => 'Camry'
        ]);

        $this->car = Car::factory()->create([
            'car_brand_id' => $this->carBrand->id,
            'car_model_id' => $this->carModel->id,
        ]);

        $this->user->cars()->attach($this->car->id);
    }

    private function authenticatedHeaders(User $user = null): array
    {
        $user = $user ?? $this->user;
        $token = $user->createToken('test-token')->plainTextToken;
        return ['Authorization' => 'Bearer ' . $token];
    }

    public function test_user_can_attach_car(): void
    {
        $newCar = Car::factory()->create([
            'car_brand_id' => $this->carBrand->id,
            'car_model_id' => $this->carModel->id,
        ]);

        $response = $this->withHeaders($this->authenticatedHeaders())
            ->postJson("/api/cars/{$newCar->id}/attach");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Автомобиль успешно добавлен'
            ]);

        $this->assertDatabaseHas('user_car', [
            'user_id' => $this->user->id,
            'car_id' => $newCar->id,
        ]);
    }

    public function test_user_cannot_attach_same_car_twice(): void
    {
        $response = $this->withHeaders($this->authenticatedHeaders())
            ->postJson("/api/cars/{$this->car->id}/attach");

        $response->assertStatus(409)
            ->assertJson([
                'success' => false,
                'message' => 'Автомобиль уже добавлен к этому пользователю'
            ]);
    }

    public function test_user_can_detach_car(): void
    {
        $response = $this->withHeaders($this->authenticatedHeaders())
            ->deleteJson("/api/cars/{$this->car->id}/detach");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Автомобиль успешно откреплен'
            ]);

        $this->assertDatabaseMissing('user_car', [
            'user_id' => $this->user->id,
            'car_id' => $this->car->id,
        ]);
    }

    public function test_user_cannot_detach_unattached_car(): void
    {
        $newCar = Car::factory()->create([
            'car_brand_id' => $this->carBrand->id,
            'car_model_id' => $this->carModel->id,
        ]);

        $response = $this->withHeaders($this->authenticatedHeaders())
            ->deleteJson("/api/cars/{$newCar->id}/detach");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Автомобиль не найден или не привязан к пользователю'
            ]);
    }

    public function test_user_can_get_car_users(): void
    {
        $this->car->users()->attach($this->otherUser->id);

        $response = $this->withHeaders($this->authenticatedHeaders())
            ->getJson("/api/cars/{$this->car->id}/users");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'car' => [
                        'id' => $this->car->id
                    ]
                ]
            ])
            ->assertJsonCount(2, 'data.users');
    }

    public function test_user_can_share_car_with_another_user(): void
    {
        $response = $this->withHeaders($this->authenticatedHeaders())
            ->postJson("/api/cars/{$this->car->id}/share", [
                'user_email' => $this->otherUser->email
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Автомобиль успешно предоставлен в пользование',
                'data' => [
                    'shared_with' => [
                        'id' => $this->otherUser->id,
                        'email' => $this->otherUser->email
                    ]
                ]
            ]);

        $this->assertDatabaseHas('user_car', [
            'user_id' => $this->otherUser->id,
            'car_id' => $this->car->id,
        ]);
    }

    public function test_user_cannot_share_car_with_nonexistent_user(): void
    {
        $response = $this->withHeaders($this->authenticatedHeaders())
            ->postJson("/api/cars/{$this->car->id}/share", [
                'user_email' => 'nonexistent@example.com'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user_email']);
    }

    public function test_user_cannot_share_car_with_user_who_already_has_access(): void
    {
        $this->car->users()->attach($this->otherUser->id);

        $response = $this->withHeaders($this->authenticatedHeaders())
            ->postJson("/api/cars/{$this->car->id}/share", [
                'user_email' => $this->otherUser->email
            ]);

        $response->assertStatus(409)
            ->assertJson([
                'success' => false,
                'message' => 'Пользователь уже имеет доступ к этому автомобилю'
            ]);
    }

    public function test_other_user_cannot_share_car(): void
    {
        $response = $this->withHeaders($this->authenticatedHeaders($this->otherUser))
            ->postJson("/api/cars/{$this->car->id}/share", [
                'user_email' => 'someone@example.com'
            ]);

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_attach_car(): void
    {
        $response = $this->postJson("/api/cars/{$this->car->id}/attach");
        $response->assertStatus(401);
    }

    public function test_unauthenticated_user_cannot_detach_car(): void
    {
        $response = $this->deleteJson("/api/cars/{$this->car->id}/detach");
        $response->assertStatus(401);
    }

    public function test_unauthenticated_user_cannot_get_car_users(): void
    {
        $response = $this->getJson("/api/cars/{$this->car->id}/users");
        $response->assertStatus(401);
    }

    public function test_unauthenticated_user_cannot_share_car(): void
    {
        $response = $this->postJson("/api/cars/{$this->car->id}/share", [
            'user_email' => $this->otherUser->email
        ]);
        $response->assertStatus(401);
    }
}
