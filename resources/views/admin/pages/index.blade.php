@extends('layouts.admin')

@section('title', 'Pages & Content')
@section('crumb', 'Edit every word and image on the site')

@section('content')
<div class="card">
  <h2 class="card__title">Site pages</h2>
  <p class="card__sub">Pick a page to edit its headings, paragraphs, buttons, stats and images. Entity content (projects, jobs, posts...) is managed from its own section.</p>

  @php
    // Friendly names + icons for the known page groups.
    $meta = [
      'home' => ['Home page', 'Hero, intro, stats strip, section headings and CTAs'],
      'footer' => ['Footer (all pages)', 'CTA banner, company summary, address and newsletter copy'],
      'header' => ['Navigation (all pages)', 'Menu labels and mega-menu copy'],
      'about' => ['About page', 'Story, values and approach copy'],
      'work' => ['Work page', 'Portfolio intro and section headings'],
      'team' => ['Team page', 'Team intro and culture copy'],
      'careers' => ['Careers page', 'Hiring intro, benefits and culture copy'],
      'partners' => ['Partners page', 'Alliances intro copy'],
      'blog' => ['Blog page', 'Journal intro copy'],
      'contact' => ['Contact page', 'Form intro, contact details and sidebar copy'],
    ];
    $order = array_keys($meta);
    $sorted = $pages->sortBy(fn ($v, $k) => array_search($k, $order) ?: 99);
  @endphp

  @foreach($sorted as $page => $sections)
    <a class="page-row" href="{{ route('admin.pages.edit', $page) }}">
      <span class="page-row__icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M5 4h14v16H5z"/><path d="M9 8h6M9 12h6M9 16h4"/></svg>
      </span>
      <span>
        <span class="page-row__name">{{ $meta[$page][0] ?? ucfirst($page) }}</span><br/>
        <span class="page-row__meta">{{ $meta[$page][1] ?? '' }} — {{ count($sections) }} editable blocks</span>
      </span>
      <svg class="arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M9 6l6 6-6 6"/></svg>
    </a>
  @endforeach
</div>
@endsection
