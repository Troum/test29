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

        $carId = $this->route('id');
        if (!$carId) {
            return false;
        }

        $car = Car::find($carId);
        if (!$car) {
            return false;
        }

        $user = auth()->user();
        return $user->cars()->where('car_id', $car->id)->exists();
    }

    /**
     * @return array<string, mixed>
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
