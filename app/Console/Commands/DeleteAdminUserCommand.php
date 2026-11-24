<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\User;
use App\Support\Permissions;
use Illuminate\Console\Command;

class DeleteAdminUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:delete
                            {identifier? : Email address or numeric User ID to delete}
                            {--keep-member : Unlink and keep the team profile in members table instead of deleting it}
                            {--force : Delete immediately without confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a dashboard admin user (including Peer Owners) and clean up their linked team profile';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $identifier = $this->argument('identifier');

        if (empty($identifier)) {
            $users = User::with(['roles', 'member'])->orderBy('id')->get();
            if ($users->isEmpty()) {
                $this->warn('No users found in the database.');
                return self::FAILURE;
            }

            $this->table(
                ['ID', 'Name', 'Email', 'Roles', 'Team Profile Slug'],
                $users->map(fn (User $u) => [
                    $u->id,
                    $u->name,
                    $u->email,
                    $u->roles->pluck('name')->implode(', ') ?: 'none',
                    $u->member?->slug ?? '—',
                ])->all()
            );

            $identifier = $this->ask('Enter the Email address or ID of the user to delete');
        }

        if (empty($identifier)) {
            $this->error('User Email or ID is required.');
            return self::FAILURE;
        }

        $user = is_numeric($identifier)
            ? User::with(['roles', 'member'])->find((int) $identifier)
            : User::with(['roles', 'member'])->whereRaw('LOWER(email) = ?', [strtolower(trim((string) $identifier))])->first();

        if (! $user) {
            $this->error("No user found matching [{$identifier}].");
            return self::FAILURE;
        }

        // Prevent deleting the very last Owner in the system
        if ($user->hasRole(Permissions::OWNER_ROLE) && User::role(Permissions::OWNER_ROLE)->count() <= 1) {
            $this->error("Cannot delete [{$user->email}] because they are the last remaining Owner account.");
            return self::FAILURE;
        }

        if (! $this->option('force')) {
            $confirmed = $this->confirm(
                "Are you sure you want to permanently delete user [{$user->name} <{$user->email}>] (ID: {$user->id})?",
                false
            );

            if (! $confirmed) {
                $this->info('Deletion cancelled.');
                return self::SUCCESS;
            }
        }

        $linkedMember = Member::where('user_id', $user->id)
            ->orWhere(function ($q) use ($user) {
                $q->where('email', $user->email)->whereNull('user_id');
            })
            ->first();

        if ($linkedMember) {
            if ($this->option('keep-member')) {
                $linkedMember->update(['user_id' => null]);
                $this->info("Unlinked team profile [{$linkedMember->name}] (kept in members table).");
            } else {
                $memberName = $linkedMember->name;
                $linkedMember->delete();
                $this->info("Deleted linked team profile [{$memberName}].");
            }
        }

        $deletedEmail = $user->email;
        $deletedName = $user->name;

        $user->syncRoles([]);
        $user->delete();

        $this->info("✅ User [{$deletedName} <{$deletedEmail}>] has been deleted successfully.");

        return self::SUCCESS;
    }
}
