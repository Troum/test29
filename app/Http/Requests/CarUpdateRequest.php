<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarUpdateRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        return [
            'car_brand_id' => 'sometimes|required|integer|exists:car_brands,id',
            'car_model_id' => 'sometimes|required|integer|exists:car_models,id',
            'year' => 'sometimes|nullable|date_format:Y',
            'color' => 'sometimes|nullable|string|max:50',
            'mileage' => 'sometimes|nullable|integer|min:0',
        ];
    }

    /**
     * @return array<string,string>
     */
    public function messages(): array
    {
        return [
            'car_brand_id.required' => 'Марка автомобиля обязательна для заполнения.',
            'car_brand_id.exists' => 'Выбранная марка автомобиля не существует.',
            'car_model_id.required' => 'Модель автомобиля обязательна для заполнения.',
            'car_model_id.exists' => 'Выбранная модель автомобиля не существует.',
            'year.date_format' => 'Год должен быть в формате YYYY.',
            'color.max' => 'Цвет не должен превышать 50 символов.',
            'mileage.integer' => 'Пробег должен быть числом.',
            'mileage.min' => 'Пробег не может быть отрицательным.',
        ];
    }
}
