<?php

namespace App\Services;

use App\Contracts\BaseRepositoryInterface;
use App\Contracts\BaseServiceInterface;
use Illuminate\Database\Eloquent\Model;

class CarBrandService implements BaseServiceInterface
{
    /**
     * @param BaseRepositoryInterface $carBrandRepository
     */
    public function __construct(private BaseRepositoryInterface $carBrandRepository)
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function getAll(array $relations = []): mixed
    {
        return $this->carBrandRepository->getAll($relations);
    }

    /**
     * @inheritDoc
     */
    public function getOne(int $id, array $relations = []): mixed
    {
        return $this->carBrandRepository->getOne($id, $relations);
    }

    /**
     * @inheritDoc
     */
    public function createOne(array $data): mixed
    {
        return $this->carBrandRepository->createOne($data);
    }

    /**
     * @inheritDoc
     */
    public function updateOne(Model $model, array $data): mixed
    {
        return $this->carBrandRepository->updateOne($model, $data);
    }

    /**
     * @inheritDoc
     */
    public function deleteOne(Model $model, bool $force = false): mixed
    {
        return $this->carBrandRepository->deleteOne($model, $force);
    }
}
