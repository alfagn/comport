@extends('layouts.public', ['activePage' => 'work'])

@section('meta')
  <meta name="description" content="{{ sec('work', 'copy_83644a777d3c', 'Explore our production software engineering dossiers, high-concurrency cloud systems, and enterprise digital platforms.') }}"/>
  <title>{{ sec('work', 'copy_77e7fd581bd1', 'Production Software Engineering & Enterprise Platforms — Empetix Solution') }}</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="{{ sec('work', 'copy_77e7fd581bd1', 'Production Software Engineering & Enterprise Platforms — Empetix Solution') }}"/>
  <meta property="og:description" content="{{ sec('work', 'copy_83644a777d3c', 'Explore our production software engineering dossiers, high-concurrency cloud systems, and enterprise digital platforms.') }}"/>
  <meta property="og:url" content="{{ seo_current_url() }}"/>
  <meta property="og:image" content="{{ seo_og_image() }}"/>
  <meta property="og:image:url" content="{{ seo_og_image() }}"/>
  <meta property="og:image:secure_url" content="{{ seo_og_image() }}"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="{{ sec('work', 'copy_77e7fd581bd1', 'Production Software Engineering & Enterprise Platforms — Empetix Solution') }}"/>
  <meta name="twitter:description" content="{{ sec('work', 'copy_83644a777d3c', 'Explore our production software engineering dossiers, high-concurrency cloud systems, and enterprise digital platforms.') }}"/>
  <meta name="twitter:image" content="{{ seo_og_image() }}"/>
@endsection

@section('content')
<main id="main-content">

    <section class="page-hero">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ url('/') }}">Home</a></li>
          <li aria-current="page">Projects</li>
        </ol>
        <h1 class="page-hero__title">{{ sec('work', 'hero_title', 'Production software engineering & high-stakes platforms.') }}</h1>
        <div class="page-hero__row">
          <div class="page-hero__label">
            <span class="eyebrow">{{ sec('work', 'hero_eyebrow', 'Platform Portfolio 2025–2026') }}</span>
          </div>
          <p class="page-hero__lead">
            {{ sec('work', 'hero_lead', 'Every project below represents production software engineering, high-concurrency cloud architecture, and mission-critical systems. Explore the live platforms or click any project dossier to inspect technical architecture, system design, and verified metrics.') }}
          </p>
          <div class="page-hero__aside">
            <span class="t-meta">{{ sec('work', 'hero_meta', $projects->count().' AUDITED PLATFORMS') }}</span>
          </div>
        </div>
      </div>
    </section>

    <section class="section section--flush-top" aria-label="Project archive">
      <div class="container">
        <!-- Interactive Filter Bar (Strictly Software, SaaS, Cloud, EdTech, FinTech) -->
        <div class="filter" data-filter-bar="work-archive" role="toolbar" aria-label="Filter projects by discipline">
          <span class="filter__label">Filter by system:</span>
          <button type="button" class="filter__btn" data-filter="all" aria-pressed="true">All Platforms<sup>{{ $projects->count() }}</sup></button>
          <button type="button" class="filter__btn" data-filter="software" aria-pressed="false">Software Engineering</button>
          <button type="button" class="filter__btn" data-filter="edtech" aria-pressed="false">EdTech &amp; LMS</button>
          <button type="button" class="filter__btn" data-filter="cloud" aria-pressed="false">Cloud &amp; APIs</button>
          <button type="button" class="filter__btn" data-filter="saas" aria-pressed="false">FinTech &amp; Enterprise SaaS</button>
          <span class="filter__count" data-filter-count>{{ $projects->count() }} {{ \Illuminate\Support\Str::plural('entry', $projects->count()) }}</span>
        </div>

        <div id="work-archive" class="work-cards-grid">
          <!-- LOOP: project-card -->
          @foreach($projects as $project)
            @php
              // Calculate category tags for filtering
              $tagSlugs = $project->tags->pluck('slug')->implode(' ');
              $kickerLower = strtolower($project->kicker ?? '');
              $categoryClasses = 'software';
              if (str_contains($kickerLower, 'edtech') || str_contains($tagSlugs, 'edtech') || str_contains($tagSlugs, 'lms') || str_contains($kickerLower, 'learn')) {
                  $categoryClasses .= ' edtech';
              }
              if (str_contains($kickerLower, 'fintech') || str_contains($tagSlugs, 'fintech') || str_contains($tagSlugs, 'saas') || str_contains($kickerLower, 'saas')) {
                  $categoryClasses .= ' saas';
              }
              if (str_contains($kickerLower, 'cloud') || str_contains($tagSlugs, 'cloud') || str_contains($tagSlugs, 'api') || str_contains($kickerLower, 'logistics')) {
                  $categoryClasses .= ' cloud';
              }
            @endphp
            <article class="work-card reveal" data-category="{{ $categoryClasses }}">
              <div class="work-card__frame">
                @if($project->live_url)
                  <div class="work-card__badge-live">
                    <span class="live-pulse"></span>
                    <span>Live Platform</span>
                  </div>
                @endif
                <a href="{{ route('work.show', $project->slug) }}" tabindex="-1" aria-hidden="true">
                  <img alt="{{ $project->title }}" loading="lazy" height="720" width="1280" src="{{ str_starts_with((string) $project->image, 'uploads/') ? asset('storage/'.$project->image) : asset($project->image ?: 'assets/img/work/placeholder.jpg') }}"/>
                </a>
              </div>
              
              <div class="work-card__body">
                <div class="work-card__meta">
                  <span class="work-card__client">{{ $project->kicker ?? $project->client }}</span>
                  <span class="work-card__year">{{ $project->year }}</span>
                </div>

                <h3 class="work-card__title">
                  <a href="{{ route('work.show', $project->slug) }}">{{ $project->title }}</a>
                </h3>

                @if($project->excerpt)
                  <p class="work-card__desc">{{ $project->excerpt }}</p>
                @endif

                @if($project->tags->isNotEmpty())
                  <ul class="tag-list">
                    @foreach($project->tags as $tag)
                      <li class="tag">{{ $tag->name }}</li>
                    @endforeach
                  </ul>
                @endif

                <div class="work-card__foot">
                  @if($project->live_url)
                    <a href="{{ $project->live_url }}" target="_blank" rel="noopener noreferrer" class="work-card__live-link">
                      <span>Visit Live Site</span>
                      <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M4.5 1.5H14.5V11.5H13V4.06L2.53 14.53L1.47 13.47L11.94 3H4.5V1.5Z"/></svg>
                    </a>
                  @else
                    <span style="font-size:0.75rem; color:var(--color-text-muted, #777);">Enterprise Audited</span>
                  @endif

                  <a href="{{ route('work.show', $project->slug) }}" class="work-card__details-link">
                    <span>Case Study</span>
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8.5 2.5L7.44 3.56L11.13 7.25H2V8.75H11.13L7.44 12.44L8.5 13.5L14 8L8.5 2.5Z"/></svg>
                  </a>
                </div>
              </div>
            </article>
          @endforeach
          <!-- END LOOP: project-card -->
        </div>
      </div>
    </section>
</main>
@endsection
