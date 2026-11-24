<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\AdminUserRepositoryInterface;
use App\Support\Permissions;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

/**
 * Admin-account management.
 *
 * Holds the safety rules around ownership so the controller stays thin:
 * the last owner can never be deleted or demoted, no one can delete their
 * own account from the table, and only an owner may grant or revoke the
 * owner role.
 */
class AdminUserService
{
    public function __construct(private AdminUserRepositoryInterface $repository) {}

    /** Create an admin account and attach the chosen roles. */
    public function create(array $attributes, array $roleNames, User $actor): User
    {
        abort_unless($actor->hasRole(Permissions::OWNER_ROLE), 403);
        $this->guardOwnerGrant($roleNames, $actor);

        $user = $this->repository->create($attributes);
        $user->syncRoles($roleNames);

        return $user;
    }

    /** Update an admin account and re-attach the chosen roles. */
    public function update(int $id, array $attributes, array $roleNames, User $actor): User
    {
        $user = $this->repository->find($id);

        abort_unless($actor->hasRole(Permissions::OWNER_ROLE), 403);
        $this->guardOwnerGrant($roleNames, $actor);
        $this->guardLastOwner($user, $roleNames);

        // Blank password on edit means "keep the current one".
        if (blank($attributes['password'] ?? null)) {
            unset($attributes['password']);
        }

        $this->repository->update($user, $attributes);
        $user->syncRoles($roleNames);

        return $user;
    }

    /** Delete an admin account. */
    public function delete(int $id, User $actor): void
    {
        abort_unless($actor->hasRole(Permissions::OWNER_ROLE), 403);
        $user = $this->repository->find($id);

        if ($user->is($actor)) {
            throw ValidationException::withMessages(['admin' => 'You cannot delete your own account.']);
        }

        // Guard: an owner cannot delete another owner
        if ($user->hasRole(Permissions::OWNER_ROLE) && $actor->hasRole(Permissions::OWNER_ROLE)) {
            throw ValidationException::withMessages(['admin' => 'You cannot delete another Owner account.']);
        }

        $this->guardLastOwner($user, []);

        $this->repository->delete($user);
    }

    /** Every selectable role for the form. */
    public function allRoles()
    {
        return Role::query()->withCount('users')->orderBy('name')->get();
    }

    /** Only an owner may grant or revoke the owner role. */
    private function guardOwnerGrant(array $roleNames, User $actor): void
    {
        if (in_array(Permissions::OWNER_ROLE, $roleNames, true) && ! $actor->hasRole(Permissions::OWNER_ROLE)) {
            throw ValidationException::withMessages(['roles' => 'Only an owner can grant the owner role.']);
        }
    }

    /** The site must always keep at least one owner. */
    private function guardLastOwner(User $user, array $newRoleNames): void
    {
        $staysOwner = in_array(Permissions::OWNER_ROLE, $newRoleNames, true);

        if ($user->hasRole(Permissions::OWNER_ROLE) && ! $staysOwner && $this->ownerCount() <= 1) {
            throw ValidationException::withMessages(['roles' => 'The site must keep at least one owner.']);
        }
    }

    private function ownerCount(): int
    {
        return User::role(Permissions::OWNER_ROLE)->count();
    }
}
