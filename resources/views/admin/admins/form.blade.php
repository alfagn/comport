@extends('layouts.admin')

@section('title', $item ? 'Edit Admin Access' : 'New Admin')
@section('crumb', 'Admins')

@section('content')
<div class="card" style="max-width: 860px;">
  <form method="post" action="{{ $item ? route('admin.admins.update', $item->id) : route('admin.admins.store') }}">
    @csrf
    @if($item) @method('PUT') @endif

    <div class="form-grid">
      @if($item)
        {{-- Edit Mode: Linked Team Member Identity Card (Single Source of Truth) --}}
        @php
          $linkedMember = $item->member ?: ($item->member_id ? \App\Models\Member::find($item->member_id) : \App\Models\Member::where('email', $item->email)->first());
          $mPhoto = $linkedMember?->photo
              ? (str_starts_with((string) $linkedMember->photo, 'uploads/') ? asset('storage/'.$linkedMember->photo) : asset($linkedMember->photo))
              : null;
        @endphp
        <div class="full">
          <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; padding:18px 22px; border-radius:14px; background:var(--surface-2, rgba(255,77,28,0.05)); border:1px solid var(--line, rgba(0,0,0,0.08)); margin-bottom:6px;">
            <div style="display:flex; align-items:center; gap:16px;">
              <div style="width:56px; height:56px; border-radius:50%; overflow:hidden; background:var(--accent, #FF4D1C); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.25rem; flex-shrink:0;">
                @if($mPhoto)
                  <img src="{{ $mPhoto }}" alt="{{ $item->name }}" style="width:100%; height:100%; object-fit:cover; display:block;"/>
                @else
                  {{ strtoupper(substr($item->name, 0, 1)) }}
                @endif
              </div>
              <div>
                <div style="font-weight:700; font-size:1.05rem; color:var(--text);">{{ $linkedMember?->name ?? $item->name }}</div>
                <div style="font-size:0.86rem; color:var(--text-muted);">{{ $linkedMember?->email ?? $item->email }} @if($linkedMember?->role) &bull; {{ $linkedMember->role }} @endif</div>
              </div>
            </div>
            @if($linkedMember)
              <input type="hidden" name="member_id" value="{{ $linkedMember->id }}"/>
              <a href="{{ route('admin.members.edit', $linkedMember->id) }}" class="btn btn--ghost btn--sm">
                Edit Person in Team &rarr;
              </a>
            @endif
          </div>
        </div>
      @else
        {{-- Create Mode: Pick Team Member Email directly from Team --}}
        <div class="full field">
          <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; margin-bottom:6px;">
            <label for="admin-member-select" style="margin:0;">Select Team Member (Email) <span style="color:var(--accent)">*</span></label>
            <a href="{{ route('admin.members.create') }}" style="font-size:0.82rem; font-weight:600; color:var(--accent, #FF4D1C); text-decoration:none;">
              + Add new person to Team first &rarr;
            </a>
          </div>

          @if($availableMembers->isEmpty())
            <div style="padding:18px 20px; border-radius:12px; background:rgba(255, 77, 28, 0.06); border:1px dashed rgba(255, 77, 28, 0.35); color:var(--text);">
              <strong>All current Team members already have Admin accounts.</strong>
              <p style="margin:6px 0 12px; font-size:0.88rem; color:var(--text-muted);">
                Add the employee in <strong>Team &amp; Founders</strong> first (with their email and photo), then return here to assign their password and role.
              </p>
              <a href="{{ route('admin.members.create') }}" class="btn btn--primary btn--sm">+ Add Person in Team &amp; Founders</a>
            </div>
          @else
            @php
              $membersMeta = $availableMembers->mapWithKeys(function ($m) {
                  $photoUrl = $m->photo
                      ? (str_starts_with((string) $m->photo, 'uploads/') ? asset('storage/'.$m->photo) : asset($m->photo))
                      : '';
                  return [$m->id => [
                      'name' => $m->name,
                      'email' => $m->email ?? '',
                      'role' => $m->role ?? 'Team Member',
                      'photo' => $photoUrl,
                      'initial' => mb_strtoupper(mb_substr((string) $m->name, 0, 1)),
                  ]];
              });
            @endphp

            <select name="member_id" id="admin-member-select" required style="width:100%; height:46px; padding:0 14px; border-radius:12px; border:1px solid var(--line-strong, #ccc); background:var(--surface, #fff); color:var(--text, #111); font:inherit; font-size:0.95rem;" onchange="empetixPreviewSelectedMember(this.value)">
              <option value="">— Choose email from Team &amp; Founders —</option>
              @foreach($availableMembers as $m)
                <option value="{{ $m->id }}" @selected((int) old('member_id') === $m->id) @disabled(empty($m->email))>
                  @if($m->email)
                    [{{ $m->member_code ?: 'TEAM' }}] {{ $m->email }} — {{ $m->name }}{{ $m->role ? ' ('.$m->role.')' : '' }}
                  @else
                    [{{ $m->member_code ?: 'TEAM' }}] {{ $m->name }} — [No email set — edit in Team first]
                  @endif
                </option>
              @endforeach
            </select>
            @error('member_id')<div class="error">{{ $message }}</div>@enderror
            <div class="hint">Personal details (name, email, photo, title) come automatically from the selected Team profile.</div>

            {{-- Instant Selected Team Member Preview Card --}}
            <div id="selected-member-preview" style="display:none; margin-top:14px; padding:14px 18px; border-radius:14px; background:var(--surface-2, rgba(255,77,28,0.05)); border:1px solid var(--line, rgba(0,0,0,0.08)); align-items:center; gap:14px;">
              <div style="width:48px; height:48px; border-radius:50%; overflow:hidden; background:var(--accent, #FF4D1C); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.1rem; flex-shrink:0;">
                <img id="smp-photo" src="" alt="" style="width:100%; height:100%; object-fit:cover; display:none;"/>
                <span id="smp-initial"></span>
              </div>
              <div>
                <div id="smp-name" style="font-weight:700; font-size:0.98rem; color:var(--text);"></div>
                <div id="smp-meta" style="font-size:0.84rem; color:var(--text-muted);"></div>
              </div>
            </div>

            <script>
              window.__empetixAvailableMembers = @json($membersMeta);
              window.empetixPreviewSelectedMember = function (id) {
                var box = document.getElementById('selected-member-preview');
                if (!box) return;
                var data = window.__empetixAvailableMembers ? window.__empetixAvailableMembers[id] : null;
                if (!data) {
                  box.style.display = 'none';
                  return;
                }
                var img = document.getElementById('smp-photo');
                var ini = document.getElementById('smp-initial');
                if (data.photo) {
                  img.src = data.photo;
                  img.style.display = 'block';
                  ini.style.display = 'none';
                } else {
                  img.style.display = 'none';
                  ini.textContent = data.initial;
                  ini.style.display = 'block';
                }
                document.getElementById('smp-name').textContent = data.name;
                document.getElementById('smp-meta').textContent = data.email + (data.role ? ' • ' + data.role : '');
                box.style.display = 'flex';
              };
              document.addEventListener('DOMContentLoaded', function () {
                var sel = document.getElementById('admin-member-select');
                if (sel && sel.value) window.empetixPreviewSelectedMember(sel.value);
              });
            </script>
          @endif
        </div>
      @endif

      {{-- Credentials: Password & Confirmation --}}
      <x-admin.input name="password" label="{{ $item ? 'New password (leave blank to keep)' : 'Password' }}" type="password" :required="!$item"/>
      <x-admin.input name="password_confirmation" label="Confirm password" type="password" :required="!$item"/>

      {{-- Role Assignment --}}
      <div class="full field">
        <label>Roles <span style="color:var(--accent)">*</span></label>
        <div class="check-grid">
          @foreach($allRoles as $role)
            <label class="toggle">
              <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                     @checked(in_array($role->name, old('roles', $item?->roles->pluck('name')->all() ?? [])))/>
              <span class="track"></span>
              <span class="toggle-label">{{ $role->name }} ({{ $role->users_count }})</span>
            </label>
          @endforeach
        </div>
        @error('roles')<div class="error">{{ $message }}</div>@enderror
        <div class="hint">Owner has full access to every section. Other roles are limited to the sections they allow.</div>
      </div>
    </div>

    <div class="form-actions">
      <button class="btn btn--primary" type="submit" @disabled(!$item && $availableMembers->isEmpty())>{{ $item ? 'Save changes' : 'Create admin' }}</button>
      <a class="btn btn--ghost" href="{{ route('admin.admins.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection
