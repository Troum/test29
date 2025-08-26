<?php

namespace App\Services;

use App\Contracts\BaseRepositoryInterface;
use App\Contracts\BaseServiceInterface;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Model;

class UserService implements BaseServiceInterface
{
    /**
     * @param UserRepository $userRepository
     */
    public function __construct(private BaseRepositoryInterface $userRepository)
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function getAll(array $relations = []): mixed
    {
        return $this->userRepository->getAll($relations);
    }

    /**
     * @inheritDoc
     */
    public function getOne(int $id, array $relations = []): mixed
    {
        return $this->userRepository->getOne($id, $relations);
    }

    /**
     * Find user by email
     *
     * @param string $email
     * @param array $relations
     * @return mixed
     */
    public function findByEmail(string $email, array $relations = []): mixed
    {
        /** @var UserRepository $repository */
        $repository = $this->userRepository;
        return $repository->findByEmail($email, $relations);
    }

    /**
     * @inheritDoc
     */
    public function createOne(array $data): mixed
    {
        return $this->userRepository->createOne($data);
    }

    /**
     * @inheritDoc
     */
    public function updateOne(Model $model, array $data): mixed
    {
        return $this->userRepository->updateOne($model, $data);
    }

    /**
     * @inheritDoc
     */
    public function deleteOne(Model $model, bool $force = false): mixed
    {
        return $this->userRepository->deleteOne($model, $force);
    }
}
