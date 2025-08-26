<?php

namespace Tests\Unit\Services;

use App\Models\Car;
use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\User;
use App\Services\CarOwnershipService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CarOwnershipServiceTest extends TestCase
{
    use RefreshDatabase;

    private CarOwnershipService $ownershipService;
    private User $user;
    private User $otherUser;
    private Car $car;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->ownershipService = new CarOwnershipService();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        
        $carBrand = CarBrand::factory()->create();
        $carModel = CarModel::factory()->create(['car_brand_id' => $carBrand->id]);
        
        $this->car = Car::factory()->create([
            'car_brand_id' => $carBrand->id,
            'car_model_id' => $carModel->id,
        ]);
    }

    public function test_has_access_returns_true_when_user_has_car(): void
    {
        $this->user->cars()->attach($this->car->id);

        $result = $this->ownershipService->hasAccess($this->car, $this->user);

        $this->assertTrue($result);
    }

    public function test_has_access_returns_false_when_user_does_not_have_car(): void
    {
        $result = $this->ownershipService->hasAccess($this->car, $this->user);

        $this->assertFalse($result);
    }

    public function test_has_access_returns_false_when_user_is_null(): void
    {
        $result = $this->ownershipService->hasAccess($this->car, null);

        $this->assertFalse($result);
    }

    public function test_has_access_uses_auth_user_when_no_user_provided(): void
    {
        Auth::shouldReceive('user')->once()->andReturn($this->user);
        $this->user->cars()->attach($this->car->id);

        $result = $this->ownershipService->hasAccess($this->car);

        $this->assertTrue($result);
    }

    public function test_attach_car_success(): void
    {
        Auth::shouldReceive('user')->once()->andReturn($this->user);

        $result = $this->ownershipService->attachCar($this->car);

        $this->assertTrue($result['success']);
        $this->assertEquals('Автомобиль успешно добавлен', $result['message']);
        $this->assertDatabaseHas('user_car', [
            'user_id' => $this->user->id,
            'car_id' => $this->car->id,
        ]);
    }

    public function test_attach_car_fails_when_already_attached(): void
    {
        $this->user->cars()->attach($this->car->id);
        Auth::shouldReceive('user')->once()->andReturn($this->user);

        $result = $this->ownershipService->attachCar($this->car);

        $this->assertFalse($result['success']);
        $this->assertEquals('Автомобиль уже добавлен к этому пользователю', $result['message']);
    }

    public function test_attach_car_fails_when_user_not_authenticated(): void
    {
        Auth::shouldReceive('user')->once()->andReturn(null);

        $result = $this->ownershipService->attachCar($this->car);

        $this->assertFalse($result['success']);
        $this->assertEquals('Пользователь не авторизован', $result['message']);
    }

    public function test_detach_car_success(): void
    {
        $this->user->cars()->attach($this->car->id);
        Auth::shouldReceive('user')->once()->andReturn($this->user);

        $result = $this->ownershipService->detachCar($this->car);

        $this->assertTrue($result['success']);
        $this->assertEquals('Автомобиль успешно откреплен', $result['message']);
        $this->assertDatabaseMissing('user_car', [
            'user_id' => $this->user->id,
            'car_id' => $this->car->id,
        ]);
    }

    public function test_detach_car_fails_when_not_attached(): void
    {
        Auth::shouldReceive('user')->once()->andReturn($this->user);

        $result = $this->ownershipService->detachCar($this->car);

        $this->assertFalse($result['success']);
        $this->assertEquals('Автомобиль не найден или не привязан к пользователю', $result['message']);
    }

    public function test_share_car_success(): void
    {
        $this->user->cars()->attach($this->car->id);
        Auth::shouldReceive('user')->once()->andReturn($this->user);

        $result = $this->ownershipService->shareCar($this->car, $this->otherUser);

        $this->assertTrue($result['success']);
        $this->assertEquals('Автомобиль успешно предоставлен в пользование', $result['message']);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('shared_with', $result['data']);
        $this->assertEquals($this->otherUser->id, $result['data']['shared_with']['id']);
        
        $this->assertDatabaseHas('user_car', [
            'user_id' => $this->otherUser->id,
            'car_id' => $this->car->id,
        ]);
    }

    public function test_share_car_fails_when_owner_does_not_have_access(): void
    {
        Auth::shouldReceive('user')->once()->andReturn($this->user);

        $result = $this->ownershipService->shareCar($this->car, $this->otherUser);

        $this->assertFalse($result['success']);
        $this->assertEquals('Доступ запрещен. У вас нет прав на этот автомобиль.', $result['message']);
    }

    public function test_share_car_fails_when_user_already_has_access(): void
    {
        $this->user->cars()->attach($this->car->id);
        $this->otherUser->cars()->attach($this->car->id);
        Auth::shouldReceive('user')->once()->andReturn($this->user);

        $result = $this->ownershipService->shareCar($this->car, $this->otherUser);

        $this->assertFalse($result['success']);
        $this->assertEquals('Пользователь уже имеет доступ к этому автомобилю', $result['message']);
    }

    public function test_get_car_users_returns_correct_data(): void
    {
        $this->user->cars()->attach($this->car->id);
        $this->otherUser->cars()->attach($this->car->id);

        $result = $this->ownershipService->getCarUsers($this->car);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('car', $result['data']);
        $this->assertArrayHasKey('users', $result['data']);
        $this->assertCount(2, $result['data']['users']);
    }
}
