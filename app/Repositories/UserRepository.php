<?php

namespace App\Repositories;

use App\Contracts\BaseRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserRepository implements BaseRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getAll(array $relations = []): Collection
    {
        return User::with($relations)->get();
    }

    /**
     * @inheritDoc
     */
    public function getOne(int $id, array $relations = []): ?User
    {
        return User::with($relations)->find($id);
    }

    /**
     * Find user by email
     *
     * @param string $email
     * @param array $relations
     * @return User|null
     */
    public function findByEmail(string $email, array $relations = []): ?User
    {
        return User::with($relations)->where('email', $email)->first();
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function createOne(array $data): ?User
    {
        return DB::transaction(function () use ($data) {
            return User::create($data);
        });
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function updateOne(Model $model, array $data): bool
    {
        /** @var User $model */
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
        /** @var User $model */
        return DB::transaction(function () use ($model, $force) {
            if ($force) {
                return $model->forceDelete();
            }
            return $model->delete();
        });
    }
}
