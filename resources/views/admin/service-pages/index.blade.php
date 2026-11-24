@extends('layouts.admin')
@section('title', 'Service page editions')
@section('content')
<div class="content-header"><h1>Service page editions</h1><p>English and Arabic drafts for review. Nothing is public until you explicitly publish an edition. Confirm that every statement matches the services you offer.</p></div>
<div class="form-grid">
@foreach($pages as $edition)
  <article class="card">
    <p class="eyebrow">{{ $edition->locale === 'ar' ? 'العربية' : 'English' }}</p>
    <h2 dir="{{ $edition->locale === 'ar' ? 'rtl' : 'ltr' }}">{{ $edition->title }}</h2>
    <p>{{ $edition->is_published ? 'Published on this installation' : 'Draft, not public' }}</p>
    <div class="form-actions">
      <a class="btn btn--primary btn--sm" href="{{ route('admin.service-pages.edit', $edition->id) }}" wire:navigate>Edit edition</a>
      <a class="btn btn--ghost btn--sm" href="{{ route('admin.service-pages.preview', $edition->id) }}" target="_blank">Private preview ↗</a>
    </div>
  </article>
@endforeach
</div>
@endsection
