<?php

namespace Database\Seeders;

use App\Support\Permissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Additive RBAC seeder: safe on both fresh installs and existing databases.
 *
 * Creates the permission catalog and the owner role ONLY. It never assigns
 * roles on its own: granting access is an explicit operator decision, done
 * per account through `php artisan empetix:owner <email>` (or the Admins
 * screen). Everything is findOrCreate/sync, so re-running is always safe.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset the cached permission lookup so freshly created rows resolve.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (array_keys(Permissions::all()) as $name) {
            Permission::findOrCreate($name);
        }

        Role::findOrCreate(Permissions::OWNER_ROLE)
            ->syncPermissions(Permission::all());
    }
}
