@extends('layouts.admin')

@section('title', $item ? 'Edit Industry' : 'New Industry')
@section('crumb', 'Industries')

@section('content')
<div class="card">
  <form method="post" action="{{ $item ? route('admin.industries.update', $item->id) : route('admin.industries.store') }}">
    @csrf
    @if($item) @method('PUT') @endif
    <div class="form-grid">
      <x-admin.input name="num" label="Number" :value="$item?->num" placeholder="SECTOR 01"/>
      <x-admin.input name="title" label="Title" :value="$item?->title" :required="true" placeholder="FinTech & Digital Banking"/>
      <x-admin.input name="subtitle" label="Subtitle" :value="$item?->subtitle" placeholder="Wallets, KYC & Ledgers"/>
      <x-admin.input name="sort_order" label="Sort order" type="number" :value="$item?->sort_order ?? 0"/>
      <x-admin.toggle name="is_active" label="Live on the site" :checked="$item?->is_active ?? true"/>
    </div>
    <div class="form-actions">
      <button class="btn btn--primary" type="submit">{{ $item ? 'Save changes' : 'Create industry' }}</button>
      <a class="btn btn--ghost" href="{{ route('admin.industries.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection
