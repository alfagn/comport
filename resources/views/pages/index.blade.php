@extends('layouts.public', ['activePage' => 'home'])

@section('meta')
  <meta name="description" content="Empetix is a multinational software engineering and performance marketing studio building web/mobile platforms and high-ROAS growth engines."/>
  <title>Empetix — Software Engineering & Growth Marketing Studio</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="Empetix — Software Engineering & Growth Marketing Studio"/>
  <meta property="og:description" content="Empetix is a multinational software engineering and performance marketing studio building web/mobile platforms and high-ROAS growth engines."/>
  <meta property="og:url" content="https://empetix.vercel.app/"/>
  <meta property="og:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:secure_url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution Logo"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="Empetix — Software Engineering & Growth Marketing Studio"/>
  <meta name="twitter:description" content="Empetix is a multinational software engineering and performance marketing studio building web/mobile platforms and high-ROAS growth engines."/>
  <meta name="twitter:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
@endsection

@section('content')
<main id="main-content">

    <!-- ====================================================================
         1. LINK DEVELOPMENT-STYLE 4-SLIDE INTERACTIVE HERO CAROUSEL
         ==================================================================== -->
    <section class="hero-slider" data-hero-slider aria-label="Featured studio highlights">
      <div class="hero-slider__viewport">

        @foreach($slides as $i => $slide)
        <article class="hero-slide {{ $i === 0 ? 'is-active' : '' }}" data-hero-slide aria-hidden="{{ $i === 0 ? 'false' : 'true' }}">
          <div class="hero-slide__bg">
            <img src="{{ str_starts_with((string) $slide->image, 'uploads/') ? asset('storage/'.$slide->image) : asset($slide->image ?: 'assets/img/home/hero.jpg') }}" alt="{{ $slide->title }}" width="1920" height="1080"/>
          </div>
          <div class="container">
            <div class="hero-slide__content">
              <div class="hero-slide__meta">
                <span class="hero-slide__tag">{{ $slide->tag }}</span>
                <span class="hero-slide__kicker">{{ sec('home', 'hero_kicker', 'NEW CAIRO, EGYPT') }}</span>
              </div>
              <h1 class="hero-slide__title">
                {{ $slide->title }}
              </h1>
              <p class="hero-slide__desc">
                {{ $slide->description }}
              </p>
              <div class="hero-slide__actions">
                @if($slide->cta_label)
                  <a href="{{ url($slide->cta_url ?: '/contact') }}" class="btn btn--accent btn--lg">
                    <span>{{ $slide->cta_label }}</span>
                    <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                      <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                  </a>
                @endif
                <a href="#who-we-are" class="btn btn--ghost btn--lg">
                  <span>{{ sec('home', 'hero_secondary_cta', 'Watch Company Overview') }}</span>
                </a>
              </div>
            </div>
          </div>
        </article>
@endforeach

      </div>

      <!-- Docked 4-Tab Hero Slider Navigation Bar -->
      <div class="hero-slider__dock">
        <div class="container">
          <div class="hero-slider__tabs" role="tablist" aria-label="Hero slides">
            @foreach($slides as $i => $slide)
            <button type="button" class="hero-tab" role="tab" data-hero-tab="{{ $i }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}">
              <span class="hero-tab__progress"><span class="hero-tab__progress-bar"></span></span>
              <span class="hero-tab__num">{{ $slide->tab_label }}</span>
              <span class="hero-tab__title">{{ $slide->tab_title }}</span>
              <span class="hero-tab__sub">{{ $slide->tab_sub }}</span>
            </button>
@endforeach
          </div>
        </div>
      </div>
    </section>

    <!-- Continuous Capabilities Ticker -->
    <div class="ticker" aria-label="Core studio capabilities">
      <div class="ticker__track">
        <span class="ticker__item"><span class="ticker__dot"></span>LARAVEL &amp; DISTRIBUTED CLOUD APIS</span>
        <span class="ticker__item"><span class="ticker__dot"></span>FLUTTER &amp; NATIVE MOBILE ENGINEERING</span>
        <span class="ticker__item"><span class="ticker__dot"></span>PERFORMANCE MARKETING &amp; SERVER-SIDE CAPI</span>
        <span class="ticker__item"><span class="ticker__dot"></span>ARABIC-FIRST (RTL/LTR) DESIGN SYSTEMS</span>
        <span class="ticker__item"><span class="ticker__dot"></span>HEADLESS E-COMMERCE &amp; FINTECH LEDGERS</span>
        <span class="ticker__item"><span class="ticker__dot"></span>COMMERCIAL FILM &amp; 35MM PHOTOGRAPHY</span>
        <span class="ticker__item"><span class="ticker__dot"></span>CONVERSION RATE OPTIMIZATION (CRO)</span>
        <span class="ticker__item" aria-hidden="true"><span class="ticker__dot"></span>LARAVEL &amp; DISTRIBUTED CLOUD APIS</span>
        <span class="ticker__item" aria-hidden="true"><span class="ticker__dot"></span>FLUTTER &amp; NATIVE MOBILE ENGINEERING</span>
        <span class="ticker__item" aria-hidden="true"><span class="ticker__dot"></span>PERFORMANCE MARKETING &amp; SERVER-SIDE CAPI</span>
        <span class="ticker__item" aria-hidden="true"><span class="ticker__dot"></span>ARABIC-FIRST (RTL/LTR) DESIGN SYSTEMS</span>
        <span class="ticker__item" aria-hidden="true"><span class="ticker__dot"></span>HEADLESS E-COMMERCE &amp; FINTECH LEDGERS</span>
        <span class="ticker__item" aria-hidden="true"><span class="ticker__dot"></span>COMMERCIAL FILM &amp; 35MM PHOTOGRAPHY</span>
        <span class="ticker__item" aria-hidden="true"><span class="ticker__dot"></span>CONVERSION RATE OPTIMIZATION (CRO)</span>
      </div>
    </div>

    <!-- ====================================================================
         1.5. COMPANY OVERVIEW FILM ("WHO WE ARE")
         ==================================================================== -->
    <section id="who-we-are" class="overview-film" aria-labelledby="who-we-are-heading">
      <div class="container">
        <header class="overview-film__header">
          <h2 id="who-we-are-heading" class="overview-film__title">{{ sec('home', 'who_we_are', 'Who we are') }}</h2>
          <p class="overview-film__lead">
            Empetix Solution brings software engineering, growth marketing, and strategic investment together under one roof in New Cairo. Watch our 40-second overview to see how we build and scale digital businesses.
          </p>
        </header>

        <div class="overview-film__stage">
          <video
            class="overview-film__video"
            controls
            playsinline
            preload="metadata"
            poster="assets/img/home/overview-poster.jpg"
            width="1920"
            height="1080"
          >
            <source src="{{ asset('assets/video/empetix-overview.mp4') }}" type="video/mp4"/>
            Your browser does not support embedded video playback.
          </video>
        </div>

        <ul class="overview-film__pillars" aria-label="Empetix Solution core pillars">
          <li class="overview-film__pillar">
            <h3 class="overview-film__pillar-title">Software engineering</h3>
            <p class="overview-film__pillar-desc">
              Full-stack web and mobile platforms built for reliability, speed, and long-term scale.
            </p>
          </li>
          <li class="overview-film__pillar">
            <h3 class="overview-film__pillar-title">Growth marketing</h3>
            <p class="overview-film__pillar-desc">
              Engineers and marketers working as one unified team to turn clean data into customer acquisition.
            </p>
          </li>
          <li class="overview-film__pillar">
            <h3 class="overview-film__pillar-title">Strategic investment</h3>
            <p class="overview-film__pillar-desc">
              Backing bold founders with capital, senior technical leadership, and go-to-market execution.
            </p>
          </li>
        </ul>
      </div>
    </section>

    <!-- ====================================================================
         2. INTERACTIVE SOLUTIONS & CAPABILITIES HUB (Link Development Pattern)
         ==================================================================== -->
    <section id="solutions-hub" class="section section--alt" aria-labelledby="solutions-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="solutions-heading" class="t-h2">{{ sec('home', 'solutions_heading', 'End-to-end engineering and growth capabilities.') }}</h2>
            <p class="t-lead">
              Select a practice pillar below to inspect our architecture, deliverables, and verified client outcomes.
            </p>
          </div>
          <div class="section-head__action">
            <a href="{{ url('/contact') }}" class="arrow-link">
              <span>Request Custom Scope</span>
              <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </a>
          </div>
        </div>

        <div class="solutions-hub reveal">
          <!-- Left 4-Col Pillar Selector Tabs -->
          <div class="solutions-hub__nav" role="tablist" aria-label="Solution pillars">
            @foreach($services as $i => $service)
            <button type="button" class="solution-tab" role="tab" data-solution-tab="sol-{{ $service->slug }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}">
              <span class="solution-tab__num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
              <span class="solution-tab__title">{{ $service->title }}</span>
              <span class="solution-tab__tag">{{ $service->tag }}</span>
            </button>
@endforeach
          </div>

          <!-- Right 8-Col Interactive Stage -->
          <div class="solutions-hub__stage">
            @foreach($services as $i => $service)
            <div class="solution-panel {{ $i === 0 ? 'is-active' : '' }}" data-solution-panel="sol-{{ $service->slug }}">
              <div class="solution-panel__body">
                <div>
                  <span class="solution-panel__kicker">{{ $service->kicker }}</span>
                  <h3 class="solution-panel__title">{{ $service->heading }}</h3>
                  <p class="solution-panel__desc">
                    {{ $service->description }}
                  </p>
                  <ul class="solution-panel__deliverables">
                    @foreach((array) $service->deliverables as $deliverable)
                      <li>{{ $deliverable }}</li>
                    @endforeach
                  </ul>
                </div>
                @if($service->cta_label)
                  <a class="btn btn--primary" href="{{ url($service->cta_url ?: '/contact') }}">
                    <span>{{ $service->cta_label }}</span>
                    <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                      <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                  </a>
                @endif
              </div>
              <div class="solution-panel__media">
                @if($service->image)
                  <img alt="{{ $service->heading }}" height="600" src="{{ str_starts_with((string) $service->image, 'uploads/') ? asset('storage/'.$service->image) : asset($service->image) }}" width="800"/>
                @endif
                @if($service->metric_num)
                  <div class="solution-panel__metric-badge">
                    <span class="solution-panel__metric-num">{{ $service->metric_num }}</span>
                    <span class="solution-panel__metric-text">{{ $service->metric_label }}</span>
                  </div>
                @endif
              </div>
            </div>
@endforeach

          </div>
        </div>

        <!-- Industries / Verticals Strip -->
        <div class="sectors-grid reveal">
          @foreach($industries as $i => $industry)
          <a href="{{ url('/work') }}" class="sector-box">
            <span class="sector-box__num">SECTOR {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
            <h3 class="sector-box__title">{{ $industry->title }}</h3>
            <span class="sector-box__sub">{{ $industry->subtitle }}</span>
          </a>
@endforeach
        </div>
      </div>
    </section>

    <!-- ====================================================================
         3. SELECTED SUCCESS STORIES / CASE STUDIES
         ==================================================================== -->
    <section class="section" aria-labelledby="work-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="work-heading" class="t-h2">{{ sec('home', 'work_heading', 'Platforms built to perform. Campaigns built to convert.') }}</h2>
          </div>
          <div class="section-head__action">
            <a href="{{ url('/work') }}" class="arrow-link">
              <span>All 6 Case Studies</span>
              <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </a>
          </div>
        </div>

        <div class="work-grid">
          <!-- LOOP: project-item -->
          @foreach($projects as $i => $project)
          <article class="project-card {{ $i === 0 ? 'project-card--wide' : 'project-card--narrow' }} reveal">
            <a class="project-card__link" href="{{ route('work.show', $project->slug) }}">
              <div class="frame project-card__frame frame--zoom">
                <img alt="{{ $project->title }}" height="750" src="{{ str_starts_with((string) $project->image, 'uploads/') ? asset('storage/'.$project->image) : asset($project->image ?: 'assets/img/work/placeholder.jpg') }}" width="1200"/>
              </div>
              <div class="project-card__body">
                <span class="project-card__client">{{ $project->kicker }}</span>
                <span class="project-card__year">{{ $project->year }}</span>
                <h3 class="project-card__title">{{ $project->title }}</h3>
                <ul class="tag-list">
                  @foreach($project->tags as $tag)
                    <li class="tag">{{ $tag->name }}</li>
                  @endforeach
                </ul>
              </div>
            </a>
          </article>
@endforeach
          <!-- END LOOP: project-item -->
        </div>
      </div>
    </section>

    <!-- ====================================================================
         4. FIXED-BACKGROUND CURTAIN WINDOW ("IMAGE EATING" SCROLL) + NUMBERS
         ==================================================================== -->
    <section class="section curtain-window is-inverse" aria-labelledby="impact-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="impact-heading" class="t-h2">{{ sec('home', 'impact_heading', 'Measurable engineering velocity and commercial return.') }}</h2>
          </div>
        </div>

        <div class="stats reveal">
          <!-- LOOP: stat-item -->
          <div class="stat">
            <div class="stat__value">
              <span data-counter data-counter-target="64" data-counter-decimals="0">64</span>
              <span class="stat__suffix">+</span>
            </div>
            <p class="stat__label">Software platforms &amp; growth engines shipped since 2018</p>
          </div>
          <!-- END LOOP: stat-item -->

          <!-- LOOP: stat-item -->
          <div class="stat">
            <div class="stat__value">
              <span data-counter data-counter-target="3.8" data-counter-decimals="1">3.8</span>
              <span class="stat__suffix">x</span>
            </div>
            <p class="stat__label">Audited median return on ad spend across active retainers</p>
          </div>
          <!-- END LOOP: stat-item -->

          <!-- LOOP: stat-item -->
          <div class="stat">
            <div class="stat__value">
              <span data-counter data-counter-target="99.96" data-counter-decimals="2">99.96</span>
              <span class="stat__suffix">%</span>
            </div>
            <p class="stat__label">Production uptime across client SaaS and FinTech applications</p>
          </div>
          <!-- END LOOP: stat-item -->

          <!-- LOOP: stat-item -->
          <div class="stat">
            <div class="stat__value">
              <span data-counter data-counter-target="94" data-counter-decimals="0">94</span>
              <span class="stat__suffix">%</span>
            </div>
            <p class="stat__label">Client retention beyond initial 12-month engagement</p>
          </div>
          <!-- END LOOP: stat-item -->
        </div>
      </div>
    </section>

    <!-- ====================================================================
         5. FOUNDERS / OWNERS PREVIEW (أصحاب المشروع)
         ==================================================================== -->
    @include('partials.founders')

    <!-- ====================================================================
         6. STRATEGIC CORPORATE PARTNERS (شركاء من الشركات)
         ==================================================================== -->
    <section class="section section--alt section--ruled" aria-labelledby="partners-home-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="partners-home-heading" class="t-h2">{{ sec('home', 'partners_heading', 'Trusted by category leaders and global technology alliances.') }}</h2>
          </div>
          <div class="section-head__action">
            <a href="{{ url('/partners') }}" class="arrow-link">
              <span>Partner Directory</span>
              <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </a>
          </div>
        </div>

        <div class="logo-wall reveal">
          <!-- LOOP: partner-logo -->
          @foreach($partners as $partner)
          <a href="{{ url('/partners') }}" class="logo-wall__item">
            <span class="logo-wall__tier">{{ $partner->tier }}</span>
            <img src="{{ str_starts_with((string) $partner->logo, 'uploads/') ? asset('storage/'.$partner->logo) : asset($partner->logo) }}" alt="{{ $partner->name }}" width="220" height="56"/>
          </a>
@endforeach
          <!-- END LOOP: partner-logo -->
        </div>
      </div>
    </section>

    <!-- ====================================================================
         7. BLOGS / JOURNAL: Articles, Video Episodes & Photo Essays
         ==================================================================== -->
    <section class="section" aria-labelledby="blog-home-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="blog-home-heading" class="t-h2">{{ sec('home', 'journal_heading', 'Engineering essays, video episodes & behind-the-scenes photo dispatches.') }}</h2>
          </div>
          <div class="section-head__action">
            <a href="{{ url('/blog') }}" class="arrow-link">
              <span>View All Posts</span>
              <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </a>
          </div>
        </div>

        <div class="post-grid">
          <!-- LOOP: blog-card -->
          @foreach($posts as $post)
          <article class="post-card reveal">
            <a class="post-card__link" href="{{ route('blog.show', $post->slug) }}">
              <div class="frame post-card__frame frame--zoom">
                <img alt="{{ $post->title }}" height="533" src="{{ str_starts_with((string) $post->cover_image, 'uploads/') ? asset('storage/'.$post->cover_image) : asset($post->cover_image ?: 'assets/img/blog/placeholder.jpg') }}" width="800"/>
                <span class="post-card__badge">{{ $post->type === 'video' ? 'VIDEO EPISODE' : ($post->type === 'photos' ? 'PHOTO ESSAY' : 'ARTICLE') }}</span>
              </div>
              <div class="post-card__meta">
                <span class="post-card__cat">{{ $post->category }}</span>
                <time datetime="{{ $post->published_at?->format('Y-m-d') }}">{{ $post->published_at?->format('d M Y') }}</time>
                @if($post->meta['card_label'] ?? null)<span>{{ $post->meta['card_label'] }}</span>@endif
              </div>
              <h3 class="post-card__title">{{ $post->title }}</h3>
              <p class="post-card__excerpt">{{ $post->excerpt }}</p>
            </a>
          </article>
@endforeach
          <!-- END LOOP: blog-card -->
        </div>
      </div>
    </section>

    <!-- ====================================================================
         8. CAREERS BANNER (Fixed-Background Curtain Scroll -> jobs.empetix.com)
         ==================================================================== -->
    <section class="cta-band" aria-labelledby="careers-band-title">
      <div class="cta-band__media">
        <img src="{{ asset('assets/img/careers/culture.jpg') }}" alt="Empetix engineers and designers collaborating during a studio workshop" width="1600" height="900"/>
      </div>
      <div class="container cta-band__inner reveal">
        <div class="cta-band__main">
          <span class="eyebrow eyebrow--inverse">07 / Careers Portal &middot; jobs.empetix.com</span>
          <h2 id="careers-band-title" class="cta-band__title">{{ sec('home', 'careers_band_title', 'Do the sharpest work of your career without the agency theatre.') }}</h2>
        </div>
        <div class="cta-band__side">
          <p>
            Explore open roles in Laravel, Next.js, Flutter, Product Design, and Performance Marketing on our dedicated talent portal.
          </p>
          <a href="{{ url('/careers') }}" class="btn btn--accent btn--lg">
            <span>Launch jobs.empetix.com (6 Open Roles)</span>
            <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
            </svg>
          </a>
        </div>
      </div>
    </section>
@endsection
