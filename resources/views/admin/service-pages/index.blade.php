@extends('layouts.admin')

@section('title', 'Service Pages')
@section('crumb', 'Dedicated service landing pages (/services)')

@section('actions')
  <a class="btn btn--ghost" href="{{ route('admin.services.index') }}" wire:navigate>← Homepage Services</a>
  <a class="btn btn--primary" href="{{ route('admin.service-pages.create') }}" wire:navigate>+ Add Service Page</a>
@endsection

@section('content')
<div class="card" style="margin-bottom: 22px;">
  <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:16px;">
    <div>
      <h2 class="card__title">Service landing pages &amp; English drafts</h2>
      <p class="card__sub" style="margin-bottom:0; max-width:72ch;">
        Create, edit, preview, or remove standalone service landing pages under <code>/services/{slug}</code>. Drafts stay private until you switch them to Published.
      </p>
    </div>
    <div style="display:flex; flex-wrap:wrap; gap:10px;">
      <a class="btn btn--ghost btn--sm" href="{{ url('/services') }}" target="_blank" rel="noopener">View /services index ↗</a>
      <a class="btn btn--primary btn--sm" href="{{ route('admin.service-pages.create') }}" wire:navigate>+ Add Service Page</a>
    </div>
  </div>
</div>

@if($pages->isEmpty())
  <div class="card" style="text-align:center; padding: 56px 24px;">
    <h3 class="card__title" style="margin-bottom:8px;">No service pages yet</h3>
    <p class="card__sub" style="max-width:46ch; margin: 0 auto 20px;">
      Add your own dedicated service landing pages with structured scope sections and search metadata.
    </p>
    <a class="btn btn--primary" href="{{ route('admin.service-pages.create') }}" wire:navigate>+ Add First Service Page</a>
  </div>
@else
  <div class="sp-cards-grid">
    @foreach($pages as $edition)
      <article class="card sp-card">
        <div>
          <div class="sp-card__meta">
            @include('admin._partials.status-toggle', [
              'entity' => 'service-pages',
              'id' => $edition->id,
              'active' => (bool) $edition->is_published,
              'labelOn' => 'Published',
              'labelOff' => 'Draft',
            ])
            <code class="sp-card__slug">/services/{{ $edition->slug }}</code>
          </div>

          <h2 class="sp-card__title" dir="ltr">{{ $edition->title }}</h2>
          <p class="sp-card__summary" dir="ltr">{{ \Illuminate\Support\Str::limit($edition->summary, 150) }}</p>
        </div>

        <div class="sp-card__foot">
          <div class="sp-card__actions">
            <a class="btn btn--primary btn--sm" href="{{ route('admin.service-pages.edit', $edition->id) }}" wire:navigate>Edit</a>
            <a class="btn btn--ghost btn--sm" href="{{ route('admin.service-pages.preview', $edition->id) }}" target="_blank" rel="noopener">Preview ↗</a>
            @if($edition->is_published)
              <a class="btn btn--ghost btn--sm" href="{{ url($edition->publicPath()) }}" target="_blank" rel="noopener">Live ↗</a>
            @endif
          </div>

          <form method="post"
                action="{{ route('admin.service-pages.destroy', $edition->id) }}"
                data-confirm-title="Delete Service Page"
                data-confirm="Delete &ldquo;{{ $edition->title }}&rdquo;? This removes /services/{{ $edition->slug }} permanently.">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn--danger btn--sm">Delete</button>
          </form>
        </div>
      </article>
    @endforeach
  </div>
@endif
@endsection
