<?php

namespace App\Services;

use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

readonly class AuthService
{
    /**
     * @param UserService $userService
     */
    public function __construct(
        private UserService $userService
    ) {
    }
    /**
     * @param RegisterDTO $registerDTO
     * @return array
     */
    public function register(RegisterDTO $registerDTO): array
    {
        try {
            $user = $this->userService->createOne($registerDTO->toArray());

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'success' => true,
                'message' => 'Регистрация прошла успешно',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'token' => $token,
                ],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Ошибка при регистрации: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * @param LoginDTO $loginDTO
     * @return array
     */
    public function login(LoginDTO $loginDTO): array
    {
        try {
            $user = $this->userService->findByEmail($loginDTO->email);

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Пользователь с таким email не найден',
                    'data' => [],
                ];
            }

            if (!Hash::check($loginDTO->password, $user->password)) {
                return [
                    'success' => false,
                    'message' => 'Неверный пароль',
                    'data' => [],
                ];
            }

            $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'success' => true,
                'message' => 'Вход выполнен успешно',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'token' => $token,
                ],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Ошибка при входе: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * @return array
     */
    public function logout(): array
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Пользователь не авторизован',
                    'data' => [],
                ];
            }

            $user->tokens()->delete();

            return [
                'success' => true,
                'message' => 'Выход выполнен успешно',
                'data' => [],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Ошибка при выходе: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * @return array
     */
    public function me(): array
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Пользователь не авторизован',
                    'data' => [],
                ];
            }

            return [
                'success' => true,
                'message' => 'Данные пользователя получены успешно',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                ],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Ошибка при получении данных: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }
}
