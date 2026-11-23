@extends('layouts.admin')

@section('title', $item ? 'Edit Role' : 'New Role')
@section('crumb', 'Roles')

@section('content')
<div class="card">
  <form method="post" action="{{ $item ? route('admin.roles.update', $item->id) : route('admin.roles.store') }}">
    @csrf
    @if($item) @method('PUT') @endif
    <div class="form-grid">
      <div class="full"><x-admin.input name="name" label="Role name" :value="$item?->name" :required="true" placeholder="Content editor"/></div>
      <div class="full field">
        <label>Permissions <span style="color:var(--accent)">*</span></label>
        <div class="check-grid">
          @foreach($catalog as $key => $label)
            <label class="toggle">
              <input type="checkbox" name="permissions[]" value="{{ $key }}"
                     @checked(in_array($key, old('permissions', $item?->permissions->pluck('name')->all() ?? [])))/>
              <span class="track"></span>
              <span class="toggle-label">{{ $label }}</span>
            </label>
          @endforeach
        </div>
        @error('permissions')<div class="error">{{ $message }}</div>@enderror
        <div class="hint">Admins with this role can only reach the checked sections; everything else is blocked by the server.</div>
      </div>
    </div>
    <div class="form-actions">
      <button class="btn btn--primary" type="submit">{{ $item ? 'Save changes' : 'Create role' }}</button>
      <a class="btn btn--ghost" href="{{ route('admin.roles.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection
