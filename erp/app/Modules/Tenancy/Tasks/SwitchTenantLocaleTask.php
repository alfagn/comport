<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Tasks;

use App\Modules\Tenancy\Models\Tenant;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

/**
 * Class SwitchTenantLocaleTask
 *
 * Configures application locale, timezone, and currency to match tenant preferences.
 */
class SwitchTenantLocaleTask implements SwitchTenantTask
{
    public function makeCurrent(IsTenant $tenant): void
    {
        /** @var Tenant $tenant */
        if (! empty($tenant->locale)) {
            App::setLocale($tenant->locale);
        }

        if (! empty($tenant->timezone)) {
            date_default_timezone_set($tenant->timezone);
            Config::set('app.timezone', $tenant->timezone);
        }
    }

    public function forgetCurrent(): void
    {
        App::setLocale(config('app.fallback_locale', 'en'));
    }
}
