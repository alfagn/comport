<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Services;

use App\Core\Services\BaseService;
use App\Modules\Tenancy\Contracts\TenantRepositoryInterface;
use App\Modules\Tenancy\DTOs\ProvisionTenantData;
use App\Modules\Tenancy\Models\Tenant;
use App\Modules\Tenancy\Models\TenantDomain;
use App\Modules\Tenancy\Pipelines\TenantProvisioningPipeline;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class TenantService
 *
 * Application Service for managing tenant lifecycle, provisioning, domain verification, and status.
 */
class TenantService extends BaseService
{
    public function __construct(
        protected TenantRepositoryInterface $tenantRepository,
        protected TenantProvisioningPipeline $provisioningPipeline
    ) {}

    /**
     * Provision a brand-new tenant company workspace.
     *
     * @throws Throwable
     */
    public function provision(ProvisionTenantData $data): Tenant
    {
        $this->logInfo('Starting tenant provisioning pipeline', [
            'slug' => $data->slug,
            'company' => $data->companyName,
        ]);

        $tenant = $this->provisioningPipeline->handle($data);

        $this->logInfo('Tenant provisioning completed successfully', [
            'tenant_id' => $tenant->id,
            'slug' => $tenant->slug,
        ]);

        return $tenant;
    }

    /**
     * Toggle active/suspended state of a tenant.
     */
    public function toggleActiveStatus(int $tenantId): Tenant
    {
        /** @var Tenant $tenant */
        $tenant = $this->tenantRepository->findOrFail($tenantId);

        $tenant->update([
            'is_active' => ! $tenant->is_active,
        ]);

        $this->logInfo('Tenant status toggled', [
            'tenant_id' => $tenant->id,
            'is_active' => $tenant->is_active,
        ]);

        return $tenant;
    }

    /**
     * Add and initiate verification for a custom domain.
     */
    public function addCustomDomain(Tenant $tenant, string $domain): TenantDomain
    {
        $normalized = strtolower(trim($domain));

        return TenantDomain::create([
            'tenant_id' => $tenant->id,
            'domain' => $normalized,
            'is_primary' => false,
            'is_custom' => true,
            'is_verified' => false,
            'verification_token' => Str::random(40),
        ]);
    }

    /**
     * Verify a custom domain.
     */
    public function verifyCustomDomain(int $domainId): bool
    {
        $tenantDomain = TenantDomain::findOrFail($domainId);

        $tenantDomain->update([
            'is_verified' => true,
            'verified_at' => Carbon::now(),
        ]);

        return true;
    }
}
