<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Middleware;

use App\Modules\Tenancy\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ResolvePathTenant
 *
 * Resolves and activates tenant from route parameter {tenant_slug}
 * Enabling local path-based multitenancy: /t/{tenant_slug}/...
 */
class ResolvePathTenant
{
    /**
     * Handle incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $slug = (string) $request->route('tenant_slug');

        /** @var Tenant|null $tenant */
        $tenant = Tenant::on('landlord')->where('slug', $slug)->first();

        if ($tenant === null) {
            abort(Response::HTTP_NOT_FOUND, 'Company workspace not found.');
        }

        $tenant->makeCurrent();

        return $next($request);
    }
}
