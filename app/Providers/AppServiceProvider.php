<?php

namespace App\Providers;

use App\Models\Sector;
use App\Policies\Sector\SectorPolicy;
use App\Repositories\Eloquent\Auth\AuthRepository;
use App\Repositories\Eloquent\Auth\AuthRepositoryInterface;
use App\Repositories\Eloquent\Enterprise\EnterpriseRepositoryInterface;
use App\Repositories\Eloquent\Enterprise\EntrepriseRepository;
use App\Repositories\Eloquent\Plan\PlanRepository;
use App\Repositories\Eloquent\Plan\PlanRepositoryInterface;
use App\Repositories\Eloquent\Sector\SectorRepository;
use App\Repositories\Eloquent\Sector\SectorRepositoryInterface;
use App\Repositories\Eloquent\User\UserRepository;
use App\Repositories\Eloquent\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            AuthRepositoryInterface::class,
            AuthRepository::class
        );

        $this->app->bind(
            SectorRepositoryInterface::class,
            SectorRepository::class
        );

        $this->app->bind(
            PlanRepositoryInterface::class,
            PlanRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Sector::class, SectorPolicy::class);
    }
}
