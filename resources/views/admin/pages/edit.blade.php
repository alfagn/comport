@extends('layouts.admin')

@section('title', 'Edit: '.ucfirst($page))
@section('crumb', 'Pages & Content')

@section('content')
@php
  $editableSections = $sections->where('type', '!=', 'internal')->values();
  $publicRoutes = [
      'home' => '/',
      'about' => '/about',
      'work' => '/work',
      'team' => '/team',
      'careers' => '/careers',
      'partners' => '/partners',
      'blog' => '/blog',
      'contact' => '/contact',
  ];
  $liveUrl = isset($publicRoutes[$page]) ? url($publicRoutes[$page]) : null;
@endphp

<div class="card card--form">
  <div class="form-card__head">
    <div class="form-card__intro">
      <span class="form-card__icon" aria-hidden="true">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="18" height="18" rx="3"/>
          <path d="M3 9h18M9 21V9"/>
        </svg>
      </span>
      <div>
        <div class="form-card__title-row">
          <h2 class="card__title">{{ ucfirst($page) }} content blocks</h2>
          <span class="form-card__count">{{ $editableSections->count() }} {{ Str::plural('field', $editableSections->count()) }}</span>
        </div>
        <p class="card__sub">Changes go live on the public site as soon as you save.</p>
      </div>
    </div>

    @if($liveUrl)
      <a class="btn btn--ghost btn--sm" href="{{ $liveUrl }}" target="_blank" rel="noopener">
        <span>View live page</span>
        <span aria-hidden="true">↗</span>
      </a>
    @endif
  </div>

  <form method="post" enctype="multipart/form-data" action="{{ route('admin.pages.update', $page) }}">
    @csrf @method('PUT')

    <div class="form-grid form-grid--editorial">
      @foreach($editableSections as $section)
        @php
          $isFullWidth = in_array($section->type, ['textarea', 'image'], true)
              || mb_strlen((string) $section->value) > 85;
        @endphp

        @if($section->type === 'textarea')
          <div class="full">
            <x-admin.textarea :name="'sections['.$section->key.']'" :label="$section->label" :value="$section->value" rows="4"/>
          </div>
        @elseif($section->type === 'image')
          <div class="full">
            <x-admin.image :name="'images['.$section->key.']'" :label="$section->label" :current="$section->value"/>
          </div>
        @elseif($section->type === 'number')
          <div class="{{ $isFullWidth ? 'full' : '' }}">
            <x-admin.input :name="'sections['.$section->key.']'" :label="$section->label" type="number" step="any" :value="$section->value"/>
          </div>
        @else
          <div class="{{ $isFullWidth ? 'full' : '' }}">
            <x-admin.input :name="'sections['.$section->key.']'" :label="$section->label" :value="$section->value"/>
          </div>
        @endif
      @endforeach
    </div>

    <div class="form-actions">
      <button class="btn btn--primary" type="submit">Save {{ $page }} content</button>
      <a class="btn btn--ghost" href="{{ route('admin.pages.index') }}">Back to pages</a>
    </div>
  </form>
</div>
@endsection
