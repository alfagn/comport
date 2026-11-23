<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Server-side RBAC: section permissions gate every admin route, the owner
 * role bypasses, and the ownership safety rules hold.
 */
class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        // Spatie caches roles/permissions process-wide; RefreshDatabase rolls
        // rows back between tests, so drop the cache to avoid stale IDs.
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_owner_reaches_every_admin_section(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole(Permissions::OWNER_ROLE);

        foreach (['/admin/services', '/admin/admins', '/admin/roles', '/admin/settings', '/admin/inbox'] as $url) {
            $this->actingAs($owner)->get($url)->assertOk();
        }
    }

    public function test_admin_without_permission_is_blocked_server_side(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'editor']);
        $role->givePermissionTo('manage services');
        $user->assignRole($role);

        // Allowed section loads.
        $this->actingAs($user)->get('/admin/services')->assertOk();
        // Everything else is blocked by the server, not just hidden in the UI.
        $this->actingAs($user)->get('/admin/admins')->assertForbidden();
        $this->actingAs($user)->get('/admin/settings')->assertForbidden();
        $this->actingAs($user)->get('/admin/inbox')->assertForbidden();
    }

    public function test_owner_can_create_admin_and_assign_role(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole(Permissions::OWNER_ROLE);

        $this->actingAs($owner)->post('/admin/admins', [
            'name' => 'Second Admin',
            'email' => 'second@example.com',
            'password' => 'password-123',
            'password_confirmation' => 'password-123',
            'roles' => [Permissions::OWNER_ROLE],
        ])->assertRedirect(route('admin.admins.index'));

        $this->assertTrue(User::where('email', 'second@example.com')->first()->hasRole(Permissions::OWNER_ROLE));
    }

    public function test_non_owner_cannot_grant_owner_role(): void
    {
        $manager = User::factory()->create();
        $role = Role::create(['name' => 'manager']);
        $role->givePermissionTo('manage admins');
        $manager->assignRole($role);

        $this->actingAs($manager)->post('/admin/admins', [
            'name' => 'Escalation',
            'email' => 'escalation@example.com',
            'password' => 'password-123',
            'password_confirmation' => 'password-123',
            'roles' => [Permissions::OWNER_ROLE],
        ])->assertSessionHasErrors('roles');
    }

    public function test_last_owner_cannot_be_demoted_or_deleted(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole(Permissions::OWNER_ROLE);
        $editor = Role::create(['name' => 'editor2']);
        $editor->givePermissionTo('manage posts');

        // Suite order-independent: other test classes may leave seeded owners
        // behind, so demote every other owner until $owner is the last one.
        foreach (User::role(Permissions::OWNER_ROLE)->whereKeyNot($owner->id)->get() as $other) {
            $this->actingAs($owner)->put('/admin/admins/'.$other->id, [
                'name' => $other->name,
                'email' => $other->email,
                'roles' => ['editor2'],
            ])->assertRedirect(route('admin.admins.index'));
        }

        $this->actingAs($owner)->put('/admin/admins/'.$owner->id, [
            'name' => $owner->name,
            'email' => $owner->email,
            'roles' => ['editor2'],
        ])->assertSessionHasErrors('roles');

        $this->actingAs($owner)->delete('/admin/admins/'.$owner->id)->assertSessionHasErrors();
    }

    public function test_owner_role_cannot_be_deleted_and_assigned_role_cannot_be_deleted(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole(Permissions::OWNER_ROLE);

        $this->actingAs($owner)->delete('/admin/roles/'.Role::findByName(Permissions::OWNER_ROLE)->id)
            ->assertSessionHasErrors('role');

        $this->actingAs($owner)->post('/admin/roles', [
            'name' => 'content',
            'permissions' => ['manage posts'],
        ])->assertRedirect(route('admin.roles.index'));

        $role = Role::findByName('content');
        $owner->assignRole('content');
        $this->actingAs($owner)->delete('/admin/roles/'.$role->id)->assertSessionHasErrors('role');
    }

    public function test_seeder_is_idempotent_and_grants_existing_users_owner(): void
    {
        $user = User::factory()->create();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->assertTrue($user->fresh()->hasRole(Permissions::OWNER_ROLE));
        $this->assertEquals(count(Permissions::all()), Permission::count());
    }
}
