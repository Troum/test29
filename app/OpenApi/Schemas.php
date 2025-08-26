<?php

namespace App\OpenApi;

/**
 * @OA\Schema(
 *     schema="Car",
 *     type="object",
 *     title="Автомобиль",
 *     description="Модель автомобиля",
 *     @OA\Property(property="id", type="integer", example=1, description="ID автомобиля"),
 *     @OA\Property(property="car_brand_id", type="integer", example=1, description="ID бренда"),
 *     @OA\Property(property="car_model_id", type="integer", example=1, description="ID модели"),
 *     @OA\Property(property="year", type="string", example="2020", nullable=true, description="Год выпуска"),
 *     @OA\Property(property="color", type="string", example="Красный", nullable=true, description="Цвет"),
 *     @OA\Property(property="mileage", type="integer", example=50000, nullable=true, description="Пробег"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Дата создания"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Дата обновления"),
 *     @OA\Property(
 *         property="car_brand",
 *         ref="#/components/schemas/CarBrand",
 *         description="Бренд автомобиля"
 *     ),
 *     @OA\Property(
 *         property="car_model",
 *         ref="#/components/schemas/CarModel",
 *         description="Модель автомобиля"
 *     ),
 *     @OA\Property(
 *         property="users",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/User"),
 *         description="Пользователи, связанные с автомобилем"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="CarBrand",
 *     type="object",
 *     title="Бренд автомобиля",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Toyota"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="CarModel",
 *     type="object",
 *     title="Модель автомобиля",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="car_brand_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Camry"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="car_brand",
 *         ref="#/components/schemas/CarBrand",
 *         description="Бренд автомобиля"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="Пользователь",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Иван Иванов"),
 *     @OA\Property(property="email", type="string", format="email", example="ivan@example.com"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="CarCreateRequest",
 *     type="object",
 *     title="Запрос создания автомобиля",
 *     required={"car_brand_id", "car_model_id"},
 *     @OA\Property(property="car_brand_id", type="integer", example=1, description="ID бренда автомобиля"),
 *     @OA\Property(property="car_model_id", type="integer", example=1, description="ID модели автомобиля"),
 *     @OA\Property(property="year", type="string", example="2020", nullable=true, description="Год выпуска"),
 *     @OA\Property(property="color", type="string", example="Красный", nullable=true, description="Цвет"),
 *     @OA\Property(property="mileage", type="integer", example=50000, nullable=true, description="Пробег")
 * )
 *
 * @OA\Schema(
 *     schema="CarUpdateRequest",
 *     type="object",
 *     title="Запрос обновления автомобиля",
 *     @OA\Property(property="car_brand_id", type="integer", example=1, nullable=true, description="ID бренда автомобиля"),
 *     @OA\Property(property="car_model_id", type="integer", example=1, nullable=true, description="ID модели автомобиля"),
 *     @OA\Property(property="year", type="string", example="2021", nullable=true, description="Год выпуска"),
 *     @OA\Property(property="color", type="string", example="Синий", nullable=true, description="Цвет"),
 *     @OA\Property(property="mileage", type="integer", example=60000, nullable=true, description="Пробег")
 * )
 *
 * @OA\Schema(
 *     schema="ApiResponse",
 *     type="object",
 *     title="Стандартный ответ API",
 *     @OA\Property(property="success", type="boolean", example=true, description="Успешность операции"),
 *     @OA\Property(property="message", type="string", example="Операция выполнена успешно", description="Сообщение"),
 *     @OA\Property(property="data", type="object", description="Данные ответа")
 * )
 *
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     title="Ошибка валидации",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\AdditionalProperties(
 *             type="array",
 *             @OA\Items(type="string")
 *         ),
 *         example={"email": {"The email field is required."}}
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     type="object",
 *     title="Запрос регистрации",
 *     required={"name", "email", "password"},
 *     @OA\Property(property="name", type="string", example="Иван Иванов", description="Имя пользователя"),
 *     @OA\Property(property="email", type="string", format="email", example="ivan@example.com", description="Email пользователя"),
 *     @OA\Property(property="password", type="string", format="password", example="password123", description="Пароль (минимум 8 символов)")
 * )
 *
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     title="Запрос авторизации",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", format="email", example="ivan@example.com", description="Email пользователя"),
 *     @OA\Property(property="password", type="string", format="password", example="password123", description="Пароль")
 * )
 *
 * @OA\Schema(
 *     schema="AuthResponse",
 *     type="object",
 *     title="Ответ аутентификации",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Регистрация прошла успешно"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="user",
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Иван Иванов"),
 *             @OA\Property(property="email", type="string", example="ivan@example.com")
 *         ),
 *         @OA\Property(property="token", type="string", example="1|abc123def456...", description="API токен")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="CarShareRequest",
 *     type="object",
 *     title="Запрос предоставления доступа к автомобилю",
 *     required={"user_email"},
 *     @OA\Property(property="user_email", type="string", format="email", example="friend@example.com", description="Email пользователя для предоставления доступа")
 * )
 */
class Schemas
{
    // Этот класс содержит только аннотации OpenAPI
}
