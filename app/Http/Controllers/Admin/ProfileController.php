<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the authenticated user's profile edit form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $member = $user->member ?: Member::where('email', $user->email)->first();

        if (! $member) {
            $slug = Str::slug($user->name);
            $member = Member::firstOrCreate(
                ['email' => $user->email],
                [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'slug' => $slug,
                    'kind' => $user->hasRole('owner') ? 'founder' : 'member',
                    'role' => $user->hasRole('owner') ? 'Founder & Principal Architect' : 'Staff Software Engineer',
                    'department' => 'Software Engineering',
                    'focus' => 'Cloud Systems & High-Concurrency APIs',
                    'is_active' => true,
                    'sort_order' => 0,
                ]
            );
        }

        if (! $member->user_id) {
            $member->update(['user_id' => $user->id]);
        }

        return view('admin.profile.edit', compact('user', 'member'));
    }

    /**
     * Update the authenticated user's account and public team profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['nullable', 'string', 'max:160'],
            'department' => ['nullable', 'string', 'max:100'],
            'focus' => ['nullable', 'string', 'max:255'],
            'lead' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'twitter' => ['nullable', 'url', 'max:255'],
        ]);

        // 1. Update User Login Credentials
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        // 2. Update or Create Member Team Profile
        $member = $user->member ?: Member::where('email', $user->email)->first();
        if (! $member) {
            $member = new Member();
            $member->user_id = $user->id;
            $member->slug = Str::slug($validated['name']);
            $member->kind = $user->hasRole('owner') ? 'founder' : 'member';
            $member->is_active = true;
        }

        // Photo upload
        if ($request->hasFile('photo')) {
            if ($member->photo && Storage::disk('public')->exists($member->photo)) {
                Storage::disk('public')->delete($member->photo);
            }
            $member->photo = $request->file('photo')->store('uploads/members', 'public');
        }

        // Format bio as array of paragraphs
        $bioParagraphs = null;
        if (! empty($validated['bio'])) {
            $bioParagraphs = array_values(array_filter(array_map('trim', preg_split('/\r?\n\s*\r?\n/', $validated['bio']))));
        }

        $member->name = $validated['name'];
        $member->email = $validated['email'];
        $member->role = $validated['role'] ?? $member->role;
        $member->department = $validated['department'] ?? $member->department;
        $member->focus = $validated['focus'] ?? $member->focus;
        $member->lead = $validated['lead'] ?? $member->lead;
        if ($bioParagraphs !== null) {
            $member->bio = $bioParagraphs;
        }
        $member->linkedin = $validated['linkedin'] ?? $member->linkedin;
        $member->twitter = $validated['twitter'] ?? $member->twitter;
        $member->save();

        return redirect()->route('admin.profile.edit')->with('success', 'Your profile and team details have been updated.');
    }
}
