<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Commands;

use App\Modules\Tenancy\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * Command TenantMigrateCommand
 *
 * Runs tenant migrations across all or specific tenant databases.
 */
class TenantMigrateCommand extends Command
{
    protected $signature = 'tenant:migrate {tenant? : Optional tenant ID or slug}';

    protected $description = 'Run migrations across tenant databases';

    public function handle(): int
    {
        $tenantIdentifier = $this->argument('tenant');

        $query = Tenant::on('landlord')->where('is_active', true);
        if ($tenantIdentifier !== null) {
            $query->where(function ($q) use ($tenantIdentifier) {
                $q->where('id', $tenantIdentifier)->orWhere('slug', $tenantIdentifier);
            });
        }

        $tenants = $query->get();

        if ($tenants->isEmpty()) {
            $this->warn('No matching active tenants found.');

            return Command::SUCCESS;
        }

        $this->info("Migrating {$tenants->count()} tenant database(s)...");

        foreach ($tenants as $tenant) {
            $this->line("-> Migrating tenant: {$tenant->name} ({$tenant->slug})...");

            $tenant->makeCurrent();

            try {
                Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--path' => 'database/migrations/tenant',
                    '--force' => true,
                ]);

                $this->info("   Migrated: {$tenant->slug}");
            } catch (\Throwable $e) {
                $this->error("   Failed to migrate {$tenant->slug}: {$e->getMessage()}");
            } finally {
                $tenant->forgetCurrent();
            }
        }

        return Command::SUCCESS;
    }
}
