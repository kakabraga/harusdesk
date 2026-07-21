<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Eloquent\Plan\PlanRepositoryInterface;
use App\Repositories\Eloquent\Plan\PlanRepository;
use App\Repositories\Eloquent\Enterprise\EnterpriseRepositoryInterface;
use App\Repositories\Eloquent\Enterprise\EntrepriseRepository;
use App\Repositories\Eloquent\User\UserRepositoryInterface;
use App\Repositories\Eloquent\User\UserRepository;
use App\Repositories\Eloquent\Auth\AuthRepositoryInterface;
use App\Repositories\Eloquent\Auth\AuthRepository;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->app->bind(
        //     PlanRepositoryInterface::class,
        //     PlanRepository::class
        // );
        $this->app->bind(
            EnterpriseRepositoryInterface::class,
            EntrepriseRepository::class
        );
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            AuthRepositoryInterface::class,
            AuthRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
