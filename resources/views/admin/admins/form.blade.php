@extends('layouts.admin')

@section('title', $item ? 'Edit Admin' : 'New Admin')
@section('crumb', 'Admins')

@section('content')
<div class="card">
  <form method="post" action="{{ $item ? route('admin.admins.update', $item->id) : route('admin.admins.store') }}">
    @csrf
    @if($item) @method('PUT') @endif
    <div class="form-grid">
      <x-admin.input name="name" label="Name" :value="$item?->name" :required="true"/>
      <x-admin.input name="email" label="Email" type="email" :value="$item?->email" :required="true"/>
      <x-admin.input name="password" label="{{ $item ? 'New password (leave blank to keep)' : 'Password' }}" type="password" :required="!$item"/>
      <x-admin.input name="password_confirmation" label="Confirm password" type="password" :required="!$item"/>
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
      <button class="btn btn--primary" type="submit">{{ $item ? 'Save changes' : 'Create admin' }}</button>
      <a class="btn btn--ghost" href="{{ route('admin.admins.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection
