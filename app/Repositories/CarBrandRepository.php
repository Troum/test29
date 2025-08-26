<?php

namespace App\Repositories;

use App\Contracts\BaseRepositoryInterface;
use App\Models\CarBrand;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class CarBrandRepository implements BaseRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getAll(array $relations = []): Collection
    {
        return CarBrand::with($relations)->get();
    }

    /**
     * @inheritDoc
     */
    public function getOne(int $id, array $relations = []): ?CarBrand
    {
        return CarBrand::with($relations)->find($id);
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function createOne(array $data): ?CarBrand
    {
        return DB::transaction(function () use ($data) {
            return CarBrand::create($data);
        });
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function updateOne(Model $model, array $data): bool
    {
        /** @var CarBrand $model */
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
        /** @var CarBrand $model */
        return DB::transaction(function () use ($model, $force) {
            if ($force) {
                return $model->forceDelete();
            }
            return $model->delete();
        });
    }
}
