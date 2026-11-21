<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Finder;

use App\Modules\Tenancy\Models\Tenant;
use App\Modules\Tenancy\Models\TenantDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Multitenancy\Models\Tenant as SpatieTenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

/**
 * Class DomainTenantFinder
 *
 * Resolves current tenant from HTTP request host using:
 * 1. Verified Custom Domain match (e.g. portal.clientcorp.com)
 * 2. Primary Subdomain match (e.g. acme.empetix.test)
 * Central/Landlord domains return null, routing requests to the landlord context.
 */
class DomainTenantFinder extends TenantFinder
{
    /**
     * Central domains that belong to Landlord (Public Site + Super Admin).
     *
     * @var array<int, string>
     */
    protected array $centralDomains = [
        'empetix.test',
        'www.empetix.test',
        'localhost',
        '127.0.0.1',
    ];

    /**
     * Find tenant matching current HTTP request.
     */
    public function findForRequest(Request $request): ?SpatieTenant
    {
        $host = $this->normalizeHost($request->getHost());

        // Central domains are always landlord
        if ($this->isCentralDomain($host)) {
            return null;
        }

        // Cache resolution for 60 seconds to avoid repeated DB hits
        return Cache::remember("tenant_lookup_{$host}", 60, function () use ($host) {
            // 1. Direct domain match (Custom domain or full registered domain)
            $tenantDomain = TenantDomain::query()
                ->where('domain', $host)
                ->where(function ($query) {
                    $query->where('is_verified', true)
                        ->orWhere('is_custom', false);
                })
                ->first();

            if ($tenantDomain !== null) {
                return $tenantDomain->tenant;
            }

            // 2. Extract subdomain from host (e.g. acme from acme.empetix.test or acme.localhost)
            $subdomain = $this->extractSubdomain($host);
            if ($subdomain !== null && ! in_array($subdomain, ['www', 'admin', 'api', 'app'], true)) {
                return Tenant::query()->where('slug', $subdomain)->first();
            }

            return null;
        });
    }

    /**
     * Clean and normalize hostname.
     */
    protected function normalizeHost(string $host): string
    {
        $host = strtolower(trim($host));

        // Strip port if present
        if (str_contains($host, ':')) {
            $host = explode(':', $host)[0];
        }

        return $host;
    }

    /**
     * Determine if host is central landlord domain.
     */
    protected function isCentralDomain(string $host): bool
    {
        $configuredCentral = (array) config('multitenancy.central_domains', $this->centralDomains);

        return in_array($host, $configuredCentral, true);
    }

    /**
     * Extract tenant slug subdomain.
     */
    protected function extractSubdomain(string $host): ?string
    {
        $configuredCentral = (array) config('multitenancy.central_domains', $this->centralDomains);

        foreach ($configuredCentral as $central) {
            if (Str::endsWith($host, '.'.$central)) {
                return Str::before($host, '.'.$central);
            }
        }

        $parts = explode('.', $host);
        if (count($parts) >= 2) {
            return $parts[0];
        }

        return null;
    }
}
