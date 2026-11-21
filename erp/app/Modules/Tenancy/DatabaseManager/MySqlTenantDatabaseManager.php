<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\DatabaseManager;

use App\Modules\Tenancy\Models\Tenant;
use Illuminate\Support\Facades\DB;

/**
 * Class MySqlTenantDatabaseManager
 *
 * Implements MySQL database management strategy for production tenant databases.
 */
class MySqlTenantDatabaseManager implements TenantDatabaseManagerInterface
{
    /**
     * {@inheritDoc}
     */
    public function createDatabase(Tenant $tenant): void
    {
        $database = $this->sanitizeDatabaseName($tenant->getDatabaseName());
        $charset = config('database.connections.mysql.charset', 'utf8mb4');
        $collation = config('database.connections.mysql.collation', 'utf8mb4_unicode_ci');

        DB::connection('landlord')->statement(
            "CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET {$charset} COLLATE {$collation}"
        );
    }

    /**
     * {@inheritDoc}
     */
    public function dropDatabase(Tenant $tenant): void
    {
        $database = $this->sanitizeDatabaseName($tenant->getDatabaseName());

        DB::connection('landlord')->statement("DROP DATABASE IF EXISTS `{$database}`");
    }

    /**
     * {@inheritDoc}
     */
    public function databaseExists(Tenant $tenant): bool
    {
        $database = $this->sanitizeDatabaseName($tenant->getDatabaseName());

        $result = DB::connection('landlord')->select(
            'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?',
            [$database]
        );

        return ! empty($result);
    }

    /**
     * {@inheritDoc}
     */
    public function getConnectionConfig(Tenant $tenant): array
    {
        $base = config('database.connections.mysql', []);

        $base['driver'] = 'mysql';
        $base['database'] = $this->sanitizeDatabaseName($tenant->getDatabaseName());

        return $base;
    }

    /**
     * Prevent SQL injection in identifier name.
     */
    private function sanitizeDatabaseName(string $name): string
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $name);
    }
}
