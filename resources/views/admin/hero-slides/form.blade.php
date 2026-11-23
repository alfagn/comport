@extends('layouts.admin')

@section('title', $item ? 'Edit Slide' : 'New Slide')
@section('crumb', 'Hero Slides')

@section('content')
<div class="card">
  <form method="post" enctype="multipart/form-data"
        action="{{ $item ? route('admin.hero-slides.update', $item->id) : route('admin.hero-slides.store') }}">
    @csrf
    @if($item) @method('PUT') @endif

    <div class="form-grid">
      <div class="full"><x-admin.input name="title" label="Headline" :value="$item?->title" :required="true"/></div>
      <x-admin.input name="tag" label="Eyebrow tag" :value="$item?->tag" placeholder="SOFTWARE & GROWTH PARTNER"/>
      <x-admin.input name="sort_order" label="Sort order" type="number" :value="$item?->sort_order ?? 0"/>
      <div class="full"><x-admin.textarea name="description" label="Description" :value="$item?->description" rows="3"/></div>
      <x-admin.input name="cta_label" label="Button label" :value="$item?->cta_label" placeholder="Start a Project"/>
      <x-admin.input name="cta_url" label="Button link" :value="$item?->cta_url" placeholder="/contact"/>
      <div class="full"><x-admin.textarea name="meta" label="Stat chips (one per line)" :value="$item ? implode(PHP_EOL, (array) $item->meta) : ''" rows="3" hint="Small facts rendered beside the slide, e.g. 99.96% uptime."/></div>
      <div class="full"><x-admin.image name="image" label="Background image" :current="$item?->image"/></div>
      <x-admin.toggle name="is_active" label="Live on the site" :checked="$item?->is_active ?? true"/>
    </div>

    <div class="form-actions">
      <button class="btn btn--primary" type="submit">{{ $item ? 'Save changes' : 'Create slide' }}</button>
      <a class="btn btn--ghost" href="{{ route('admin.hero-slides.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection
