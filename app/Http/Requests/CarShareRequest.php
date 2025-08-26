<?php

namespace App\Http\Requests;

use App\Models\Car;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CarShareRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        // Получаем ID автомобиля из маршрута
        $carId = $this->route('id');
        if (!$carId) {
            return false;
        }

        // Проверяем права доступа к автомобилю через прямой запрос к базе
        $car = Car::find($carId);
        if (!$car) {
            return false;
        }

        // Проверяем, что автомобиль связан с текущим пользователем
        $user = auth()->user();
        return $user->cars()->where('car_id', $car->id)->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_email' => [
                'required',
                'email',
                'exists:users,email'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_email.required' => 'Email пользователя обязателен для заполнения',
            'user_email.email' => 'Введите корректный email адрес',
            'user_email.exists' => 'Пользователь с таким email не найден'
        ];
    }
}
