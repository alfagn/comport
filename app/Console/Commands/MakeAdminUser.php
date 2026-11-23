<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Interactive dashboard user management. Passwords are typed at the
 * prompt (hidden input) and never appear in code, seeds, logs or docs.
 *
 *   php artisan empetix:admin            create a new dashboard user
 *   php artisan empetix:admin --reset    reset an existing user's password
 */
class MakeAdminUser extends Command
{
    protected $signature = 'empetix:admin {--reset : Reset the password of an existing user}';

    protected $description = 'Create a dashboard user (or reset a password) with an interactive hidden password prompt';

    public function handle(): int
    {
        $email = $this->ask('Dashboard email', 'admin@empetix.com');

        $existing = User::where('email', $email)->first();

        if ($existing && ! $this->option('reset')) {
            $this->warn("A user with {$email} already exists. Re-run with --reset to change the password.");

            return self::FAILURE;
        }

        if (! $existing && $this->option('reset')) {
            $this->error("No user found with {$email}.");

            return self::FAILURE;
        }

        $name = $existing?->name ?? $this->ask('Name', 'Empetix Admin');

        // Hidden prompt; confirmation required. Nothing is echoed or logged.
        $password = $this->secret('Password (min 12 characters)');
        $confirm = $this->secret('Confirm password');

        $validator = Validator::make(
            ['password' => $password, 'password_confirmation' => $confirm],
            ['password' => ['required', 'string', 'min:12', 'confirmed']]
        );

        if ($validator->fails()) {
            $this->error($validator->errors()->first('password'));

            return self::FAILURE;
        }

        User::updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => Hash::make($password)]
        );

        $this->info($existing ? "Password updated for {$email}." : "Dashboard user {$email} created.");

        return self::SUCCESS;
    }
}
