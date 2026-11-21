<?php

declare(strict_types=1);

namespace App\Modules\Billing\Services;

use App\Modules\Tenancy\Models\Tenant;

/**
 * Class FeatureGate
 *
 * Central service for checking plan-level features and resource usage ceilings.
 */
class FeatureGate
{
    /**
     * Determine if feature is enabled for current tenant.
     */
    public static function allows(string $feature): bool
    {
        /** @var Tenant|null $tenant */
        $tenant = Tenant::current();

        if ($tenant === null) {
            return false;
        }

        return $tenant->hasFeature($feature);
    }

    /**
     * Determine if tenant can create another unit of a limited resource.
     */
    public static function canCreate(string $resourceKey, int $currentCount): bool
    {
        /** @var Tenant|null $tenant */
        $tenant = Tenant::current();

        if ($tenant === null) {
            return false;
        }

        return $tenant->canUseResource($resourceKey, $currentCount);
    }
}
