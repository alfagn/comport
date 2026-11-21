<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\DTOs;

use App\Core\DTOs\BaseData;
use App\Modules\Tenancy\Models\Tenant;

/**
 * Class ProvisionTenantData
 *
 * Immutable DTO containing parameters for tenant provisioning pipeline.
 */
final readonly class ProvisionTenantData extends BaseData
{
    public function __construct(
        public string $companyName,
        public string $slug,
        public string $ownerName,
        public string $ownerEmail,
        public string $ownerPassword,
        public ?string $phone = null,
        public ?string $customDomain = null,
        public ?int $planId = null,
        public bool $isInternal = false,
        public string $locale = 'ar',
        public string $currency = 'EGP',
        public string $timezone = 'Africa/Cairo',
        public int $trialDays = 14
    ) {}
}
