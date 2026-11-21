<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Providers;

use App\Modules\Tenancy\Commands\TenantCreateCommand;
use App\Modules\Tenancy\Commands\TenantMigrateCommand;
use App\Modules\Tenancy\Contracts\TenantRepositoryInterface;
use App\Modules\Tenancy\Livewire\RegisterCompany;
use App\Modules\Tenancy\Livewire\TenantSubscription;
use App\Modules\Tenancy\Middleware\EnsureSubscriptionIsActive;
use App\Modules\Tenancy\Middleware\EnsureTenantFeature;
use App\Modules\Tenancy\Middleware\EnsureTenantIsActive;
use App\Modules\Tenancy\Middleware\NeedsTenant;
use App\Modules\Tenancy\Middleware\ResolvePathTenant;
use App\Modules\Tenancy\Models\Tenant;
use App\Modules\Tenancy\Repositories\EloquentTenantRepository;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

/**
 * Class TenancyServiceProvider
 *
 * Bootstraps the multi-database tenancy infrastructure, repository bindings,
 * console commands, middleware aliases, and Livewire component registrations.
 */
class TenancyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TenantRepositoryInterface::class, function () {
            return new EloquentTenantRepository(new Tenant);
        });
    }

    public function boot(Router $router): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TenantCreateCommand::class,
                TenantMigrateCommand::class,
            ]);
        }

        // Register route middleware aliases
        $router->aliasMiddleware('tenant.needed', NeedsTenant::class);
        $router->aliasMiddleware('tenant.path', ResolvePathTenant::class);
        $router->aliasMiddleware('tenant.active', EnsureTenantIsActive::class);
        $router->aliasMiddleware('tenant.subscribed', EnsureSubscriptionIsActive::class);
        $router->aliasMiddleware('tenant.feature', EnsureTenantFeature::class);

        // Load views
        $this->loadViewsFrom(resource_path('views/modules/tenancy'), 'tenancy');
        $this->loadViewsFrom(resource_path('views/modules/admin'), 'admin');
        $this->loadViewsFrom(resource_path('views/modules/tenant'), 'tenant');
        $this->loadViewsFrom(resource_path('views/modules/hrm'), 'hrm');
        $this->loadViewsFrom(resource_path('views/modules/crm'), 'crm');
        $this->loadViewsFrom(resource_path('views/modules/marketing'), 'marketing');
        $this->loadViewsFrom(resource_path('views/modules/projects'), 'projects');
        $this->loadViewsFrom(resource_path('views/modules/finance'), 'finance');

        // Livewire Component aliases
        Livewire::component('tenancy.register-company', RegisterCompany::class);
        Livewire::component('tenancy.tenant-subscription', TenantSubscription::class);
    }
}
