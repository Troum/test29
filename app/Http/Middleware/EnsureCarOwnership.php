<?php

namespace App\Http\Middleware;

use App\Models\Car;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCarOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $carId = $request->route('car');

        if (!$carId) {
            return response()->json([
                'success' => false,
                'message' => 'Требуется указать ID автомобиля'
            ], 400);
        }

        $car = Car::find($carId);

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Автомобиль не найден'
            ], 404);
        }

        $user = Auth::user();
        if (!$user->cars()->where('car_id', $car->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен. Вы можете управлять только своими автомобилями.'
            ], 403);
        }

        $request->attributes->set('car', $car);

        return $next($request);
    }
}
