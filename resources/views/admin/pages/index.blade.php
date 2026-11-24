@extends('layouts.admin')

@section('title', 'Pages & Content')
@section('crumb', 'Curated editorial blocks across the public website')

@section('content')
<div class="card">
  <h2 class="card__title">Site pages &amp; shared sections</h2>
  <p class="card__sub">Pick a page to edit its headings, lead copy, media, metrics, and calls to action. Dynamic records (projects, jobs, posts, team members, partners) are managed from their dedicated menu sections.</p>

  @php
    $meta = [
      'home' => ['Home page', 'Hero CTA, capabilities ticker, overview video, impact metrics & section headings'],
      'header' => ['Header & Mega-Menu (all pages)', 'Capabilities and Journal mega-menu intros & global search placeholder'],
      'footer' => ['Footer (all pages)', 'Executive CTA banner, studio summary, newsletter dispatch & legal copyright'],
      'about' => ['About page', '2025 story, Private Storage overview video, Operating Principles cards & Timeline cards'],
      'founders' => ['Founders strip (shared)', 'Co-founders introduction shared across Home, About, and Team'],
      'work' => ['Work & Case Studies page', 'Portfolio hero headings, eyebrow badge, and summary copy'],
      'team' => ['Team page', 'Team hero, practice descriptions, studio culture & join section'],
      'careers' => ['Careers page', 'Talent portal hero, turnaround metrics, section headings & consent copy'],
      'partners' => ['Partners page', 'Ecosystem hero, three partnership models & executive endorsement'],
      'blog' => ['Blog & Journal page', 'Journal hero and featured video episode spotlight'],
      'contact' => ['Contact page', 'Executive desks, brief budget tiers, headquarters showcase & FAQ heading'],
    ];
    $order = array_keys($meta);
    $sorted = $pages->sortBy(fn ($v, $k) => ($pos = array_search($k, $order, true)) !== false ? $pos : 99);
  @endphp

  @foreach($sorted as $page => $sections)
    <a class="page-row" href="{{ route('admin.pages.edit', $page) }}">
      <span class="page-row__icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M5 4h14v16H5z"/><path d="M9 8h6M9 12h6M9 16h4"/></svg>
      </span>
      <span>
        <span class="page-row__name">{{ $meta[$page][0] ?? ucfirst($page) }}</span><br/>
        <span class="page-row__meta">{{ $meta[$page][1] ?? 'Page editorial blocks' }} — {{ count($sections) }} editable {{ \Illuminate\Support\Str::plural('field', count($sections)) }}</span>
      </span>
      <svg class="arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M9 6l6 6-6 6"/></svg>
    </a>
  @endforeach
</div>
@endsection
