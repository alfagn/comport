<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\DatabaseManager;

use InvalidArgumentException;

/**
 * Class TenantDatabaseManagerFactory
 *
 * Factory pattern resolving the appropriate TenantDatabaseManager strategy based on environment configuration.
 */
class TenantDatabaseManagerFactory
{
    /**
     * Resolve the configured database manager strategy.
     */
    public static function make(): TenantDatabaseManagerInterface
    {
        $driver = config('database.connections.tenant.driver', config('database.default'));

        return match ($driver) {
            'sqlite' => new SqliteTenantDatabaseManager,
            'mysql', 'mariadb' => new MySqlTenantDatabaseManager,
            default => throw new InvalidArgumentException("Unsupported tenant database driver: {$driver}"),
        };
    }
}
