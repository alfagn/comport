<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\AdminUserRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * Eloquent implementation of the admin-account persistence contract.
 */
class AdminUserRepository implements AdminUserRepositoryInterface
{
    public function listingQuery(): Builder
    {
        return User::query()->with('roles')->select('users.*');
    }

    public function find(int $id): User
    {
        return User::query()->with('roles')->findOrFail($id);
    }

    public function create(array $attributes): User
    {
        return User::query()->create($attributes);
    }

    public function update(User $user, array $attributes): User
    {
        $user->update($attributes);

        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
