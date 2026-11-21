<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Contracts;

use App\Core\Contracts\RepositoryInterface;
use App\Modules\Tenancy\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface TenantRepositoryInterface
 *
 * @extends RepositoryInterface<Tenant>
 */
interface TenantRepositoryInterface extends RepositoryInterface
{
    /**
     * Find tenant by slug identifier.
     */
    public function findBySlug(string $slug): ?Tenant;

    /**
     * Retrieve all active tenants.
     *
     * @return Collection<int, Tenant>
     */
    public function getActiveTenants(): Collection;
}
