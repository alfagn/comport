<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Middleware;

use App\Modules\Billing\Models\Subscription;
use App\Modules\Tenancy\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EnsureSubscriptionIsActive
 *
 * Verifies that the tenant has an active subscription or is in an active trial period.
 */
class EnsureSubscriptionIsActive
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

        if ($tenant === null) {
            return $next($request);
        }

        // Internal holding company is always exempt from billing checks
        if ($tenant->isInternal()) {
            return $next($request);
        }

        /** @var Subscription|null $subscription */
        $subscription = $tenant->currentSubscription;

        // If trial has not expired, allow access
        if ($tenant->trial_ends_at !== null && $tenant->trial_ends_at->isFuture()) {
            return $next($request);
        }

        // If subscription is active or trialing
        if ($subscription !== null && in_array($subscription->status, ['active', 'trialing'], true)) {
            return $next($request);
        }

        // Allow access to subscription / payment page so tenant can renew
        if ($request->routeIs('tenant.subscription.*') || $request->is('subscription*')) {
            return $next($request);
        }

        abort(Response::HTTP_PAYMENT_REQUIRED, 'Subscription expired. Please renew your subscription to access this workspace.');
    }
}
