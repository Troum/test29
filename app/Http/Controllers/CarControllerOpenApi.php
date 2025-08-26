<?php

namespace App\Http\Controllers;

/**
 * Аннотации OpenAPI для CarController
 *
 * @OA\Get(
 *     path="/cars",
 *     tags={"Cars"},
 *     summary="Получить список автомобилей пользователя",
 *     description="Возвращает все автомобили, принадлежащие текущему пользователю",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Список автомобилей",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(ref="#/components/schemas/Car")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Не авторизован")
 * )
 *
 * @OA\Post(
 *     path="/cars",
 *     tags={"Cars"},
 *     summary="Создать новый автомобиль",
 *     description="Создает новый автомобиль и привязывает его к текущему пользователю",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/CarCreateRequest")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Автомобиль успешно создан",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Car created successfully"),
 *             @OA\Property(property="data", ref="#/components/schemas/Car")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Не авторизован"),
 *     @OA\Response(response=422, description="Ошибка валидации")
 * )
 *
 * @OA\Get(
 *     path="/cars/{id}",
 *     tags={"Cars"},
 *     summary="Получить автомобиль по ID",
 *     description="Возвращает информацию об автомобиле, если он принадлежит пользователю",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID автомобиля",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Данные автомобиля",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", ref="#/components/schemas/Car")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Не авторизован"),
 *     @OA\Response(response=403, description="Доступ запрещен"),
 *     @OA\Response(response=404, description="Автомобиль не найден")
 * )
 *
 * @OA\Put(
 *     path="/cars/{id}",
 *     tags={"Cars"},
 *     summary="Обновить автомобиль",
 *     description="Обновляет данные автомобиля, если он принадлежит пользователю",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID автомобиля",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/CarUpdateRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Автомобиль успешно обновлен",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Car updated successfully"),
 *             @OA\Property(property="data", ref="#/components/schemas/Car")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Не авторизован"),
 *     @OA\Response(response=403, description="Доступ запрещен"),
 *     @OA\Response(response=404, description="Автомобиль не найден"),
 *     @OA\Response(response=422, description="Ошибка валидации")
 * )
 *
 * @OA\Delete(
 *     path="/cars/{id}",
 *     tags={"Cars"},
 *     summary="Удалить автомобиль",
 *     description="Удаляет автомобиль, если он принадлежит пользователю",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID автомобиля",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Автомобиль успешно удален",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Car deleted successfully")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Не авторизован"),
 *     @OA\Response(response=403, description="Доступ запрещен"),
 *     @OA\Response(response=404, description="Автомобиль не найден")
 * )
 */
