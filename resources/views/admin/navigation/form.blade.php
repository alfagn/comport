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
          <option value="mega-capabilities" @selected(old('menu', $item?->menu) === 'mega-capabilities')>Mega menu — Capabilities</option>
          <option value="mega-journal" @selected(old('menu', $item?->menu) === 'mega-journal')>Mega menu — Journal</option>
        </select>
        @error('menu')<p class="error">{{ $message }}</p>@enderror
      </div>
      <div class="field">
        <label for="parent_id">Mega-menu column (only for mega menus)</label>
        <select class="select" id="parent_id" name="parent_id">
          <option value="">— This row is a column heading —</option>
          @foreach($columns as $column)
            <option value="{{ $column->id }}" @selected((string) old('parent_id', $item?->parent_id) === (string) $column->id)>{{ $column->menu === 'mega-capabilities' ? 'Capabilities' : 'Journal' }} · {{ $column->label }}</option>
          @endforeach
        </select>
        <p class="hint">Leave empty to create a column; pick a column to add a link inside it.</p>
        @error('parent_id')<p class="error">{{ $message }}</p>@enderror
      </div>
      <x-admin.input name="sort_order" label="Sort order" type="number" :value="$item?->sort_order ?? 0"/>
      <x-admin.input name="sub" label="Sub-line (mega link description)" :value="$item?->sub"/>
      <x-admin.input name="meta" label="Meta line (feature card kicker)" :value="$item?->meta" placeholder="NILEPAY FINTECH · +310% ACTIVATION"/>
      <x-admin.input name="image" label="Image path (feature card)" :value="$item?->image" placeholder="assets/img/work/nilepay.jpg"/>
      <x-admin.toggle name="is_active" label="Visible on the site" :checked="$item?->is_active ?? true"/>
    </div>
    <div class="form-actions">
      <button class="btn btn--primary" type="submit">{{ $item ? 'Save changes' : 'Create link' }}</button>
      <a class="btn btn--ghost" href="{{ route('admin.navigation.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection
