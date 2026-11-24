@extends('layouts.admin')

@section('title', 'My Profile')
@section('crumb', 'Profile')

@section('content')
<div class="card" style="max-width: 960px; margin: 0 auto;">
  <form method="post" enctype="multipart/form-data" action="{{ route('admin.profile.update') }}" id="profile-form">
    @csrf
    @method('PUT')

    {{-- Top Identity Hero & Photo Upload Zone (Photo First) --}}
    <div class="profile-hero-zone" style="background: var(--surface-2, rgba(235, 94, 40, 0.04)); border: 1px solid var(--line, rgba(235, 94, 40, 0.15)); border-radius: 16px; padding: 24px 28px; margin-bottom: 2rem;">
      <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap: 24px;">
        
        {{-- Avatar & Quick Controls --}}
        <div style="display:flex; align-items:center; gap: 22px;">
          <div class="profile-avatar-wrap" style="position:relative; width: 110px; height: 110px; flex-shrink: 0; cursor: pointer;" onclick="document.getElementById('profile-photo-input').click()" title="Click to change profile picture">
            <div id="avatar-container" style="width: 100%; height: 100%; border-radius: 50%; overflow: hidden; border: 3px solid var(--color-text-accent, #eb5e28); box-shadow: 0 8px 24px rgba(235, 94, 40, 0.25); background: var(--surface, #1e2430); display:flex; align-items:center; justify-content:center;">
              @if($member->photo)
                <img id="avatar-preview-img" src="{{ str_starts_with((string) $member->photo, 'uploads/') ? asset('storage/'.$member->photo) : asset($member->photo) }}" alt="{{ $user->name }}" style="width:100%; height:100%; object-fit:cover; display:block;">
                <span id="avatar-preview-fallback" style="display:none; font-size:2.4rem; font-weight:800; color:var(--color-text-accent, #eb5e28);">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
              @else
                <img id="avatar-preview-img" src="" alt="{{ $user->name }}" style="width:100%; height:100%; object-fit:cover; display:none;">
                <span id="avatar-preview-fallback" style="display:block; font-size:2.4rem; font-weight:800; color:var(--color-text-accent, #eb5e28);">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
              @endif
            </div>

            {{-- Floating Camera Action Badge --}}
            <div style="position:absolute; bottom: 2px; right: 2px; width: 34px; height: 34px; border-radius: 50%; background: #eb5e28; color: #fff; display:flex; align-items:center; justify-content:center; box-shadow: 0 3px 10px rgba(0,0,0,0.3); border: 2px solid var(--surface, #fff); transition: transform 0.2s ease;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                <circle cx="12" cy="13" r="4"></circle>
              </svg>
            </div>
          </div>

          {{-- Identity Meta & Upload Trigger --}}
          <div>
            <div style="display:flex; align-items:center; gap: 8px; margin-bottom: 4px;">
              <h2 style="font-size: 1.35rem; font-weight: 700; margin: 0; color: var(--text, #111);">{{ $user->name }}</h2>
              @if($user->hasRole('owner'))
                <span style="font-size: 0.72rem; padding: 2px 8px; border-radius: 999px; background: rgba(235, 94, 40, 0.15); color: #eb5e28; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">
                  👑 Founder / Owner
                </span>
              @else
                <span style="font-size: 0.72rem; padding: 2px 8px; border-radius: 999px; background: rgba(255, 255, 255, 0.08); color: var(--text-muted, #888); font-weight: 600;">
                  Staff Member
                </span>
              @endif
            </div>

            <p style="color: var(--text-muted, #777); font-size: 0.88rem; margin: 0 0 10px;">
              {{ $member->role ?? 'Software Engineering Leader' }} &bull; {{ $user->email }}
            </p>

            <div style="display:flex; flex-wrap:wrap; align-items:center; gap: 10px;">
              {{-- Custom Styled Upload Trigger --}}
              <input type="file" id="profile-photo-input" name="photo" accept="image/*" style="display:none;" onchange="handleProfilePhotoChange(this)">
              <button type="button" class="btn btn--primary btn--sm" onclick="document.getElementById('profile-photo-input').click()" style="display:inline-flex; align-items:center; gap: 6px; cursor:pointer;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                <span>Upload New Picture</span>
              </button>

              <button type="button" id="photo-reset-btn" class="btn btn--ghost btn--sm" style="display:none; color:#ef4444;" onclick="resetPhotoSelection()">
                <span>Cancel selection</span>
              </button>
            </div>

            <div id="photo-file-status" style="font-size: 0.78rem; color: var(--text-muted, #888); margin-top: 6px;">
              JPG, PNG, WebP or GIF &bull; Square ratio recommended (Min 400x400px)
            </div>
          </div>
        </div>

        {{-- Public Visibility Pill & Live Link --}}
        <div style="display:flex; flex-direction:column; align-items:flex-end; gap: 8px;">
          @if($member->is_active)
            <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-weight: 600; padding: 6px 14px; border-radius: 20px; font-size: 0.82rem; display:inline-flex; align-items:center; gap:6px;">
              <span class="live-pulse" style="width:8px; height:8px; border-radius:50%; background:#10b981; display:inline-block;"></span>
              Live on Website
            </span>
          @else
            <span class="badge" style="background: rgba(107, 114, 128, 0.15); color: #6b7280; font-weight: 600; padding: 6px 14px; border-radius: 20px; font-size: 0.82rem; display:inline-flex; align-items:center; gap:6px;">
              Hidden from Website
            </span>
          @endif

          @if($member->slug && $member->kind === 'founder')
            <a href="{{ route('founders.show', $member->slug) }}" target="_blank" rel="noopener noreferrer" class="btn btn--ghost btn--sm" style="display:inline-flex; align-items:center; gap:5px; font-size:0.8rem;">
              <span>View Public Dossier</span>
              <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M4.5 1.5H14.5V11.5H13V4.06L2.53 14.53L1.47 13.47L11.94 3H4.5V1.5Z"/></svg>
            </a>
          @elseif($member->is_active)
            <a href="{{ url('/team') }}" target="_blank" rel="noopener noreferrer" class="btn btn--ghost btn--sm" style="display:inline-flex; align-items:center; gap:5px; font-size:0.8rem;">
              <span>View on Team Page</span>
              <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M4.5 1.5H14.5V11.5H13V4.06L2.53 14.53L1.47 13.47L11.94 3H4.5V1.5Z"/></svg>
            </a>
          @endif
        </div>
      </div>
    </div>

    {{-- Section 1: Login & Security Credentials --}}
    <h3 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 1rem; color: var(--color-text-accent, #eb5e28);">1. Login &amp; Security Credentials</h3>
    <div class="form-grid">
      <x-admin.input name="name" label="Full Name" :value="old('name', $user->name)" :required="true"/>
      <x-admin.input name="email" label="Email Address" type="email" :value="old('email', $user->email)" :required="true"/>
      <x-admin.input name="password" label="New Password" type="password" hint="Leave blank to keep your current password."/>
      <x-admin.input name="password_confirmation" label="Confirm New Password" type="password"/>
    </div>

    {{-- Section 2: Public Team & Craft Profile --}}
    <h3 style="font-size: 1.05rem; font-weight: 600; margin: 2rem 0 1rem; color: var(--color-text-accent, #eb5e28);">2. Public Team &amp; Craft Profile</h3>
    <div class="form-grid">
      <x-admin.input name="role" label="Professional Title / Role" :value="old('role', $member->role)" placeholder="e.g. Founder & Chief Technology Officer"/>
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

{{-- Interactive Instant Photo Preview Script --}}
<script>
  function handleProfilePhotoChange(input) {
    if (input.files && input.files[0]) {
      var file = input.files[0];
      var reader = new FileReader();

      reader.onload = function(e) {
        var img = document.getElementById('avatar-preview-img');
        var fallback = document.getElementById('avatar-preview-fallback');
        var status = document.getElementById('photo-file-status');
        var resetBtn = document.getElementById('photo-reset-btn');

        img.src = e.target.result;
        img.style.display = 'block';
        if (fallback) fallback.style.display = 'none';

        var sizeKb = Math.round(file.size / 1024);
        var sizeStr = sizeKb > 1024 ? (sizeKb / 1024).toFixed(1) + ' MB' : sizeKb + ' KB';
        status.innerHTML = '<span style="color:#10b981; font-weight:600;">✓ Ready to save:</span> ' + escapeHtml(file.name) + ' (' + sizeStr + ')';
        if (resetBtn) resetBtn.style.display = 'inline-flex';
      };

      reader.readAsDataURL(file);
    }
  }

  function resetPhotoSelection() {
    var input = document.getElementById('profile-photo-input');
    var resetBtn = document.getElementById('photo-reset-btn');
    var status = document.getElementById('photo-file-status');
    if (input) input.value = '';
    if (resetBtn) resetBtn.style.display = 'none';
    if (status) status.innerHTML = 'JPG, PNG, WebP or GIF &bull; Square ratio recommended (Min 400x400px)';
  }

  function escapeHtml(text) {
    var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
  }
</script>
@endsection
