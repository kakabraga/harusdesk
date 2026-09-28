<?php

namespace App\Providers;

use App\Models\Attachment;
use App\Models\Sector;
use App\Models\Ticket;
use App\Policies\Attachment\AttachmentPolicy;
use App\Policies\Sector\SectorPolicy;
use App\Policies\Ticket\TicketPolicy;
use App\Repositories\Eloquent\Attachment\AttachmentRepository;
use App\Repositories\Eloquent\Attachment\AttachmentRepositoryInterface;
use App\Repositories\Eloquent\Auth\AuthRepository;
use App\Repositories\Eloquent\Auth\AuthRepositoryInterface;
use App\Repositories\Eloquent\Enterprise\EnterpriseRepositoryInterface;
use App\Repositories\Eloquent\Enterprise\EntrepriseRepository;
use App\Repositories\Eloquent\Plan\PlanRepository;
use App\Repositories\Eloquent\Plan\PlanRepositoryInterface;
use App\Repositories\Eloquent\Sector\SectorRepository;
use App\Repositories\Eloquent\Sector\SectorRepositoryInterface;
use App\Repositories\Eloquent\Ticket\TicketRepository;
use App\Repositories\Eloquent\Ticket\TicketRepositoryInterface;
use App\Repositories\Eloquent\User\UserRepository;
use App\Repositories\Eloquent\User\UserRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rule;

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

        $this->app->bind(
            TicketRepositoryInterface::class,
            TicketRepository::class
        );

        $this->app->bind(
            AttachmentRepositoryInterface::class,
            AttachmentRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::serializeUsing(fn ($date) => $date->format('Y-m-d H:i:s'));

        Gate::policy(Sector::class, SectorPolicy::class);
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(Attachment::class, AttachmentPolicy::class);

        Rule::macro('existsForTenant', function (string $table, string $column = 'id', ?int $enterpriseId = null) {
            $resolvedEnterpriseId = $enterpriseId ?? auth()->user()?->enterprise_id;

            return Rule::exists($table, $column)->where(function ($query) use ($resolvedEnterpriseId) {
                if ($resolvedEnterpriseId && ! auth()->user()?->isSuperAdmin()) {
                    $query->where('enterprise_id', $resolvedEnterpriseId);
                }
            });
        });
    }
}
