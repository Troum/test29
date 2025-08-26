<?php

namespace App\Services;

use App\Models\Car;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Сервис для управления совместным использованием автомобилей
 * 
 * Обеспечивает функциональность:
 * - Проверка прав доступа к автомобилю
 * - Прикрепление/открепление автомобилей к пользователям
 * - Предоставление доступа к автомобилю другим пользователям
 * - Получение списка пользователей автомобиля
 */
class CarOwnershipService
{
    /**
     * Проверяет, имеет ли пользователь доступ к автомобилю
     *
     * @param Car $car Автомобиль для проверки
     * @param User|null $user Пользователь (если не указан, используется текущий)
     * @return bool true если у пользователя есть доступ
     */
    public function hasAccess(Car $car, ?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return false;
        }

        return $user->cars()->where('car_id', $car->id)->exists();
    }

    /**
     * @param Car $car
     * @param User|null $user
     * @return array
     */
    public function attachCar(Car $car, ?User $user = null): array
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ];
        }

        // Проверяем, не прикреплен ли уже автомобиль к этому пользователю
        if ($this->hasAccess($car, $user)) {
            return [
                'success' => false,
                'message' => 'Автомобиль уже добавлен к этому пользователю'
            ];
        }

        // Прикрепляем автомобиль к пользователю
        $user->cars()->attach($car->id);

        return [
            'success' => true,
            'message' => 'Автомобиль успешно добавлен'
        ];
    }

    /**
     * @param Car $car
     * @param User|null $user
     * @return array
     */
    public function detachCar(Car $car, ?User $user = null): array
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ];
        }

        if (!$this->hasAccess($car, $user)) {
            return [
                'success' => false,
                'message' => 'Автомобиль не найден или не привязан к пользователю'
            ];
        }

        $user->cars()->detach($car->id);

        return [
            'success' => true,
            'message' => 'Автомобиль успешно откреплен'
        ];
    }

    /**
     * @param Car $car
     * @param User $userToShare
     * @param User|null $owner
     * @return array
     */
    public function shareCar(Car $car, User $userToShare, ?User $owner = null): array
    {
        $owner = $owner ?? Auth::user();

        if (!$owner) {
            return [
                'success' => false,
                'message' => 'Владелец не авторизован'
            ];
        }

        if (!$this->hasAccess($car, $owner)) {
            return [
                'success' => false,
                'message' => 'Доступ запрещен. У вас нет прав на этот автомобиль.'
            ];
        }

        if ($this->hasAccess($car, $userToShare)) {
            return [
                'success' => false,
                'message' => 'Пользователь уже имеет доступ к этому автомобилю'
            ];
        }

        $car->users()->attach($userToShare->id);

        return [
            'success' => true,
            'message' => 'Автомобиль успешно предоставлен в пользование',
            'data' => [
                'shared_with' => [
                    'id' => $userToShare->id,
                    'name' => $userToShare->name,
                    'email' => $userToShare->email
                ]
            ]
        ];
    }

    /**
     * @param Car $car
     * @return array
     */
    public function getCarUsers(Car $car): array
    {
        $car->load(['users', 'carBrand', 'carModel']);

        return [
            'success' => true,
            'data' => [
                'car' => $car,
                'users' => $car->users
            ]
        ];
    }
}
