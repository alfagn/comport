<?php

namespace App\Services;

use App\Models\Member;
use App\Models\User;
use App\Repositories\Contracts\AdminUserRepositoryInterface;
use App\Support\Permissions;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

/**
 * Admin-account management.
 *
 * Team (`members`) is the single master directory of people; `users` holds
 * dashboard authentication (`member_id`, `password`, and `roles`).
 */
class AdminUserService
{
    public function __construct(private AdminUserRepositoryInterface $repository) {}

    /**
     * Team members who do not yet have an admin login, ordered for the "New Admin" selector.
     */
    public function availableTeamMembers(): Collection
    {
        $existingEmails = User::pluck('email')
            ->filter()
            ->map(fn (string $e) => strtolower(trim($e)))
            ->all();

        $existingMemberIds = User::whereNotNull('member_id')->pluck('member_id')->all();

        return Member::query()
            ->whereNull('user_id')
            ->when(! empty($existingMemberIds), fn ($q) => $q->whereNotIn('id', $existingMemberIds))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->filter(function (Member $m) use ($existingEmails) {
                if (empty($m->email)) {
                    return true;
                }

                return ! in_array(strtolower(trim($m->email)), $existingEmails, true);
            })
            ->values();
    }

    /** Create an admin account from a selected Team member (`member_id`) and attach roles. */
    public function create(array $attributes, array $roleNames, User $actor): User
    {
        abort_unless($actor->hasRole(Permissions::OWNER_ROLE), 403);
        $this->guardOwnerGrant($roleNames, $actor);

        $member = null;
        if (! empty($attributes['member_id'])) {
            $member = Member::findOrFail((int) $attributes['member_id']);
            $attributes['member_id'] = $member->id;
            $attributes['name'] = $member->name;
            $attributes['email'] = $member->email;
        } else {
            // Programmatic fallback: link or create corresponding Team member
            $member = Member::whereRaw('LOWER(email) = ?', [strtolower((string) ($attributes['email'] ?? ''))])->first();
            if (! $member && ! empty($attributes['name'])) {
                $baseSlug = Str::slug($attributes['name']) ?: 'member';
                $slug = $baseSlug;
                $i = 1;
                while (Member::where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-{$i}";
                    $i++;
                }
                $member = Member::create([
                    'name' => $attributes['name'],
                    'slug' => $slug,
                    'email' => $attributes['email'] ?? null,
                    'kind' => 'member',
                    'role' => 'Team Member',
                    'is_active' => false,
                    'sort_order' => 99,
                ]);
            }
            if ($member) {
                $attributes['member_id'] = $member->id;
            }
        }

        $user = $this->repository->create($attributes);
        $user->syncRoles($roleNames);

        if ($member) {
            $member->update(['user_id' => $user->id]);
        }

        return $user;
    }

    /** Update an admin account's password and roles (identity stays synced with Team). */
    public function update(int $id, array $attributes, array $roleNames, User $actor): User
    {
        $user = $this->repository->find($id);

        abort_unless($actor->hasRole(Permissions::OWNER_ROLE), 403);
        $this->guardOwnerGrant($roleNames, $actor);
        $this->guardLastOwner($user, $roleNames);

        $payload = [];

        if (! blank($attributes['password'] ?? null)) {
            $payload['password'] = $attributes['password'];
        }

        // Sync from linked Team member if present; otherwise preserve/accept explicit values
        $member = $user->member ?: ($user->member_id ? Member::find($user->member_id) : null);
        if ($member) {
            $payload['member_id'] = $member->id;
            $payload['name'] = $member->name;
            if (! empty($member->email)) {
                $payload['email'] = $member->email;
            }
        } else {
            if (! blank($attributes['name'] ?? null)) {
                $payload['name'] = $attributes['name'];
            }
            if (! blank($attributes['email'] ?? null)) {
                $payload['email'] = $attributes['email'];
            }
        }

        if (! empty($payload)) {
            $this->repository->update($user, $payload);
        }

        $user->syncRoles($roleNames);

        return $user;
    }

    /** Revoke admin access (keeps the person in Team & Founders). */
    public function delete(int $id, User $actor): void
    {
        abort_unless($actor->hasRole(Permissions::OWNER_ROLE), 403);
        $user = $this->repository->find($id);

        if ($user->is($actor)) {
            throw ValidationException::withMessages(['admin' => 'You cannot delete your own account.']);
        }

        if ($user->hasRole(Permissions::OWNER_ROLE) && $actor->hasRole(Permissions::OWNER_ROLE)) {
            throw ValidationException::withMessages(['admin' => 'You cannot delete another Owner account.']);
        }

        $this->guardLastOwner($user, []);

        // Unlink Team member so they stay in Team & Founders and can be re-added later
        Member::where('user_id', $user->id)->update(['user_id' => null]);

        $user->syncRoles([]);
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
