<?php

namespace App\Http\Controllers;

use App\Contracts\BaseServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarModelController extends Controller
{
    /**
     * @param BaseServiceInterface $carModelService
     */
    public function __construct(private readonly BaseServiceInterface $carModelService)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/car-models",
     *     tags={"Car Models"},
     *     summary="Получить список моделей автомобилей",
     *     description="Возвращает список моделей автомобилей, опционально отфильтрованный по бренду",
     *     @OA\Parameter(
     *         name="car_brand_id",
     *         in="query",
     *         description="ID бренда для фильтрации моделей",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список моделей автомобилей",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/CarModel")
     *             )
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $carBrandId = $request->query('car_brand_id');

        if ($carBrandId) {
            $carModels = $this->carModelService->getAll(['carBrand'])
                ->where('car_brand_id', $carBrandId)
                ->values();
        } else {
            $carModels = $this->carModelService->getAll(['carBrand']);
        }

        return response()->json([
            'success' => true,
            'data' => $carModels
        ]);
    }
}
