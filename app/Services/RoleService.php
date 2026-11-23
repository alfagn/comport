<?php

namespace App\Services;

use App\Support\Permissions;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

/**
 * Role management.
 *
 * Safety rules: the owner role is structural — it cannot be renamed,
 * edited or deleted — and a role still assigned to admins cannot be
 * deleted.
 */
class RoleService
{
    /** Create a role and sync its permissions. */
    public function create(array $attributes, array $permissions): Role
    {
        $role = Role::create($attributes);
        $role->syncPermissions($permissions);

        return $role;
    }

    /** Update a role and re-sync its permissions. */
    public function update(int $id, array $attributes, array $permissions): Role
    {
        $role = Role::findOrFail($id);
        $this->guardOwnerRole($role);

        $role->update($attributes);
        $role->syncPermissions($permissions);

        return $role;
    }

    /** Delete a role that no admin still holds. */
    public function delete(int $id): void
    {
        $role = Role::findOrFail($id);
        $this->guardOwnerRole($role);

        if ($role->users()->exists()) {
            throw ValidationException::withMessages(['role' => 'This role is still assigned to admins.']);
        }

        $role->delete();
    }

    /** The owner role is structural and always keeps full access. */
    private function guardOwnerRole(Role $role): void
    {
        if ($role->name === Permissions::OWNER_ROLE) {
            throw ValidationException::withMessages(['role' => 'The owner role cannot be changed.']);
        }
    }
}
