<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Car Management API",
 *     description="Современное API для управления автомобилями с возможностью совместного использования. Построено на Laravel с использованием Clean Architecture, Repository Pattern и Service Layer.",
 *     version="1.0.0",
 *     @OA\Contact(
 *         email="admin@example.com",
 *         name="API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Локальный сервер разработки"
 * )
 *
 * @OA\Server(
 *     url="http://api.test29.test/api",
 *     description="Сервер разработки (Herd)"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Введите токен в формате: Bearer {your-token}"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="Аутентификация пользователей"
 * )
 *
 * @OA\Tag(
 *     name="Cars",
 *     description="Управление автомобилями"
 * )
 *
 * @OA\Tag(
 *     name="Car Brands",
 *     description="Справочник брендов автомобилей"
 * )
 *
 * @OA\Tag(
 *     name="Car Models",
 *     description="Справочник моделей автомобилей"
 * )
 */
abstract class Controller
{
    //
}
