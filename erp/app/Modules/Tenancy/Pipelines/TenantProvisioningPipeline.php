<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Pipelines;

use App\Modules\Billing\Models\Plan;
use App\Modules\Billing\Models\Subscription;
use App\Modules\Tenancy\DatabaseManager\TenantDatabaseManagerFactory;
use App\Modules\Tenancy\DTOs\ProvisionTenantData;
use App\Modules\Tenancy\Models\Tenant;
use App\Modules\Tenancy\Models\TenantDomain;
use App\Modules\TenantCore\Models\User as TenantUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

/**
 * Class TenantProvisioningPipeline
 *
 * Orchestrates the end-to-end provisioning of a new tenant database,
 * schema migrations, role seeding, initial owner user creation, and subscription activation.
 */
class TenantProvisioningPipeline
{
    /**
     * Provision a complete tenant environment.
     *
     * @throws Throwable
     */
    public function handle(ProvisionTenantData $data): Tenant
    {
        // 1. Create Tenant and Domains in Landlord DB
        $tenant = DB::connection('landlord')->transaction(function () use ($data) {
            $databaseName = 'tenant_'.str_replace('-', '_', $data->slug);

            $tenant = Tenant::create([
                'name' => $data->companyName,
                'slug' => $data->slug,
                'email' => $data->ownerEmail,
                'phone' => $data->phone,
                'database_name' => $databaseName,
                'is_active' => true,
                'is_internal' => $data->isInternal,
                'locale' => $data->locale,
                'currency' => $data->currency,
                'timezone' => $data->timezone,
                'trial_ends_at' => $data->isInternal ? null : Carbon::now()->addDays($data->trialDays),
                'settings' => [
                    'company_name' => $data->companyName,
                    'contact_email' => $data->ownerEmail,
                    'contact_phone' => $data->phone,
                ],
            ]);

            // Add standard subdomain (e.g. acme.empetix.test)
            $centralDomain = (string) config('app.central_domain', 'empetix.test');
            TenantDomain::create([
                'tenant_id' => $tenant->id,
                'domain' => "{$data->slug}.{$centralDomain}",
                'is_primary' => true,
                'is_custom' => false,
                'is_verified' => true,
                'verified_at' => Carbon::now(),
            ]);

            // If custom domain provided, register it for verification
            if (! empty($data->customDomain)) {
                TenantDomain::create([
                    'tenant_id' => $tenant->id,
                    'domain' => strtolower(trim($data->customDomain)),
                    'is_primary' => false,
                    'is_custom' => true,
                    'is_verified' => false,
                    'verification_token' => Str::random(40),
                ]);
            }

            return $tenant;
        });

        // 2. Provision Tenant Database & Infrastructure
        $dbManager = TenantDatabaseManagerFactory::make();
        $dbManager->createDatabase($tenant);

        // 3. Make tenant current and run schema migrations
        $tenant->makeCurrent();

        try {
            // Run tenant migrations
            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);

            // 4. Seed Tenant Roles and Permissions
            $this->seedTenantRolesAndPermissions();

            // 5. Create Tenant Owner User
            $owner = TenantUser::on('tenant')->create([
                'name' => $data->ownerName,
                'email' => $data->ownerEmail,
                'password' => Hash::make($data->ownerPassword),
                'email_verified_at' => Carbon::now(),
                'locale' => $data->locale,
            ]);

            // Assign Owner Role
            $owner->assignRole('owner');

            // 6. Assign Initial Subscription in Landlord DB
            $this->assignSubscription($tenant, $data->planId, $data->trialDays, $data->isInternal);

        } finally {
            $tenant->forgetCurrent();
        }

        return $tenant;
    }

    /**
     * Seed core roles and permissions inside tenant database.
     */
    protected function seedTenantRolesAndPermissions(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'view-dashboard',
            'manage-users',
            'manage-roles',
            'manage-company-settings',
            // HRM
            'manage-employees',
            'view-employees',
            'manage-attendance',
            'manage-leaves',
            'approve-leaves',
            'manage-payroll',
            // CRM
            'manage-clients',
            'manage-leads',
            'manage-deals',
            // Marketing
            'manage-campaigns',
            'view-marketing-reports',
            // Projects
            'manage-projects',
            'manage-tasks',
            'track-time',
            // Finance
            'manage-invoices',
            'view-invoices',
            'record-payments',
            'manage-expenses',
            'view-financial-reports',
        ];

        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        // Create Default Roles
        $ownerRole = Role::findOrCreate('owner', 'web');
        $ownerRole->syncPermissions(Permission::all());

        $adminRole = Role::findOrCreate('admin', 'web');
        $adminRole->syncPermissions(Permission::all());

        $managerRole = Role::findOrCreate('manager', 'web');
        $managerRole->givePermissionTo([
            'view-dashboard',
            'view-employees',
            'approve-leaves',
            'manage-projects',
            'manage-tasks',
            'manage-leads',
        ]);

        $employeeRole = Role::findOrCreate('employee', 'web');
        $employeeRole->givePermissionTo([
            'view-dashboard',
            'manage-attendance',
            'manage-leaves',
            'track-time',
        ]);
    }

    /**
     * Attach subscription to tenant.
     */
    protected function assignSubscription(Tenant $tenant, ?int $planId, int $trialDays, bool $isInternal): void
    {
        $plan = null;
        if ($planId !== null) {
            $plan = Plan::on('landlord')->find($planId);
        }

        if ($plan === null) {
            // Default to first active plan or unlimited internal plan
            $plan = Plan::on('landlord')->where('is_active', true)->first();
        }

        if ($plan === null) {
            return;
        }

        Subscription::on('landlord')->create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => $isInternal ? 'active' : 'trialing',
            'trial_ends_at' => $isInternal ? null : Carbon::now()->addDays($trialDays),
            'starts_at' => Carbon::now(),
            'ends_at' => $isInternal ? null : Carbon::now()->addDays($trialDays),
            'billing_interval' => 'monthly',
        ]);
    }
}
