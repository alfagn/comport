<?php

declare(strict_types=1);

namespace App\Modules\Billing\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class BillingServiceProvider
 *
 * Bootstraps SaaS billing, plans, and subscription infrastructure.
 */
class BillingServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void {}
}
