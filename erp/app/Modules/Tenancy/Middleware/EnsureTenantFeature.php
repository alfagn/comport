<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Middleware;

use App\Modules\Tenancy\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EnsureTenantFeature
 *
 * Verifies that the tenant subscription plan includes the requested feature module.
 * Usage: middleware('tenant.feature:marketing') or middleware('tenant.feature:hrm')
 */
class EnsureTenantFeature
{
    /**
     * Handle incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        /** @var Tenant|null $tenant */
        $tenant = Tenant::current();

        if ($tenant === null) {
            abort(Response::HTTP_NOT_FOUND, 'Tenant context not resolved.');
        }

        if (! $tenant->hasFeature($feature)) {
            abort(Response::HTTP_FORBIDDEN, sprintf(
                'The "%s" module is not included in your current subscription plan. Please upgrade to unlock.',
                ucfirst($feature)
            ));
        }

        return $next($request);
    }
}
