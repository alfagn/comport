<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Permissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Additive RBAC seeder: safe on both fresh installs and existing databases.
 *
 * Everything is firstOrCreate/sync, so re-running never duplicates or wipes
 * anything. Existing admin users keep full access by receiving the "owner"
 * role; nothing about their accounts or content is touched.
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

        $owner = Role::findOrCreate(Permissions::OWNER_ROLE);
        $owner->syncPermissions(Permission::all());

        // Existing admins predate roles: keep them at full access.
        User::all()->each(function (User $user) use ($owner) {
            if (! $user->hasRole(Permissions::OWNER_ROLE)) {
                $user->assignRole($owner);
            }
        });
    }
}
