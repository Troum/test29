<?php

namespace App\Services;

use App\Contracts\BaseRepositoryInterface;
use App\Contracts\BaseServiceInterface;
use Illuminate\Database\Eloquent\Model;

readonly class CarModelService implements BaseServiceInterface
{
    /**
     * @param BaseRepositoryInterface $carModelRepository
     */
    public function __construct(private BaseRepositoryInterface $carModelRepository)
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function getAll(array $relations = []): mixed
    {
        return $this->carModelRepository->getAll($relations);
    }

    /**
     * @inheritDoc
     */
    public function getOne(int $id, array $relations = []): mixed
    {
        return $this->carModelRepository->getOne($id, $relations);
    }

    /**
     * @inheritDoc
     */
    public function createOne(array $data): mixed
    {
        return $this->carModelRepository->createOne($data);
    }

    /**
     * @inheritDoc
     */
    public function updateOne(Model $model, array $data): mixed
    {
        return $this->carModelRepository->updateOne($model, $data);
    }

    /**
     * @inheritDoc
     */
    public function deleteOne(Model $model, bool $force = false): mixed
    {
        return $this->carModelRepository->deleteOne($model, $force);
    }
}
