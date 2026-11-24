@extends('layouts.admin')

@section('title', 'My Profile')
@section('crumb', 'Profile')

@section('content')
<div class="card" style="max-width: 960px; margin: 0 auto;">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color, #eee);">
    <div>
      <h2 style="font-size: 1.35rem; font-weight: 700; margin: 0;">Account &amp; Team Profile</h2>
      <p style="color: var(--text-muted, #777); font-size: 0.88rem; margin: 0.25rem 0 0;">Manage your login credentials and how you appear in the company team directory.</p>
    </div>
    <div style="display:flex; align-items:center; gap:10px;">
      @if($member->slug && $member->kind === 'founder')
        <a href="{{ route('founders.show', $member->slug) }}" target="_blank" rel="noopener noreferrer" class="btn btn--ghost btn--sm" style="display:inline-flex; align-items:center; gap:5px;">
          <span>View Public Profile ↗</span>
        </a>
      @elseif($member->is_active)
        <a href="{{ url('/team') }}" target="_blank" rel="noopener noreferrer" class="btn btn--ghost btn--sm" style="display:inline-flex; align-items:center; gap:5px;">
          <span>View on Team Page ↗</span>
        </a>
      @endif

      @if($member->is_active)
        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-weight: 600; padding: 6px 14px; border-radius: 20px; font-size: 0.82rem; display:inline-flex; align-items:center; gap:6px;">
          <span style="width:8px; height:8px; border-radius:50%; background:#10b981; display:inline-block;"></span>
          Visible on Website
        </span>
      @else
        <span class="badge" style="background: rgba(107, 114, 128, 0.15); color: #6b7280; font-weight: 600; padding: 6px 14px; border-radius: 20px; font-size: 0.82rem; display:inline-flex; align-items:center; gap:6px;">
          Hidden from Website
        </span>
      @endif
    </div>
  </div>

  <form method="post" enctype="multipart/form-data" action="{{ route('admin.profile.update') }}">
    @csrf
    @method('PUT')

    <h3 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 1rem; color: var(--color-text-accent, #eb5e28);">1. Login &amp; Security Credentials</h3>
    <div class="form-grid">
      <x-admin.input name="name" label="Full Name" :value="old('name', $user->name)" :required="true"/>
      <x-admin.input name="email" label="Email Address" type="email" :value="old('email', $user->email)" :required="true"/>
      <x-admin.input name="password" label="New Password" type="password" hint="Leave blank to keep your current password."/>
      <x-admin.input name="password_confirmation" label="Confirm New Password" type="password"/>
    </div>

    <h3 style="font-size: 1.05rem; font-weight: 600; margin: 2rem 0 1rem; color: var(--color-text-accent, #eb5e28);">2. Public Team &amp; Founder Profile</h3>
    <div class="form-grid">
      <div class="full" style="display:flex; align-items:center; gap: 20px; margin-bottom: 1rem;">
        <div style="width: 80px; height: 80px; border-radius: 50%; overflow:hidden; background: #eee; flex-shrink:0; border: 2px solid var(--color-line, #ddd);">
          @if($member->photo)
            <img src="{{ str_starts_with((string) $member->photo, 'uploads/') ? asset('storage/'.$member->photo) : asset($member->photo) }}" alt="{{ $user->name }}" style="width:100%; height:100%; object-fit:cover;">
          @else
            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:1.8rem; font-weight:700; color:#555; background:rgba(235,94,40,0.1);">
              {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
          @endif
        </div>
        <div style="flex:1;">
          <label style="display:block; font-weight:600; font-size:0.9rem; margin-bottom:4px;">Profile Photo</label>
          <input type="file" name="photo" accept="image/*" class="form-control" style="font-size:0.85rem;">
          <p style="font-size:0.78rem; color:var(--text-muted, #777); margin-top:4px;">Square photo recommended (min 400x400px).</p>
        </div>
      </div>

      <x-admin.input name="role" label="Professional Title / Role" :value="old('role', $member->role)" placeholder="e.g. Founder & Principal Architect"/>
      <x-admin.input name="department" label="Department" :value="old('department', $member->department)" placeholder="e.g. Software Engineering"/>
      
      <div class="full">
        <x-admin.input name="focus" label="Core Focus / Specialty Headline" :value="old('focus', $member->focus)" placeholder="e.g. High-concurrency systems, WebRTC live classroom engines, distributed APIs"/>
      </div>

      @if($member->kind === 'founder' || $user->hasRole('owner'))
        <div class="full">
          <x-admin.textarea name="lead" label="Founder Executive Summary (Lead)" :value="old('lead', $member->lead)" rows="3" hint="Prominent opening statement shown at the top of your Founder page."/>
        </div>
      @endif

      <div class="full">
        <x-admin.textarea name="bio" label="Full Biography / Craft Story (Paragraphs separated by blank lines)" :value="old('bio', is_array($member->bio) ? implode(PHP_EOL.PHP_EOL, $member->bio) : $member->bio)" rows="6" hint="Your engineering background, technical craft, and achievements."/>
      </div>

      <x-admin.input name="linkedin" label="LinkedIn Profile URL" :value="old('linkedin', $member->linkedin)" placeholder="https://linkedin.com/in/..."/>
      <x-admin.input name="twitter" label="X (Twitter) Profile URL" :value="old('twitter', $member->twitter)" placeholder="https://x.com/..."/>
    </div>

    <div style="background: rgba(235, 94, 40, 0.06); border: 1px dashed rgba(235, 94, 40, 0.3); border-radius: 8px; padding: 12px 16px; margin: 1.5rem 0; font-size: 0.85rem; color: var(--color-text, #333);">
      <strong>Notice:</strong> Your profile visibility in the company team directory on the public website is controlled by the Super Admin in the <strong><a href="{{ route('admin.members.index') }}" style="color:var(--color-text-accent, #eb5e28); text-decoration:underline;">Team Members Table</a></strong> via the Status switch.
    </div>

    <div class="form-actions" style="margin-top: 1.5rem;">
      <button class="btn btn--primary" type="submit">Save Profile Changes</button>
      <a class="btn btn--ghost" href="{{ route('admin.dashboard') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection
