@extends('layouts.public', ['activePage' => 'careers'])

@section('meta')
  <meta name="description" content="{{ sec('careers', 'copy_71c7ef938d08') }}"/>
  <title>{{ sec('careers', 'copy_c8f958fda5c5') }}</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="{{ sec('careers', 'copy_c8f958fda5c5') }}"/>
  <meta property="og:description" content="{{ sec('careers', 'copy_71c7ef938d08') }}"/>
  <meta property="og:url" content="{{ seo_current_url() }}"/>
  <meta property="og:image" content="{{ seo_og_image() }}"/>
  <meta property="og:image:url" content="{{ seo_og_image() }}"/>
  <meta property="og:image:secure_url" content="{{ seo_og_image() }}"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="{{ sec('careers', 'copy_46054f40eb9f') }}"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="{{ sec('careers', 'copy_c8f958fda5c5') }}"/>
  <meta name="twitter:description" content="{{ sec('careers', 'copy_71c7ef938d08') }}"/>
  <meta name="twitter:image" content="{{ seo_og_image() }}"/>
@endsection

@section('content')
<main id="main-content">

    <!-- 1. CAREERS PORTAL HERO + LIVE SEARCH & FILTER CONSOLE (jobs.empetix.com) -->
    <section class="jobs-hero" aria-labelledby="jobs-hero-title">
      <div class="jobs-hero__bg" aria-hidden="true"></div>
      <div class="container">
        <div class="jobs-hero__grid">
          <div class="jobs-hero__main">
            <span class="eyebrow eyebrow--inverse">{{ sec('careers', 'copy_21b4847b84f7') }}</span>
            <h1 id="jobs-hero-title" class="jobs-hero__title">
              {{ sec('careers', 'copy_017ca46e789e') }}
            </h1>
            <p class="jobs-hero__lead">
              {{ sec('careers', 'copy_7f822db55925') }}
            </p>
          </div>

          <div class="jobs-hero__stats">
            <div class="jobs-hero__stat">
              <span class="jobs-hero__stat-num">{{ sec('careers', 'metric_1') }}</span>
              <span class="jobs-hero__stat-label">{{ sec('careers', 'copy_aae7c571ba3b') }}</span>
            </div>
            <div class="jobs-hero__stat">
              <span class="jobs-hero__stat-num">{{ sec('careers', 'metric_2') }}<span>{{ sec('careers', 'copy_ec369163dd99') }}</span></span>
              <span class="jobs-hero__stat-label">{{ sec('careers', 'copy_a05c8e7260e3') }}</span>
            </div>
            <div class="jobs-hero__stat">
              <span class="jobs-hero__stat-num">{{ sec('careers', 'metric_3') }}<span>{{ sec('careers', 'copy_23c33f363943') }}</span></span>
              <span class="jobs-hero__stat-label">{{ sec('careers', 'copy_3cf747b69596') }}</span>
            </div>
          </div>
        </div>

        <!-- Live Search & Multi-Filter Console -->
        <div class="jobs-console" role="search" aria-label="Filter open jobs on jobs.empetix.com">
          <div class="jobs-console__grid">
            <div class="jobs-console__field">
              <label for="jobs-search-input" class="jobs-console__label">{{ sec('careers', 'copy_e344c8a8924e') }}</label>
              <input id="jobs-search-input" type="search" class="jobs-console__input" placeholder="{{ sec('careers', 'search_placeholder') }}"/>
            </div>

            <div class="jobs-console__field">
              <label for="jobs-dept-select" class="jobs-console__label">{{ sec('careers', 'copy_2e6bccd8b351') }}</label>
              <select id="jobs-dept-select" class="jobs-console__select">
                <option value="all">{{ sec('careers', 'copy_6f97cfde6f1e') }}</option>
                <option value="engineering">{{ sec('careers', 'copy_297c1c46e1e6') }}</option>
                <option value="marketing">{{ sec('careers', 'copy_3f610d88b025') }}</option>
                <option value="design">{{ sec('careers', 'copy_eb7736ae753c') }}</option>
              </select>
            </div>

            <div class="jobs-console__field">
              <label for="jobs-loc-select" class="jobs-console__label">{{ sec('careers', 'copy_7c370205063d') }}</label>
              <select id="jobs-loc-select" class="jobs-console__select">
                <option value="all">{{ sec('careers', 'copy_3113dd8b6cd1') }}</option>
                <option value="cairo">{{ sec('careers', 'copy_ad4a3e32c2fa') }}</option>
                <option value="riyadh">{{ sec('careers', 'copy_e3633e1efbe7') }}</option>
                <option value="remote">{{ sec('careers', 'copy_d36fc93c808d') }}</option>
              </select>
            </div>

            <div class="jobs-console__field">
              <label for="jobs-mode-select" class="jobs-console__label">{{ sec('careers', 'copy_df1859440bf5') }}</label>
              <select id="jobs-mode-select" class="jobs-console__select">
                <option value="all">{{ sec('careers', 'copy_092c8f13af8c') }}</option>
                <option value="hybrid">{{ sec('careers', 'copy_4cc7b52166b5') }}</option>
                <option value="remote">{{ sec('careers', 'copy_c43752b33085') }}</option>
                <option value="onsite">{{ sec('careers', 'copy_90eeb7bbaf38') }}</option>
              </select>
            </div>

            <button type="button" id="jobs-reset-btn" class="btn btn--ghost btn--sm">{{ sec('careers', 'copy_ba15f01f177e') }}</button>
          </div>
        </div>
      </div>
    </section>

    <!-- 2. CAREER TRACKS / DEPARTMENTS (Like Vodafone Careers Tracks) -->
    <section id="tracks" class="section section--sm" aria-labelledby="tracks-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="tracks-heading" class="t-h2">{{ sec('careers', 'tracks_heading', 'Choose your practice area.') }}</h2>
            <p class="t-lead">{{ sec('careers', 'copy_5477c5140849') }}</p>
          </div>
        </div>

        <div class="tracks-grid">
          @foreach(content_list('careers.tracks') as $track)
            <button type="button" class="track-card reveal" data-track-filter="{{ $track->url }}">
              <div>
                <div class="track-card__top">
                  <span class="track-card__code">{{ $track->meta }}</span>
                  <span class="track-card__count">{{ $track->badge }}</span>
                </div>
                <h3 class="track-card__title">{{ $track->title }}</h3>
                <p class="track-card__desc">{{ $track->body }}</p>
              </div>
              <span class="track-card__cta">{{ $track->cta }} &rarr;</span>
            </button>
          @endforeach
        </div>
      </div>
    </section>

    <!-- 3. OPEN POSITIONS DIRECTORY (Each card opens its own dedicated Job Detail Page) -->
    <section id="open-roles" class="section section--alt section--ruled" aria-labelledby="roles-heading">
      <div class="container">
        <div class="jobs-toolbar">
          <div>
            <span class="eyebrow">{{ sec('careers', 'copy_53391263da4e') }}</span>
            <h2 id="roles-heading" class="t-h3">{{ sec('careers', 'roles_heading', 'Select a role to view full qualifications, requirements & apply') }}</h2>
          </div>
          <p id="jobs-results-count" class="jobs-toolbar__count" aria-live="polite">
            {{ sec('careers', 'copy_c584a43addb9') }}
          </p>
        </div>

        <div class="jobs-grid">
          @foreach($jobs as $job)
          <article class="job-card" data-job-card data-job-dept="{{ \Illuminate\Support\Str::slug($job->department) }}" data-job-loc="{{ \Illuminate\Support\Str::slug($job->location) }}" data-job-mode="{{ \Illuminate\Support\Str::slug($job->type) }}">
            <div>
              <div class="job-card__meta-top">
                <span class="job-card__dept">{{ $job->department }}</span>
                <span class="job-card__new">{{ $job->created_at->diffForHumans() }}</span>
              </div>

              <h3 class="job-card__title">
                <a href="{{ route('jobs.show', $job->slug) }}">{{ $job->title }}</a>
              </h3>
              <p class="job-card__summary">{{ $job->summary }}</p>

              <ul class="tag-list">
                @foreach((array) $job->tags as $tag)<li class="tag">{{ $tag }}</li>@endforeach
              </ul>

              <div class="job-card__pills">
                @foreach((array) $job->pills as $pill)
                  @php($parts = explode(':', $pill, 2))
                  <span class="job-card__pill">@if(count($parts) === 2)<strong>{{ $parts[0] }}:</strong>{{ $parts[1] }}@else{{ $pill }}@endif</span>
                @endforeach
              </div>
            </div>

            <div class="job-card__actions">
              <a href="{{ route('jobs.show', $job->slug) }}" class="btn btn--primary">
                <span>{{ sec('careers', 'copy_1a79fca0aeea') }}</span>
                <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                  <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </a>
            </div>
          </article>
@endforeach
        </div>

        <div id="jobs-empty-state" class="empty-state" hidden>
          <p class="t-h4">{{ sec('careers', 'copy_c93b63381e0c') }}</p>
          <p class="t-small">{{ sec('careers', 'copy_e0c019ea05b9') }}</p>
        </div>
      </div>
    </section>

    <!-- 4. FIXED-BACKGROUND STUDIO CULTURE WINDOW -->
    <section class="bleed">
      <div class="container">
        <div class="frame bleed__frame bleed__frame--culture">
          <img src="{{ content_asset(sec('careers', 'copy_b221f03ea2bf')) }}" alt="{{ sec('careers', 'copy_fb6b450b1b17') }}" width="1600" height="900"/>
        </div>
        <div class="bleed__caption">
          <span>{{ sec('careers', 'copy_b896d4468f68') }}</span>
          <span>{{ sec('careers', 'copy_015dac6443b3') }}</span>
        </div>
      </div>
    </section>

    <!-- 5. BENEFITS & COMPENSATION -->
    <section id="benefits" class="section" aria-labelledby="benefits-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="benefits-heading" class="t-h2">{{ sec('careers', 'benefits_heading', 'Built for people who take their craft seriously.') }}</h2>
          </div>
        </div>

        <div class="principles">
          @foreach(content_list('careers.benefits') as $benefit)
            <div class="principle reveal">
              <span class="principle__num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
              <h3 class="principle__title">{{ $benefit->title }}</h3>
              <p class="principle__text">{{ $benefit->body }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    <!-- 6. 4-STEP HIRING PROCESS -->
    <section id="process" class="section section--alt section--ruled" aria-labelledby="process-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="process-heading" class="t-h2">{{ sec('careers', 'process_heading', 'Four transparent steps from application to written offer.') }}</h2>
          </div>
        </div>

        <div class="steps">
          @foreach(content_list('careers.process') as $step)
            <div class="step reveal">
              <span class="step__num">{{ $step->meta }}</span>
              <h3 class="step__title">{{ $step->title }}</h3>
              <p class="step__text">{{ $step->body }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </section>
@endsection
