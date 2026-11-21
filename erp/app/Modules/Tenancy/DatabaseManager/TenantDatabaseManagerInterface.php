<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\DatabaseManager;

use App\Modules\Tenancy\Models\Tenant;

/**
 * Interface TenantDatabaseManagerInterface
 *
 * Strategy pattern interface defining database operations for tenant provisioning and lifecycle.
 */
interface TenantDatabaseManagerInterface
{
    /**
     * Create isolated database for the tenant.
     */
    public function createDatabase(Tenant $tenant): void;

    /**
     * Drop the tenant database.
     */
    public function dropDatabase(Tenant $tenant): void;

    /**
     * Determine if tenant database currently exists.
     */
    public function databaseExists(Tenant $tenant): bool;

    /**
     * Get dynamic connection configuration array for the tenant.
     *
     * @return array<string, mixed>
     */
    public function getConnectionConfig(Tenant $tenant): array;
}
