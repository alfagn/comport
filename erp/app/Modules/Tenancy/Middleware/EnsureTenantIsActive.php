<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Middleware;

use App\Modules\Tenancy\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EnsureTenantIsActive
 *
 * Checks that the resolved tenant company is in active status (not suspended or deactivated).
 */
class EnsureTenantIsActive
{
    /**
     * Handle incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Tenant|null $tenant */
        $tenant = Tenant::current();

        if ($tenant !== null && ! $tenant->isActive()) {
            abort(Response::HTTP_FORBIDDEN, 'This company workspace has been suspended. Please contact support.');
        }

        return $next($request);
    }
}
