<?php

namespace Tests\Unit\Services;

use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = $this->app->make(AuthService::class);
    }

    public function test_register_creates_user_successfully(): void
    {
        $registerDTO = new RegisterDTO(
            name: 'Test User',
            email: 'test@example.com',
            password: Hash::make('password123')
        );

        $result = $this->authService->register($registerDTO);

        $this->assertTrue($result['success']);
        $this->assertEquals('Регистрация прошла успешно', $result['message']);
        $this->assertArrayHasKey('user', $result['data']);
        $this->assertArrayHasKey('token', $result['data']);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
    }

    public function test_register_handles_duplicate_email(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $registerDTO = new RegisterDTO(
            name: 'Test User',
            email: 'test@example.com',
            password: Hash::make('password123')
        );

        $result = $this->authService->register($registerDTO);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Ошибка при регистрации', $result['message']);
    }

    public function test_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        $loginDTO = new LoginDTO(
            email: 'test@example.com',
            password: 'password123'
        );

        $result = $this->authService->login($loginDTO);

        $this->assertTrue($result['success']);
        $this->assertEquals('Вход выполнен успешно', $result['message']);
        $this->assertArrayHasKey('user', $result['data']);
        $this->assertArrayHasKey('token', $result['data']);
        $this->assertEquals($user->id, $result['data']['user']['id']);
    }

    public function test_login_with_invalid_email(): void
    {
        $loginDTO = new LoginDTO(
            email: 'nonexistent@example.com',
            password: 'password123'
        );

        $result = $this->authService->login($loginDTO);

        $this->assertFalse($result['success']);
        $this->assertEquals('Пользователь с таким email не найден', $result['message']);
    }

    public function test_login_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correct_password')
        ]);

        $loginDTO = new LoginDTO(
            email: 'test@example.com',
            password: 'wrong_password'
        );

        $result = $this->authService->login($loginDTO);

        $this->assertFalse($result['success']);
        $this->assertEquals('Неверный пароль', $result['message']);
    }

    public function test_login_removes_old_tokens(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        $user->createToken('old_token');
        $this->assertCount(1, $user->tokens);

        $loginDTO = new LoginDTO(
            email: 'test@example.com',
            password: 'password123'
        );

        $result = $this->authService->login($loginDTO);

        $this->assertTrue($result['success']);

        $user->refresh();
        $this->assertCount(1, $user->tokens);
    }

    public function test_logout_removes_user_tokens(): void
    {
        $user = User::factory()->create();
        $user->createToken('test_token');

        Auth::shouldReceive('user')->andReturn($user);

        $result = $this->authService->logout();

        $this->assertTrue($result['success']);
        $this->assertEquals('Выход выполнен успешно', $result['message']);

        $user->refresh();
        $this->assertCount(0, $user->tokens);
    }

    public function test_logout_fails_when_user_not_authenticated(): void
    {
        Auth::shouldReceive('user')->andReturn(null);

        $result = $this->authService->logout();

        $this->assertFalse($result['success']);
        $this->assertEquals('Пользователь не авторизован', $result['message']);
    }

    public function test_me_returns_user_data(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);

        Auth::shouldReceive('user')->andReturn($user);

        $result = $this->authService->me();

        $this->assertTrue($result['success']);
        $this->assertEquals('Данные пользователя получены успешно', $result['message']);
        $this->assertEquals($user->id, $result['data']['user']['id']);
        $this->assertEquals('Test User', $result['data']['user']['name']);
        $this->assertEquals('test@example.com', $result['data']['user']['email']);
    }

    public function test_me_fails_when_user_not_authenticated(): void
    {
        Auth::shouldReceive('user')->andReturn(null);

        $result = $this->authService->me();

        $this->assertFalse($result['success']);
        $this->assertEquals('Пользователь не авторизован', $result['message']);
    }
}
