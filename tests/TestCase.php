<?php

namespace Tests;

use App\Models\User;
use App\Support\Permissions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    /**
     * A dashboard admin holding the owner role (full access). Tests that
     * exercise permission denial create their own restricted users instead.
     */
    protected function owner(): User
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        Role::findOrCreate(Permissions::OWNER_ROLE);
        $user = User::factory()->create();
        $user->assignRole(Permissions::OWNER_ROLE);

        return $user;
    }
}
