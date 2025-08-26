<?php

namespace App\Repositories;

use App\Contracts\BaseRepositoryInterface;
use App\Models\CarModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class CarModelRepository implements BaseRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getAll(array $relations = []): Collection
    {
        return CarModel::with($relations)->get();
    }

    /**
     * @inheritDoc
     */
    public function getOne(int $id, array $relations = []): ?CarModel
    {
        return CarModel::with($relations)->find($id);
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function createOne(array $data): ?CarModel
    {
        return DB::transaction(function () use ($data) {
            return CarModel::create($data);
        });
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function updateOne(Model $model, array $data): bool
    {
        /** @var Car $model */
        return DB::transaction(function () use ($model, $data) {
            return $model->update($data);
        });

    }

    /**
     * @param Model $model
     * @inheritDoc
     * @throws Throwable
     */
    public function deleteOne(Model $model, bool $force = false): bool
    {
        /** @var CarModel $model */
        return DB::transaction(function () use ($model, $force) {
            if ($force) {
                return $model->forceDelete();
            }
            return $model->delete();
        });
    }
}
