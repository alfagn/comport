<?php

namespace App\Http\Middleware;

use App\Services\PageStatusService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures a requested public page has not been marked hidden/disabled in the CMS.
 * Aborts with an authentic 404 response if the page is turned off.
 */
class EnsurePageIsActive
{
    public function __construct(private PageStatusService $pageStatus) {}

    public function handle(Request $request, Closure $next, string $page): Response
    {
        abort_unless($this->pageStatus->isActive($page), 404);

        return $next($request);
    }
}
