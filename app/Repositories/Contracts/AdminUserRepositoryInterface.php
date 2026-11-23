<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Persistence contract for dashboard admin accounts (the users table).
 */
interface AdminUserRepositoryInterface
{
    /** Listing query for the Yajra table, roles eager-loaded. */
    public function listingQuery(): Builder;

    /** Find one admin or fail. */
    public function find(int $id): User;

    /** Persist a new admin account. */
    public function create(array $attributes): User;

    /** Persist changes to an existing admin account. */
    public function update(User $user, array $attributes): User;

    /** Remove an admin account. */
    public function delete(User $user): void;
}
