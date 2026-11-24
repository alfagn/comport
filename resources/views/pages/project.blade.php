@extends('layouts.public', ['activePage' => 'work'])

@section('meta')
  <meta name="description" content="{{ $project->excerpt }}"/>
  <title>{{ $project->title }} — Empetix Solution Technical Dossier</title>
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="article"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="{{ $project->title }} — Empetix Solution Technical Dossier"/>
  <meta property="og:description" content="{{ $project->excerpt }}"/>
  <meta property="og:url" content="{{ seo_current_url() }}"/>
  <meta property="og:image" content="{{ seo_og_image() }}"/>
@endsection

@section('content')
<main id="main-content">

    @php
      $projectLogo = $project->logo
          ? (str_starts_with((string) $project->logo, 'uploads/') ? asset('storage/'.$project->logo) : asset($project->logo))
          : null;

      if (! $projectLogo) {
          if (str_contains((string) $project->slug, 'western') || str_contains((string) $project->slug, 'westvale')) {
              $projectLogo = asset('assets/img/work/westvale/logo.png');
          } elseif (str_contains((string) $project->slug, 'alfagn')) {
              $projectLogo = asset('assets/img/work/alfagn.png');
          } else {
              $projectLogo = brand_logo();
          }
      }
    @endphp

    <section class="article-head article-head--project">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ url('/') }}">Home</a></li>
          <li><a href="{{ url('/work') }}">Projects</a></li>
          <li aria-current="page">{{ $project->client ?? $project->title }}</li>
        </ol>

        <div class="project-hero-split">
          <!-- Left Column: Title, Summary & Quick Action Nav -->
          <div class="project-hero-split__content">
            <div class="article-head__meta">
              @if($project->kicker)<span class="article-head__cat">{{ $project->kicker }}</span>@endif
              @if($project->location)<span>{{ $project->location }}</span>@endif
              @if($project->year)<span>{{ $project->year }}</span>@endif
              @if($project->live_url)
                <span style="display:inline-flex; align-items:center; gap:5px; color:#10b981; font-weight:700;">
                  <span class="live-pulse"></span> Production Live
                </span>
              @endif
            </div>

            <h1 class="article-head__title">{{ $project->title }}</h1>
            @if($project->excerpt)
              <p class="article-head__dek">{{ $project->excerpt }}</p>
            @endif

            <!-- Quick Action Nav -->
            <div style="display:flex; flex-wrap:wrap; gap:12px; margin: 1.5rem 0 2rem; align-items:center;">
              @if($project->live_url)
                <a href="{{ $project->live_url }}" target="_blank" rel="noopener noreferrer" class="btn btn--accent" style="display:inline-flex; align-items:center; gap:8px; font-weight:700;">
                  <span class="live-pulse"></span>
                  <span>Visit Live Platform</span>
                  <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M4.5 1.5H14.5V11.5H13V4.06L2.53 14.53L1.47 13.47L11.94 3H4.5V1.5Z"/></svg>
                </a>
              @endif
              @if($project->body)
                <a href="#architecture" class="btn btn--ghost" style="font-size:0.85rem;">System Architecture &darr;</a>
              @endif
              @if(!empty($project->gallery))
                <a href="#gallery" class="btn btn--ghost" style="font-size:0.85rem;">Interface &amp; Screenshots &darr;</a>
              @endif
              @if($project->video_url || $project->video_src)
                <a href="#video-showcase" class="btn btn--ghost" style="font-size:0.85rem;">Demo Video &darr;</a>
              @endif
            </div>
          </div>

          <!-- Right Column: Animated Project / Brand Emblem Showcase -->
          <div class="project-hero-split__stage">
            <div class="project-emblem-stage">
              <div class="project-emblem__halo" aria-hidden="true"></div>

              <!-- Outer Clockwise Orbital Ring -->
              <div class="project-emblem__orbit project-emblem__orbit--outer" aria-hidden="true">
                <span class="project-emblem__node project-emblem__node--accent"></span>
                <span class="project-emblem__node project-emblem__node--live"></span>
              </div>

              <!-- Inner Counter-Clockwise Orbital Ring -->
              <div class="project-emblem__orbit project-emblem__orbit--inner" aria-hidden="true">
                <span class="project-emblem__node project-emblem__node--navy"></span>
              </div>

              <!-- Levitating Glass-Porcelain Medallion -->
              @if($project->live_url)
                <a href="{{ $project->live_url }}" target="_blank" rel="noopener noreferrer" class="project-emblem__core" title="Open {{ $project->client ?? $project->title }} Live Platform">
                  <span class="project-emblem__sheen" aria-hidden="true"></span>
                  <img class="project-emblem__img" src="{{ $projectLogo }}" alt="{{ $project->client ?? $project->title }} Logo" width="220" height="220"/>
                </a>
              @else
                <div class="project-emblem__core">
                  <span class="project-emblem__sheen" aria-hidden="true"></span>
                  <img class="project-emblem__img" src="{{ $projectLogo }}" alt="{{ $project->client ?? $project->title }} Logo" width="220" height="220"/>
                </div>
              @endif

              <!-- Architectural Status Badge -->
              <div class="project-emblem__pill">
                <span class="live-pulse"></span>
                <span>{{ $project->live_url ? 'VERIFIED PRODUCTION SYSTEM' : 'ARCHITECTED BY EMPETIX' }}</span>
              </div>
            </div>
          </div>
        </div>

        @if(!empty($project->facts))
          <dl class="facts">
            @foreach((array) $project->facts as $fact)
              <div>
                <dt>{{ $fact['label'] ?? '' }}</dt>
                <dd>{{ $fact['value'] ?? '' }}</dd>
              </div>
            @endforeach
          </dl>
        @endif
      </div>
    </section>

    <!-- Full-Bleed Hero Image -->
    <section class="bleed">
      <div class="container">
        <div class="frame bleed__frame">
          <img src="{{ str_starts_with((string) $project->image, 'uploads/') ? asset('storage/'.$project->image) : asset($project->image ?: 'assets/img/work/placeholder.jpg') }}" alt="{{ $project->title }}" width="1600" height="900"/>
        </div>
      </div>
    </section>

    <!-- Audited Outcomes Strip -->
    @if(!empty($project->stats))
      <section class="section section--sm">
        <div class="container">
          <div class="stats">
            @foreach((array) $project->stats as $stat)
              <div class="stat">
                <div class="stat__value">
                  <span data-counter data-counter-target="{{ $stat['target'] ?? 0 }}" data-counter-decimals="{{ $stat['decimals'] ?? 0 }}">{{ $stat['target'] ?? 0 }}</span>
                  <span class="stat__suffix">{{ $stat['suffix'] ?? '' }}</span>
                </div>
                <p class="stat__label">{{ $stat['label'] ?? '' }}</p>
              </div>
            @endforeach
          </div>
        </div>
      </section>
    @endif

    <!-- Technical Architecture & Detailed Implementation (HTML Supported) -->
    @if($project->body)
      <section id="architecture" class="section section--ruled">
        <div class="container case-cols">
          <div class="case-cols__label">
            <span class="eyebrow">CORE ARCHITECTURE</span>
            <div style="font-size:0.8rem; color:var(--color-text-muted, #888); margin-top:0.5rem; line-height:1.4;">
              System Design, High-Concurrency Specs &amp; Implementation
            </div>
          </div>
          <div class="case-cols__body prose" style="max-width: 100%;">
            {!! $project->body !!}
          </div>
        </div>
      </section>
    @endif

    <!-- Product Design & Screenshots Gallery Section -->
    @if(!empty($project->gallery))
      <section id="gallery" class="section section--alt">
        <div class="container">
          <div style="margin-bottom: 2rem;">
            <span class="eyebrow">INTERFACE &amp; PLATFORM DESIGN</span>
            <h2 style="font-size: var(--text-2xl); margin-top: 0.5rem; letter-spacing: var(--tracking-tight);">
              Production Screens, Architecture &amp; Dashboards
            </h2>
            <p style="color: var(--color-text-body); max-width: 45rem;">
              Audited production interfaces, high-concurrency interactive workflows, and mobile-responsive dashboards engineered for this platform.
            </p>
          </div>

          <div class="work-gallery-grid">
            @foreach((array) $project->gallery as $galleryImg)
              @php
                $gSrc = str_starts_with((string) $galleryImg, 'uploads/') ? asset('storage/'.$galleryImg) : asset($galleryImg);
              @endphp
              <div class="work-gallery-item">
                <a href="{{ $gSrc }}" target="_blank" rel="noopener noreferrer" title="View Full High-Resolution Screen">
                  <img src="{{ $gSrc }}" alt="{{ $project->title }} screenshot" loading="lazy" width="800" height="500">
                </a>
              </div>
            @endforeach
          </div>
        </div>
      </section>
    @endif

    <!-- Video Showcase Section -->
    @if($project->video_url || $project->video_src)
      <section id="video-showcase" class="section section--alt">
        <div class="container">
          <div style="margin-bottom: 1.5rem;">
            <span class="eyebrow">SYSTEM DEMO &amp; RECORDING</span>
            <h2 style="font-size: var(--text-2xl); margin-top: 0.5rem; letter-spacing: var(--tracking-tight);">
              Live Platform Walkthrough
            </h2>
          </div>

          @if($project->video_url)
            @php
              $embedUrl = video_embed_url($project->video_url);
            @endphp
            <div class="video-responsive-wrap">
              @if(str_contains($embedUrl, 'youtube') || str_contains($embedUrl, 'vimeo'))
                <iframe src="{{ $embedUrl }}" title="{{ $project->title }} Video Showcase" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
              @else
                <video controls preload="metadata" @if($project->video_poster) poster="{{ content_asset($project->video_poster) }}" @endif>
                  <source src="{{ $embedUrl }}" type="video/mp4"/>
                  Your browser does not support HTML5 video.
                </video>
              @endif
            </div>
          @elseif($project->video_src)
            <div class="video" data-video-player="{{ $project->slug }}-reel">
              @if($project->video_poster)
                <img class="video__poster" src="{{ content_asset($project->video_poster) }}" alt="{{ $project->title }} video poster" width="1280" height="720"/>
              @endif
              <video class="video__el" preload="metadata" @if($project->video_poster) poster="{{ content_asset($project->video_poster) }}" @endif>
                <source src="{{ content_asset($project->video_src) }}" type="{{ video_mime($project->video_src) }}"/>
              </video>
              <button type="button" class="video__btn" data-video-play aria-label="Play {{ $project->title }} case study video">
                <span class="video__btn-inner">
                  <span class="video__btn-icon">
                    <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><polygon points="4,2 14,8 4,14"/></svg>
                  </span>
                  <span>Play System Walkthrough</span>
                </span>
              </button>
            </div>
          @endif

          <div class="video__meta" style="margin-top:1rem;">
            <span>ENGINEERED &amp; AUDITED BY EMPETIX ARCHITECTURE TEAM</span>
          </div>
        </div>
      </section>
    @endif

    <!-- Narrative Sections (Discovery, Implementation, Outcomes) -->
    @php($secNo = 0)
    @foreach((array) $project->sections as $section)
      @php($secNo++)
      <section class="section {{ $secNo % 2 ? 'section--ruled' : '' }}">
        <div class="container case-cols">
          <div class="case-cols__label">
            <span class="eyebrow">{{ str_pad((string) $secNo, 2, '0', STR_PAD_LEFT) }} / {{ $section['eyebrow'] ?? '' }}</span>
          </div>
          <div class="case-cols__body prose">
            <h2>{{ $section['heading'] ?? '' }}</h2>
            @foreach((array) ($section['items'] ?? []) as $paragraph)
              <p>{{ $paragraph }}</p>
            @endforeach
            @if($loop->last && $project->quote)
              <blockquote>
                &ldquo;{{ $project->quote }}&rdquo;
                @if($project->quote_cite)<cite>&mdash; {{ $project->quote_cite }}</cite>@endif
              </blockquote>
            @endif
          </div>
        </div>
      </section>
    @endforeach

    <!-- Bottom CTA Bar -->
    <section class="section section--sm" style="border-block-start: var(--border) solid var(--color-line); margin-block-start: var(--space-8);">
      <div class="container" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:20px;">
        <div>
          <span class="eyebrow">EXPLORE LIVE OR BUILD NEXT</span>
          <h3 style="font-size:var(--text-xl); margin-top:0.3rem;">Architect your mission-critical software with our team.</h3>
        </div>
        <div style="display:flex; gap:12px; align-items:center;">
          @if($project->live_url)
            <a href="{{ $project->live_url }}" target="_blank" rel="noopener noreferrer" class="btn btn--accent">
              <span>Visit {{ $project->client ?? 'Live Platform' }} ↗</span>
            </a>
          @endif
          <a href="{{ url('/contact') }}" class="btn btn--ghost">Start Architecture Brief →</a>
        </div>
      </div>
    </section>

    <!-- Next Project Pager -->
    <div class="container" style="margin-block-end: var(--space-8);">
      @if($nextProject)
      <a href="{{ route('work.show', $nextProject->slug) }}" class="pager">
        <span class="pager__label">Next Case Study</span>
        <span class="pager__title">
          <span>{{ $nextProject->title }}</span>
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="currentColor" stroke-width="2"/></svg>
        </span>
      </a>
      @endif
    </div>
</main>
@endsection
