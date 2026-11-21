<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Tasks;

use App\Modules\Tenancy\Models\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

/**
 * Class SwitchTenantFilesystemTask
 *
 * Scopes tenant storage root to isolated directory: storage/app/tenants/{slug}
 */
class SwitchTenantFilesystemTask implements SwitchTenantTask
{
    public function makeCurrent(IsTenant $tenant): void
    {
        /** @var Tenant $tenant */
        $tenantPath = storage_path('app/tenants/'.$tenant->slug);

        Config::set('filesystems.disks.tenant', [
            'driver' => 'local',
            'root' => $tenantPath,
            'throw' => false,
        ]);

        Storage::forgetDisk('tenant');
    }

    public function forgetCurrent(): void
    {
        Config::set('filesystems.disks.tenant', null);
        Storage::forgetDisk('tenant');
    }
}
