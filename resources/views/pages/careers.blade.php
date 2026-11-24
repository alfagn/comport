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
  <meta property="og:image:alt" content="Empetix Solution"/>
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
              <span class="jobs-hero__stat-num">{{ sec('careers', 'metric_2') }}<span>h</span></span>
              <span class="jobs-hero__stat-label">{{ sec('careers', 'copy_a05c8e7260e3') }}</span>
            </div>
            <div class="jobs-hero__stat">
              <span class="jobs-hero__stat-num">{{ sec('careers', 'metric_3') }}<span>d</span></span>
              <span class="jobs-hero__stat-label">{{ sec('careers', 'copy_3cf747b69596') }}</span>
            </div>
          </div>
        </div>

        <!-- Live Search & Multi-Filter Console -->
        <div class="jobs-console" role="search" aria-label="Filter open jobs">
          <div class="jobs-console__grid">
            <div class="jobs-console__field">
              <label for="jobs-search-input" class="jobs-console__label">01 / Search Keyword or Skill</label>
              <input id="jobs-search-input" type="search" class="jobs-console__input" placeholder="e.g. Laravel, Flutter, Next.js, Cloud DevOps, Product Designer..."/>
            </div>

            <div class="jobs-console__field">
              <label for="jobs-dept-select" class="jobs-console__label">02 / Department</label>
              <select id="jobs-dept-select" class="jobs-console__select">
                <option value="all">All Departments ({{ $jobs->count() }})</option>
                <option value="engineering">Software Engineering</option>
                <option value="cloud">Cloud &amp; DevOps</option>
                <option value="design">Product Design</option>
              </select>
            </div>

            <div class="jobs-console__field">
              <label for="jobs-loc-select" class="jobs-console__label">03 / Studio Location</label>
              <select id="jobs-loc-select" class="jobs-console__select">
                <option value="all">All Locations</option>
                <option value="cairo">Cairo, Egypt</option>
                <option value="riyadh">Riyadh, KSA</option>
                <option value="remote">Remote (EMEA)</option>
              </select>
            </div>

            <div class="jobs-console__field">
              <label for="jobs-mode-select" class="jobs-console__label">04 / Work Model</label>
              <select id="jobs-mode-select" class="jobs-console__select">
                <option value="all">All Models</option>
                <option value="hybrid">Hybrid</option>
                <option value="remote">Remote-Friendly</option>
                <option value="onsite">On-Site Studio</option>
              </select>
            </div>

            <button type="button" id="jobs-reset-btn" class="btn btn--ghost btn--sm">Reset</button>
          </div>
        </div>
      </div>
    </section>

    <!-- 2. CAREER TRACKS / DEPARTMENTS -->
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

    <!-- 3. OPEN POSITIONS DIRECTORY -->
    <section id="open-roles" class="section section--alt section--ruled" aria-labelledby="roles-heading">
      <div class="container">
        <div class="jobs-toolbar">
          <div>
            <span class="eyebrow">02 / Open Requisitions</span>
            <h2 id="roles-heading" class="t-h3">{{ sec('careers', 'roles_heading', 'Select a role to view full qualifications, requirements & apply') }}</h2>
          </div>
          <p id="jobs-results-count" class="jobs-toolbar__count" aria-live="polite">
            Showing {{ $jobs->count() }} of {{ $jobs->count() }} open positions
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
                <span>View Job &amp; Apply</span>
                <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                  <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </a>
            </div>
          </article>
@endforeach
        </div>

        <div id="jobs-empty-state" class="empty-state" hidden>
          <p class="t-h4">No positions match your current filter criteria.</p>
          <p class="t-small">Try clearing your search keyword or switching the location/department filter to &ldquo;All&rdquo;.</p>
        </div>
      </div>
    </section>

    <!-- 4. STUDIO CULTURE WINDOW -->
    <section class="bleed">
      <div class="container">
        <div class="frame bleed__frame bleed__frame--culture">
          <img src="{{ content_asset(sec('careers', 'copy_b221f03ea2bf')) }}" alt="Empetix engineers and designers collaborating during a design sprint workshop" width="1600" height="900"/>
        </div>
        <div class="bleed__caption">
          <span>ARCHITECTURE &amp; GROWTH CRITIQUE &middot; EMPETIX CAIRO</span>
          <span>PROTECTED DEEP-WORK BLOCKS &middot; ZERO AGENCY THEATRE</span>
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
</main>
@endsection
