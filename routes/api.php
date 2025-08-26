<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarBrandController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CarModelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Маршруты аутентификации
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Защищенные маршруты аутентификации
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/car-brands', [CarBrandController::class, 'index']);
Route::get('/car-models', [CarModelController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('cars', [CarController::class, 'index']);
    Route::post('cars', [CarController::class, 'store']);
    
    // Маршруты для прикрепления/открепления автомобилей (не требуют ownership)
    Route::post('cars/{car}/attach', [CarController::class, 'attachToUser']);
    Route::delete('cars/{car}/detach', [CarController::class, 'detachFromUser']);

    // Маршруты для управления автомобилями (проверка прав доступа в контроллере)
    Route::get('cars/{id}', [CarController::class, 'show']);
    Route::put('cars/{id}', [CarController::class, 'update']);
    Route::patch('cars/{id}', [CarController::class, 'update']);
    Route::delete('cars/{id}', [CarController::class, 'destroy']);
    
    // Маршруты для совместного использования
    Route::get('cars/{id}/users', [CarController::class, 'getCarUsers']);
    Route::post('cars/{id}/share', [CarController::class, 'shareWithUser']);
});
