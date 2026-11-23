@extends('layouts.admin')

@section('title', $item ? 'Edit Service' : 'New Service')
@section('crumb', 'Services')

@section('content')
<div class="card">
  <form method="post" enctype="multipart/form-data"
        action="{{ $item ? route('admin.services.update', $item->id) : route('admin.services.store') }}">
    @csrf
    @if($item) @method('PUT') @endif

    <div class="form-grid">
      <x-admin.input name="num" label="Number" :value="$item?->num" placeholder="01"/>
      <x-admin.input name="title" label="Tab title" :value="$item?->title" :required="true"/>
      <x-admin.input name="slug" label="Slug" :value="$item?->slug" hint="Leave empty to generate from the title."/>
      <x-admin.input name="tag" label="Tag" :value="$item?->tag" placeholder="Engineering"/>
      <x-admin.input name="kicker" label="Pillar label" :value="$item?->kicker" placeholder="PILLAR SOFTWARE ARCHITECTURE"/>
      <div class="full"><x-admin.input name="heading" label="Panel heading" :value="$item?->heading"/></div>
      <div class="full"><x-admin.textarea name="description" label="Panel description" :value="$item?->description" rows="3"/></div>
      <div class="full"><x-admin.textarea name="deliverables" label="Deliverables (one per line)" :value="$item ? implode(PHP_EOL, (array) $item->deliverables) : ''" rows="4"/></div>
      <x-admin.input name="metric_num" label="Proof metric" :value="$item?->metric_num" placeholder="99.96%"/>
      <x-admin.input name="metric_label" label="Metric label" :value="$item?->metric_label"/>
      <x-admin.input name="cta_label" label="Button label" :value="$item?->cta_label"/>
      <x-admin.input name="cta_url" label="Button link" :value="$item?->cta_url"/>
      <div class="full"><x-admin.image name="image" label="Panel image" :current="$item?->image"/></div>
      <x-admin.input name="sort_order" label="Sort order" type="number" :value="$item?->sort_order ?? 0"/>
      <x-admin.toggle name="is_active" label="Live on the site" :checked="$item?->is_active ?? true"/>
    </div>

    <div class="form-actions">
      <button class="btn btn--primary" type="submit">{{ $item ? 'Save changes' : 'Create service' }}</button>
      <a class="btn btn--ghost" href="{{ route('admin.services.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection
