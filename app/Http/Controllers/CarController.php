<?php

namespace App\Http\Controllers;

use App\Contracts\BaseServiceInterface;
use App\DTOs\CarCreateDTO;
use App\DTOs\CarShareDTO;
use App\DTOs\CarUpdateDTO;
use App\Http\Requests\CarCreateRequest;
use App\Http\Requests\CarShareRequest;
use App\Http\Requests\CarUpdateRequest;
use App\Services\CarOwnershipService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    /**
     * @param BaseServiceInterface $carService
     * @param CarOwnershipService $ownershipService
     * @param UserService $userService
     */
    public function __construct(
        private readonly BaseServiceInterface $carService,
        private readonly CarOwnershipService $ownershipService,
        private readonly UserService $userService
    ) {
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $cars = $user->cars()->with(['carBrand', 'carModel'])->get();

        return response()->json([
            'success' => true,
            'data' => $cars->values()
        ]);
    }

    /**
     * @param CarCreateRequest $request
     * @return JsonResponse
     */
    public function store(CarCreateRequest $request): JsonResponse
    {
        $dto = CarCreateDTO::fromRequest($request);
        $car = $this->carService->createOne($dto->toArray());

        Auth::user()->cars()->attach($car->id);

        return response()->json([
            'success' => true,
            'message' => 'Автомобиль успешно добавлен',
            'data' => $car->load(['carBrand', 'carModel'])
        ], 201);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $car = $this->carService->getOne($id);

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Автомобиль не найден'
            ], 404);
        }

        if (!$this->ownershipService->hasAccess($car)) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен. Вы можете управлять только своими автомобилями.'
            ], 403);
        }

        $car->load(['carBrand', 'carModel', 'users']);

        return response()->json([
            'success' => true,
            'data' => $car
        ]);
    }

    /**
     * @param CarUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(CarUpdateRequest $request, int $id): JsonResponse
    {
        $car = $this->carService->getOne($id);

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Автомобиль не найден'
            ], 404);
        }

        if (!$this->ownershipService->hasAccess($car)) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен. Вы можете управлять только своими автомобилями.'
            ], 403);
        }

        $dto = CarUpdateDTO::fromRequest($request);

        if (!$dto->hasData()) {
            return response()->json([
                'success' => false,
                'message' => 'Не указаны данные для обновления'
            ], 400);
        }

        $updated = $this->carService->updateOne($car, $dto->toArray());

        if ($updated) {
            $car->refresh();
            return response()->json([
                'success' => true,
                'message' => 'Автомобиль успешно обновлен',
                'data' => $car->load(['carBrand', 'carModel'])
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Не удалось обновить автомобиль'
        ], 500);
    }

    /**
     * Remove the specified car.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $car = $this->carService->getOne($id);

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Автомобиль не найден'
            ], 404);
        }

        if (!$this->ownershipService->hasAccess($car)) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен. Вы можете управлять только своими автомобилями.'
            ], 403);
        }

        $car->users()->detach();

        $deleted = $this->carService->deleteOne($car);

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Автомобиль успешно удален'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Не удалось удалить автомобиль'
        ], 500);
    }

    /**
     * @OA\Post(
     *     path="/cars/{id}/attach",
     *     tags={"Cars"},
     *     summary="Прикрепить автомобиль к текущему пользователю",
     *     description="Добавляет связь между автомобилем и текущим пользователем",
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
     *         description="Автомобиль успешно прикреплен",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Car attached successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Не авторизован"),
     *     @OA\Response(response=404, description="Автомобиль не найден"),
     *     @OA\Response(response=409, description="Автомобиль уже прикреплен")
     * )
     *
     * @param int $id
     * @return JsonResponse
     */
    public function attachToUser(int $id): JsonResponse
    {
        $car = $this->carService->getOne($id);

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Автомобиль не найден'
            ], 404);
        }

        $result = $this->ownershipService->attachCar($car);
        $statusCode = $result['success'] ? 200 : ($result['message'] === 'Автомобиль уже добавлен к этому пользователю' ? 409 : 400);

        return response()->json($result, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/cars/{id}/detach",
     *     tags={"Cars"},
     *     summary="Открепить автомобиль от текущего пользователя",
     *     description="Удаляет связь между автомобилем и текущим пользователем",
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
     *         description="Автомобиль успешно откреплен",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Car detached successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Не авторизован"),
     *     @OA\Response(response=404, description="Автомобиль не найден или не прикреплен")
     * )
     *
     * @param int $id
     * @return JsonResponse
     */
    public function detachFromUser(int $id): JsonResponse
    {
        $car = $this->carService->getOne($id);

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Автомобиль не найден'
            ], 404);
        }

        $result = $this->ownershipService->detachCar($car);
        $statusCode = $result['success'] ? 200 : 404;

        return response()->json($result, $statusCode);
    }

    /**
     * @OA\Get(
     *     path="/cars/{id}/users",
     *     tags={"Cars"},
     *     summary="Получить список пользователей автомобиля",
     *     description="Возвращает всех пользователей, которые имеют доступ к автомобилю",
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
     *         description="Список пользователей автомобиля",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="car", ref="#/components/schemas/Car"),
     *                 @OA\Property(
     *                     property="users",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/User")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Не авторизован"),
     *     @OA\Response(response=403, description="Доступ запрещен"),
     *     @OA\Response(response=404, description="Автомобиль не найден")
     * )
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function getCarUsers(Request $request, int $id): JsonResponse
    {
        $car = $this->carService->getOne($id);

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Автомобиль не найден'
            ], 404);
        }

        if (!$this->ownershipService->hasAccess($car)) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен. Вы можете управлять только своими автомобилями.'
            ], 403);
        }

        $result = $this->ownershipService->getCarUsers($car);

        return response()->json($result);
    }

    /**
     * @OA\Post(
     *     path="/cars/{id}/share",
     *     tags={"Cars"},
     *     summary="Поделиться автомобилем с другим пользователем",
     *     description="Добавляет другого пользователя к автомобилю для совместного использования",
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
     *         @OA\JsonContent(
     *             required={"user_email"},
     *             @OA\Property(property="user_email", type="string", format="email", example="friend@example.com", description="Email пользователя для добавления")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Автомобиль успешно расшарен",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Car shared successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Не авторизован"),
     *     @OA\Response(response=403, description="Доступ запрещен"),
     *     @OA\Response(response=404, description="Автомобиль или пользователь не найден"),
     *     @OA\Response(response=409, description="Пользователь уже имеет доступ к автомобилю"),
     *     @OA\Response(response=422, description="Ошибка валидации")
     * )
     *
     * @param CarShareRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function shareWithUser(CarShareRequest $request, int $id): JsonResponse
    {
        $car = $this->carService->getOne($id);
        $dto = CarShareDTO::fromRequest($request);

        $userToShare = $this->userService->findByEmail($dto->userEmail);

        $result = $this->ownershipService->shareCar($car, $userToShare);
        $statusCode = $result['success'] ? 200 : ($result['message'] === 'Пользователь уже имеет доступ к этому автомобилю' ? 409 : 403);

        return response()->json($result, $statusCode);
    }
}
