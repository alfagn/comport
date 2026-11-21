<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Middleware;

use App\Modules\Tenancy\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NeedsTenant
 *
 * Ensures request has an active resolved tenant context.
 */
class NeedsTenant
{
    /**
     * Handle incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Tenant::current();

        if ($tenant === null) {
            abort(Response::HTTP_NOT_FOUND, 'Company workspace not found.');
        }

        return $next($request);
    }
}
