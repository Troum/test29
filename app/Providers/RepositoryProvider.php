<?php

namespace App\Providers;

use App\Contracts\BaseRepositoryInterface;
use App\Repositories\CarBrandRepository;
use App\Repositories\CarModelRepository;
use App\Repositories\CarRepository;
use App\Repositories\UserRepository;
use App\Services\CarBrandService;
use App\Services\CarModelService;
use App\Services\CarService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->when(CarBrandService::class)
            ->needs(BaseRepositoryInterface::class)
            ->give(CarBrandRepository::class);

        $this->app->when(CarModelService::class)
            ->needs(BaseRepositoryInterface::class)
            ->give(CarModelRepository::class);

        $this->app->when(CarService::class)
            ->needs(BaseRepositoryInterface::class)
            ->give(CarRepository::class);

        $this->app->when(UserService::class)
            ->needs(BaseRepositoryInterface::class)
            ->give(UserRepository::class);
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
