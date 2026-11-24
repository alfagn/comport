<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\User;
use App\Support\Permissions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class CreateAdminUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create
                            {name? : Full name of the user}
                            {email? : Email address used for dashboard login}
                            {password? : Plain text password (min 8 chars)}
                            {--role=owner : Role to assign (owner, editor, author)}
                            {--founder : Mark this team member as a Founder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new dashboard admin user and link their team profile';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name') ?: $this->ask('Full Name');
        $email = $this->argument('email') ?: $this->ask('Email address');
        $password = $this->argument('password') ?: $this->secret('Password');
        $roleName = $this->option('role') ?: 'owner';
        $isFounder = (bool) $this->option('founder');

        if (empty($name) || empty($email) || empty($password)) {
            $this->error('Name, email, and password are required.');
            return self::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->error("A user with email [{$email}] already exists.");
            return self::FAILURE;
        }

        // Ensure role exists
        $role = Role::firstOrCreate(['name' => $roleName], ['guard_name' => 'web']);

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $user->assignRole($role);

        // Link or create Member record
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;
        while (Member::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        $member = Member::where('email', $email)->first();
        if ($member) {
            $member->update(['user_id' => $user->id]);
            $this->info("Linked existing team profile [{$member->name}] to this user.");
        } else {
            $member = Member::create([
                'user_id' => $user->id,
                'name' => $name,
                'slug' => $slug,
                'email' => $email,
                'kind' => $isFounder ? 'founder' : 'member',
                'role' => $isFounder ? 'Founder & Principal Architect' : 'Staff Software Engineer',
                'department' => 'Software Engineering',
                'focus' => 'Cloud Platforms, Distributed Systems & High-Concurrency APIs',
                'is_active' => true,
                'sort_order' => 0,
            ]);
            $this->info("Created linked team profile [{$member->name}] (Kind: {$member->kind}).");
        }

        $this->newLine();
        $this->info("✅ User created successfully!");
        $this->table(
            ['ID', 'Name', 'Email', 'Role', 'Team Profile', 'Status on Site'],
            [[$user->id, $user->name, $user->email, $roleName, $member->role ?? 'Linked', $member->is_active ? 'Visible (Live)' : 'Hidden']]
        );
        $this->comment("Super Admins can toggle the visibility of this user in the Team table (/admin/members).");

        return self::SUCCESS;
    }
}
