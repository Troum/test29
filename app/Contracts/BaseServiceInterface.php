<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

interface BaseServiceInterface
{
    /**
     * @param array $relations
     * @return mixed
     */
    public function getAll(array $relations = []): mixed;

    /**
     * @param int $id
     * @param array $relations
     * @return mixed
     */
    public function getOne(int $id, array $relations = []): mixed;

    /**
     * @param array $data
     * @return mixed
     */
    public function createOne(array $data): mixed;

    /**
     * @param Model $model
     * @param array $data
     * @return mixed
     */
    public function updateOne(Model $model, array $data): mixed;

    /**
     * @param Model $model
     * @param bool $force
     * @return mixed
     */
    public function deleteOne(Model $model, bool $force = false): mixed;
}
