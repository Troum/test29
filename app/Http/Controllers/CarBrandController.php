<?php

namespace App\Http\Controllers;

use App\Contracts\BaseServiceInterface;
use App\Services\CarBrandService;
use Illuminate\Http\JsonResponse;

class CarBrandController extends Controller
{
    /**
     * @param CarBrandService $carBrandService
     */
    public function __construct(private readonly BaseServiceInterface $carBrandService)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/car-brands",
     *     tags={"Car Brands"},
     *     summary="Получить список всех брендов автомобилей",
     *     description="Возвращает список всех доступных брендов автомобилей",
     *     @OA\Response(
     *         response=200,
     *         description="Список брендов автомобилей",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/CarBrand")
     *             )
     *         )
     *     )
     * )
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $carBrands = $this->carBrandService->getAll();

        return response()->json([
            'success' => true,
            'data' => $carBrands
        ]);
    }
}
