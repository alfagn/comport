<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\Permissions;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Console\Command;

/**
 * Explicitly grant the owner role to one named admin account.
 *
 * Owner assignment is always an operator decision about a specific account;
 * nothing in the app grants it implicitly or in bulk.
 */
class GrantOwnerRole extends Command
{
    protected $signature = 'empetix:owner {email : The admin account email to make an owner}';

    protected $description = 'Grant the owner role (full dashboard access) to one admin account';

    public function handle(): int
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('No admin account with that email.');

            return self::FAILURE;
        }

        if (! $user->hasRole(Permissions::OWNER_ROLE)) {
            $user->assignRole(Permissions::OWNER_ROLE);
        }

        $this->info("{$user->email} is now an owner.");

        return self::SUCCESS;
    }
}
