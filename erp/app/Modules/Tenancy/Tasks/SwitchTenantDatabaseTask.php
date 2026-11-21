<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Tasks;

use App\Modules\Tenancy\DatabaseManager\TenantDatabaseManagerFactory;
use App\Modules\Tenancy\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

/**
 * Class SwitchTenantDatabaseTask
 *
 * Automatically switches the active 'tenant' connection database and sets
 * default database connection to 'tenant' when tenant context is active.
 */
class SwitchTenantDatabaseTask implements SwitchTenantTask
{
    public function makeCurrent(IsTenant $tenant): void
    {
        /** @var Tenant $tenant */
        $manager = TenantDatabaseManagerFactory::make();
        $config = $manager->getConnectionConfig($tenant);

        config(['database.connections.tenant' => $config]);
        config(['database.default' => 'tenant']);
        DB::purge('tenant');
    }

    public function forgetCurrent(): void
    {
        config(['database.connections.tenant.database' => null]);
        config(['database.default' => 'landlord']);
        DB::purge('tenant');
    }
}
