<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
use App\Support\Permissions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class TeamUsersSyncSeeder extends Seeder
{
    public function run(): void
    {
        $ownerRole = Role::firstOrCreate(['name' => Permissions::OWNER_ROLE], ['guard_name' => 'web']);
        $editorRole = Role::firstOrCreate(['name' => 'editor'], ['guard_name' => 'web']);

        $defaultPassword = Hash::make('password123');

        // 1. Explicit Founders as Owners
        $founders = [
            [
                'slug' => 'mohanad',
                'name' => 'Mohanad Ahmed Shehata',
                'email' => 'mohand@empetix.com',
                'role' => 'Founder & Chief Technology Officer',
            ],
            [
                'slug' => 'ayman',
                'name' => 'Ayman Ibrahim',
                'email' => 'ayman@empetix.com',
                'role' => 'Founder & Chief Executive Officer',
            ],
            [
                'slug' => 'bishoy',
                'name' => 'Bishoy Magdy',
                'email' => 'bishoy@empetix.com',
                'role' => 'Founder & Chief Operating Officer',
            ],
        ];

        foreach ($founders as $fData) {
            $user = User::updateOrCreate(
                ['email' => $fData['email']],
                [
                    'name' => $fData['name'],
                    'password' => $defaultPassword,
                ]
            );

            if (! $user->hasRole(Permissions::OWNER_ROLE)) {
                $user->syncRoles([$ownerRole]);
            }

            // Sync with Member record
            $member = Member::where('slug', $fData['slug'])->first();
            if ($member) {
                $member->update([
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'kind' => 'founder',
                    'role' => $fData['role'],
                ]);
            } else {
                Member::create([
                    'user_id' => $user->id,
                    'name' => $fData['name'],
                    'slug' => $fData['slug'],
                    'email' => $user->email,
                    'kind' => 'founder',
                    'role' => $fData['role'],
                    'department' => 'Executive Leadership',
                    'is_active' => true,
                    'sort_order' => 0,
                ]);
            }
        }

        // 2. Sync all other team members into dashboard Users
        $members = Member::whereNotIn('slug', ['mohanad', 'ayman', 'bishoy'])->get();
        foreach ($members as $member) {
            $email = $member->email;
            if (empty($email) || str_contains($email, '&#64;') || $email === 'hello@empetix.com') {
                $cleanSlug = Str::slug($member->name);
                $email = "{$cleanSlug}@empetix.com";
            }

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $member->name,
                    'password' => $defaultPassword,
                ]
            );

            if ($user->roles->isEmpty()) {
                $user->assignRole($editorRole);
            }

            $member->update([
                'user_id' => $user->id,
                'email' => $email,
            ]);
        }
    }
}
