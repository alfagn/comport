@extends('layouts.admin')

@section('title', $item ? 'Edit Link' : 'New Link')
@section('crumb', 'Navigation')

@section('content')
<div class="card">
  <form method="post" action="{{ $item ? route('admin.navigation.update', $item->id) : route('admin.navigation.store') }}">
    @csrf
    @if($item) @method('PUT') @endif
    <div class="form-grid">
      <x-admin.input name="label" label="Label" :value="$item?->label" :required="true"/>
      <x-admin.input name="url" label="URL" :value="$item?->url" placeholder="/work or https://..." :required="true"/>
      <div class="field">
        <label for="menu">Menu</label>
        <select class="select" id="menu" name="menu">
          <option value="utility" @selected(old('menu', $item?->menu) === 'utility')>Utility bar (top of every page)</option>
          <option value="drawer" @selected(old('menu', $item?->menu) === 'drawer')>Mobile drawer</option>
        </select>
        @error('menu')<p class="error">{{ $message }}</p>@enderror
      </div>
      <x-admin.input name="sort_order" label="Sort order" type="number" :value="$item?->sort_order ?? 0"/>
      <x-admin.toggle name="is_active" label="Visible on the site" :checked="$item?->is_active ?? true"/>
    </div>
    <div class="form-actions">
      <button class="btn btn--primary" type="submit">{{ $item ? 'Save changes' : 'Create link' }}</button>
      <a class="btn btn--ghost" href="{{ route('admin.navigation.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection
