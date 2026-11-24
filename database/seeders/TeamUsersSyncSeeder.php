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
                'photo' => 'assets/img/team/mohanad-shehata.jpg',
            ],
            [
                'slug' => 'ayman',
                'name' => 'Ayman Ibrahim',
                'email' => 'ayman@empetix.com',
                'role' => 'Founder & Chief Executive Officer',
                'photo' => 'assets/img/team/ayman-ibrahim.jpg',
            ],
            [
                'slug' => 'bishoy',
                'name' => 'Bishoy Magdy',
                'email' => 'bishoy@empetix.com',
                'role' => 'Founder & Chief Operating Officer',
                'photo' => 'assets/img/team/bishoy-magdy.jpg',
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
                    'photo' => $member->photo ?: $fData['photo'],
                ]);
            } else {
                $member = Member::create([
                    'user_id' => $user->id,
                    'name' => $fData['name'],
                    'slug' => $fData['slug'],
                    'email' => $user->email,
                    'kind' => 'founder',
                    'role' => $fData['role'],
                    'photo' => $fData['photo'],
                    'department' => 'Executive Leadership',
                    'is_active' => true,
                    'sort_order' => 0,
                ]);
            }

            if ($user->member_id !== $member->id) {
                $user->update(['member_id' => $member->id]);
            }
        }

        // 2. Ensure all other Team members have clean company emails ready for selection in "New Admin"
        $members = Member::whereNotIn('slug', ['mohanad', 'ayman', 'bishoy'])->get();
        foreach ($members as $member) {
            $email = $member->email;
            if (empty($email) || str_contains($email, '&#64;') || $email === 'hello@empetix.com') {
                $cleanSlug = Str::slug($member->name);
                $email = "{$cleanSlug}@empetix.com";
                $member->update(['email' => $email]);
            }
        }
    }
}
