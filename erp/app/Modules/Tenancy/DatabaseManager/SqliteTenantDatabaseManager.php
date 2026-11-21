<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\DatabaseManager;

use App\Modules\Tenancy\Models\Tenant;
use Illuminate\Support\Facades\File;

/**
 * Class SqliteTenantDatabaseManager
 *
 * Implements SQLite database management strategy for tenant databases.
 * Useful for local testing, CI/CD pipelines, and lightweight standalone instances.
 */
class SqliteTenantDatabaseManager implements TenantDatabaseManagerInterface
{
    /**
     * Get path to the tenant sqlite database file.
     */
    public function getDatabasePath(Tenant $tenant): string
    {
        $dir = database_path('tenants');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        return $dir.DIRECTORY_SEPARATOR.$tenant->getDatabaseName().'.sqlite';
    }

    /**
     * {@inheritDoc}
     */
    public function createDatabase(Tenant $tenant): void
    {
        $path = $this->getDatabasePath($tenant);

        if (! File::exists($path)) {
            File::put($path, '');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function dropDatabase(Tenant $tenant): void
    {
        $path = $this->getDatabasePath($tenant);

        if (File::exists($path)) {
            File::delete($path);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function databaseExists(Tenant $tenant): bool
    {
        return File::exists($this->getDatabasePath($tenant));
    }

    /**
     * {@inheritDoc}
     */
    public function getConnectionConfig(Tenant $tenant): array
    {
        return [
            'driver' => 'sqlite',
            'database' => $this->getDatabasePath($tenant),
            'prefix' => '',
            'foreign_key_constraints' => true,
        ];
    }
}
