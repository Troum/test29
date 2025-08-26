<?php

namespace App\Services;

use App\Contracts\BaseRepositoryInterface;
use App\Contracts\BaseServiceInterface;
use Illuminate\Database\Eloquent\Model;

class CarService implements BaseServiceInterface
{
    /**
     * @param BaseRepositoryInterface $carRepository
     */
    public function __construct(private BaseRepositoryInterface $carRepository)
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function getAll(array $relations = []): mixed
    {
        return $this->carRepository->getAll($relations);
    }

    /**
     * @inheritDoc
     */
    public function getOne(int $id, array $relations = []): mixed
    {
        return $this->carRepository->getOne($id, $relations);
    }

    /**
     * @inheritDoc
     */
    public function createOne(array $data): mixed
    {
        return $this->carRepository->createOne($data);
    }

    /**
     * @inheritDoc
     */
    public function updateOne(Model $model, array $data): mixed
    {
        return $this->carRepository->updateOne($model, $data);
    }

    /**
     * @inheritDoc
     */
    public function deleteOne(Model $model, bool $force = false): mixed
    {
        return $this->carRepository->deleteOne($model, $force);
    }
}
