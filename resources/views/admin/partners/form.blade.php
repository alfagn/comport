@extends('layouts.admin')

@section('title', $item ? 'Edit Partner' : 'New Partner')
@section('crumb', 'Partners')

@section('content')
<div class="card">
  <form method="post" enctype="multipart/form-data" action="{{ $item ? route('admin.partners.update', $item->id) : route('admin.partners.store') }}">
    @csrf
    @if($item) @method('PUT') @endif
    <div class="form-grid">
      <x-admin.input name="name" label="Name" :value="$item?->name" :required="true"/>
      <x-admin.input name="tier" label="Tier" :value="$item?->tier" placeholder="Technology Alliance"/>
      <x-admin.input name="url" label="Website" type="url" :value="$item?->url"/>
      <x-admin.input name="sort_order" label="Sort order" type="number" :value="$item?->sort_order ?? 0"/>
      <div class="full"><x-admin.image name="logo" label="Logo" :current="$item?->logo" hint="SVG or PNG on transparent background looks best."/></div>
      <x-admin.toggle name="is_active" label="Live on the site" :checked="$item?->is_active ?? true"/>
    </div>
    <div class="form-actions">
      <button class="btn btn--primary" type="submit">{{ $item ? 'Save changes' : 'Create partner' }}</button>
      <a class="btn btn--ghost" href="{{ route('admin.partners.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection
