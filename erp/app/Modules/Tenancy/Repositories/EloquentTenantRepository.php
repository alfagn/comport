<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Repositories;

use App\Core\Repositories\BaseEloquentRepository;
use App\Modules\Tenancy\Contracts\TenantRepositoryInterface;
use App\Modules\Tenancy\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class EloquentTenantRepository
 *
 * @extends BaseEloquentRepository<Tenant>
 */
class EloquentTenantRepository extends BaseEloquentRepository implements TenantRepositoryInterface
{
    public function __construct(Tenant $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritDoc}
     */
    public function findBySlug(string $slug): ?Tenant
    {
        return $this->query()->where('slug', $slug)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function getActiveTenants(): Collection
    {
        return $this->query()->where('is_active', true)->get();
    }
}
