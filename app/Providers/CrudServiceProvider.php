<?php

namespace App\Providers;

use App\Contracts\BaseServiceInterface;
use App\Http\Controllers\CarBrandController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CarModelController;
use App\Services\CarBrandService;
use App\Services\CarModelService;
use App\Services\CarOwnershipService;
use App\Services\CarService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class CrudServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->when(CarBrandController::class)
            ->needs(BaseServiceInterface::class)
            ->give(CarBrandService::class);

        $this->app->when(CarModelController::class)
            ->needs(BaseServiceInterface::class)
            ->give(CarModelService::class);

        $this->app->when(CarController::class)
            ->needs(BaseServiceInterface::class)
            ->give(CarService::class);

        $this->app->singleton(CarOwnershipService::class);
        $this->app->singleton(UserService::class);
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
