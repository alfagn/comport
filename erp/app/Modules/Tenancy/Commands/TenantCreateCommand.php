<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Commands;

use App\Modules\Tenancy\DTOs\ProvisionTenantData;
use App\Modules\Tenancy\Services\TenantService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Command TenantCreateCommand
 *
 * Artisan command to provision a new company workspace.
 */
class TenantCreateCommand extends Command
{
    protected $signature = 'tenant:create 
                            {name? : Company Name} 
                            {--slug= : Company Subdomain Slug} 
                            {--email= : Owner Email} 
                            {--password= : Owner Password} 
                            {--internal : Flag as internal holding company}';

    protected $description = 'Provision a new tenant database, owner account, and workspace';

    public function handle(TenantService $tenantService): int
    {
        $name = (string) ($this->argument('name') ?? $this->ask('Company Name'));
        $slug = (string) ($this->option('slug') ?? $this->ask('Subdomain slug', Str::slug($name)));
        $email = (string) ($this->option('email') ?? $this->ask('Owner Email'));
        $password = (string) ($this->option('password') ?? $this->secret('Owner Password'));
        $isInternal = (bool) $this->option('internal');

        $this->info("Provisioning company: {$name} ({$slug})...");

        try {
            $data = new ProvisionTenantData(
                companyName: $name,
                slug: $slug,
                ownerName: $name.' Admin',
                ownerEmail: $email,
                ownerPassword: $password,
                isInternal: $isInternal,
                trialDays: $isInternal ? 0 : 14
            );

            $tenant = $tenantService->provision($data);

            $this->info("✓ Tenant successfully created with ID: {$tenant->id}");
            $this->info("✓ Subdomain: {$tenant->slug}.empetix.test");
            $this->info("✓ Database: {$tenant->getDatabaseName()}");

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed to provision tenant: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
