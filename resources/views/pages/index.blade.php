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

        <!-- SLIDE 01: Digital Product Engineering & Growth -->
        <article class="hero-slide is-active" data-hero-slide aria-hidden="false">
          <div class="hero-slide__bg">
            <img src="{{ asset('assets/img/home/hero.jpg') }}" alt="Empetix software engineers and creative directors collaborating around an oak table in our Cairo studio" width="1920" height="1080"/>
          </div>
          <div class="container">
            <div class="hero-slide__content">
              <div class="hero-slide__meta">
                <span class="hero-slide__tag">SOFTWARE &amp; GROWTH PARTNER</span>
                <span class="hero-slide__kicker">NEW CAIRO, EGYPT</span>
              </div>
              <h1 class="hero-slide__title">
                We build web and mobile software and run growth marketing.
              </h1>
              <p class="hero-slide__desc">
                Empetix is an end-to-end software engineering and performance marketing partner. Our engineers and marketers work on the same team to build your software and grow your customer base.
              </p>
              <div class="hero-slide__actions">
                <a href="{{ url('/contact') }}" class="btn btn--accent btn--lg">
                  <span>Start a Project</span>
                  <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                  </svg>
                </a>
                <a href="#who-we-are" class="btn btn--ghost btn--lg">
                  <span>Watch Company Overview</span>
                </a>
              </div>
            </div>
          </div>
        </article>

        <!-- SLIDE 02: Performance Marketing, CRO & Commercial Production -->
        <article class="hero-slide" data-hero-slide aria-hidden="true">
          <div class="hero-slide__bg">
            <img src="{{ asset('assets/img/blog/performance-playbook.jpg') }}" alt="Performance marketing analysts reviewing attribution telemetry on dual monitors" width="1920" height="1080"/>
          </div>
          <div class="container">
            <div class="hero-slide__content">
              <div class="hero-slide__meta">
                <span class="hero-slide__tag">PERFORMANCE MARKETING &amp; CRO</span>
                <span class="hero-slide__kicker">3.8X AUDITED MEDIAN ROAS</span>
              </div>
              <h2 class="hero-slide__title">
                Paid media, video production, and conversion work for growing brands.
              </h2>
              <p class="hero-slide__desc">
                We replace guesswork with server-side Conversions API (CAPI) engineering, in-house commercial film shoots, and bilingual conversion optimization across MENA and Europe.
              </p>
              <div class="hero-slide__actions">
                <a href="{{ url('/work') }}" class="btn btn--accent btn--lg">
                  <span>View Growth Case Studies</span>
                  <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                  </svg>
                </a>
                <a href="{{ url('/blog/photos') }}" class="btn btn--ghost btn--lg">
                  <span>Inside Our Production Lab</span>
                </a>
              </div>
            </div>
          </div>
        </article>

        <!-- SLIDE 03: Flagship Case Study — NilePay FinTech -->
        <article class="hero-slide" data-hero-slide aria-hidden="true">
          <div class="hero-slide__bg">
            <img src="{{ asset('assets/img/work/nilepay.jpg') }}" alt="Customer tapping smartphone to pay at a sunlit Cairo cafe counter" width="1920" height="1080"/>
          </div>
          <div class="container">
            <div class="hero-slide__content">
              <div class="hero-slide__meta">
                <span class="hero-slide__tag">SUCCESS STORY: NILEPAY</span>
                <span class="hero-slide__kicker">FINTECH &middot; 1.4M ACTIVE WALLETS</span>
              </div>
              <h2 class="hero-slide__title">
                How NilePay reached 1.4 million active mobile wallet users.
              </h2>
              <p class="hero-slide__desc">
                Pairing a 60fps Flutter mobile wallet and event-sourced Laravel 11 settlement core with a nationwide paid acquisition engine that lowered CAC by 41%.
              </p>
              <div class="hero-slide__actions">
                <a href="{{ url('/work/nilepay') }}" class="btn btn--accent btn--lg">
                  <span>Read Full Case Dossier</span>
                  <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                  </svg>
                </a>
                <a href="{{ url('/work') }}" class="btn btn--ghost btn--lg">
                  <span>All 6 Projects</span>
                </a>
              </div>
            </div>
          </div>
        </article>

        <!-- SLIDE 04: Video Episode — 5-Day Product & Growth Sprint -->
        <article class="hero-slide" data-hero-slide aria-hidden="true">
          <div class="hero-slide__bg">
            <img src="{{ asset('assets/img/blog/design-sprint.jpg') }}" alt="Empetix product and engineering team voting on wireframe sketches during a 5-day sprint" width="1920" height="1080"/>
          </div>
          <div class="container">
            <div class="hero-slide__content">
              <div class="hero-slide__meta">
                <span class="hero-slide__tag">STUDIO VIDEO DOCUMENTARY</span>
                <span class="hero-slide__kicker">RUNTIME 14:20 &middot; FULL EPISODE</span>
              </div>
              <h2 class="hero-slide__title">
                From product problem to tested prototype in five days.
              </h2>
              <p class="hero-slide__desc">
                Watch how our software architects, product designers, and media buyers take a product from commercial bottleneck to user-tested prototype in one week.
              </p>
              <div class="hero-slide__actions">
                <a href="{{ url('/blog/video') }}" class="btn btn--accent btn--lg">
                  <span>Watch Full Video Episode</span>
                  <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                  </svg>
                </a>
                <a href="{{ url('/blog') }}" class="btn btn--ghost btn--lg">
                  <span>Explore All Blogs</span>
                </a>
              </div>
            </div>
          </div>
        </article>

      </div>

      <!-- Docked 4-Tab Hero Slider Navigation Bar -->
      <div class="hero-slider__dock">
        <div class="container">
          <div class="hero-slider__tabs" role="tablist" aria-label="Hero slides">
            <button type="button" class="hero-tab" role="tab" data-hero-tab="0" aria-selected="true">
              <span class="hero-tab__progress"><span class="hero-tab__progress-bar"></span></span>
              <span class="hero-tab__num">OVERVIEW</span>
              <span class="hero-tab__title">Software &amp; Growth Studio</span>
              <span class="hero-tab__sub">End-to-end digital transformation</span>
            </button>

            <button type="button" class="hero-tab" role="tab" data-hero-tab="1" aria-selected="false">
              <span class="hero-tab__progress"><span class="hero-tab__progress-bar"></span></span>
              <span class="hero-tab__num">MARKETING</span>
              <span class="hero-tab__title">Performance &amp; CRO Engine</span>
              <span class="hero-tab__sub">3.8x median ROAS &amp; server-side CAPI</span>
            </button>

            <button type="button" class="hero-tab" role="tab" data-hero-tab="2" aria-selected="false">
              <span class="hero-tab__progress"><span class="hero-tab__progress-bar"></span></span>
              <span class="hero-tab__num">CASE STUDY</span>
              <span class="hero-tab__title">NilePay FinTech Platform</span>
              <span class="hero-tab__sub">1.4M wallets &middot; +310% activation</span>
            </button>

            <button type="button" class="hero-tab" role="tab" data-hero-tab="3" aria-selected="false">
              <span class="hero-tab__progress"><span class="hero-tab__progress-bar"></span></span>
              <span class="hero-tab__num">VIDEO EPISODE</span>
              <span class="hero-tab__title">Inside a 5-Day Sprint</span>
              <span class="hero-tab__sub">Watch 14:20 studio documentary</span>
            </button>
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
          <h2 id="who-we-are-heading" class="overview-film__title">Who we are</h2>
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
            <h2 id="solutions-heading" class="t-h2">End-to-end engineering and growth capabilities.</h2>
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
            <button type="button" class="solution-tab" role="tab" data-solution-tab="sol-software" aria-selected="true">
              <span class="solution-tab__num">01</span>
              <span class="solution-tab__title">Custom Web, SaaS &amp; Cloud APIs</span>
              <span class="solution-tab__tag">Engineering</span>
            </button>

            <button type="button" class="solution-tab" role="tab" data-solution-tab="sol-mobile" aria-selected="false">
              <span class="solution-tab__num">02</span>
              <span class="solution-tab__title">Mobile Apps &amp; FinTech Ledgers</span>
              <span class="solution-tab__tag">Flutter / Core</span>
            </button>

            <button type="button" class="solution-tab" role="tab" data-solution-tab="sol-marketing" aria-selected="false">
              <span class="solution-tab__num">03</span>
              <span class="solution-tab__title">Performance Marketing &amp; Paid Media</span>
              <span class="solution-tab__tag">Growth</span>
            </button>

            <button type="button" class="solution-tab" role="tab" data-solution-tab="sol-production" aria-selected="false">
              <span class="solution-tab__num">04</span>
              <span class="solution-tab__title">Brand Systems, Film &amp; Photography</span>
              <span class="solution-tab__tag">Creative</span>
            </button>

            <button type="button" class="solution-tab" role="tab" data-solution-tab="sol-cro" aria-selected="false">
              <span class="solution-tab__num">05</span>
              <span class="solution-tab__title">Conversion Optimization (CRO) &amp; AI</span>
              <span class="solution-tab__tag">Data / AI</span>
            </button>
          </div>

          <!-- Right 8-Col Interactive Stage -->
          <div class="solutions-hub__stage">
            <!-- Panel 1: Software & Cloud -->
            <div class="solution-panel is-active" data-solution-panel="sol-software">
              <div class="solution-panel__body">
                <div>
                  <span class="solution-panel__kicker">PILLAR SOFTWARE ARCHITECTURE</span>
                  <h3 class="solution-panel__title">Enterprise Web Platforms, Laravel APIs &amp; Cloud Modernization</h3>
                  <p class="solution-panel__desc">
                    We build modular web platforms, B2B portals, and multi-tenant SaaS products in Laravel 11, Next.js, and AWS—engineered for 99.96% uptime and clean ERP/CRM integration.
                  </p>
                  <ul class="solution-panel__deliverables">
                    <li>Domain-Driven Laravel &amp; Node.js Microservices</li>
                    <li>Next.js / TypeScript SSR &amp; Headless Storefronts</li>
                    <li>AWS ECS, RDS &amp; Zero-Downtime CI/CD Pipelines</li>
                    <li>Automated Pest / PHPUnit &amp; Security Audits</li>
                  </ul>
                </div>
                <a href="{{ url('/work/nilepay') }}" class="btn btn--primary">
                  <span>Explore Software Architecture</span>
                  <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                  </svg>
                </a>
              </div>
              <div class="solution-panel__media">
                <img src="{{ asset('assets/img/home/studio.jpg') }}" alt="Two Empetix developers pair-programming at a wooden desk" width="800" height="600"/>
                <div class="solution-panel__metric-badge">
                  <span class="solution-panel__metric-num">99.96%</span>
                  <span class="solution-panel__metric-text">Audited production availability across deployed enterprise platforms</span>
                </div>
              </div>
            </div>

            <!-- Panel 2: Mobile & FinTech -->
            <div class="solution-panel" data-solution-panel="sol-mobile">
              <div class="solution-panel__body">
                <div>
                  <span class="solution-panel__kicker">PILLAR MOBILE &amp; FINTECH</span>
                  <h3 class="solution-panel__title">60fps Consumer Mobile Apps &amp; Event-Sourced Payment Ledgers</h3>
                  <p class="solution-panel__desc">
                    From biometric KYC and NFC merchant checkouts to offline-first fleet logistics tablets, our Flutter and native iOS/Android squads build apps used by 1.4M+ monthly active users.
                  </p>
                  <ul class="solution-panel__deliverables">
                    <li>Flutter &amp; Native Swift / Kotlin Mobile Clients</li>
                    <li>Double-Entry Payment Wallets &amp; Regional Gateways</li>
                    <li>90-Second Biometric &amp; OCR Onboarding Flows</li>
                    <li>Offline-First SQLite Sync for Field Operations</li>
                  </ul>
                </div>
                <a href="{{ url('/work/nilepay') }}" class="btn btn--primary">
                  <span>See NilePay Mobile Case Study</span>
                  <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                  </svg>
                </a>
              </div>
              <div class="solution-panel__media">
                <img src="{{ asset('assets/img/work/nilepay.jpg') }}" alt="Customer paying with smartphone at a cafe counter" width="800" height="600"/>
                <div class="solution-panel__metric-badge">
                  <span class="solution-panel__metric-num">+310%</span>
                  <span class="solution-panel__metric-text">First-transaction wallet activation after onboarding rebuild</span>
                </div>
              </div>
            </div>

            <!-- Panel 3: Performance Marketing -->
            <div class="solution-panel" data-solution-panel="sol-marketing">
              <div class="solution-panel__body">
                <div>
                  <span class="solution-panel__kicker">PILLAR GROWTH &amp; ACQUISITION</span>
                  <h3 class="solution-panel__title">Full-Funnel Performance Media &amp; Server-Side Attribution</h3>
                  <p class="solution-panel__desc">
                    We wire server-side Conversions API (CAPI) events straight from your backend database into Meta, Google, TikTok, and LinkedIn—bidding on real net revenue instead of vanity clicks.
                  </p>
                  <ul class="solution-panel__deliverables">
                    <li>Meta, Google PMax, TikTok &amp; Snapchat Media Buying</li>
                    <li>First-Party Server-Side CAPI &amp; CRM Attribution</li>
                    <li>Weekly Creative Hook &amp; Angle Testing Matrices</li>
                    <li>Cohort LTV:CAC &amp; Payback Period Dashboards</li>
                  </ul>
                </div>
                <a href="{{ url('/work') }}" class="btn btn--primary">
                  <span>Examine Growth Outcomes</span>
                  <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                  </svg>
                </a>
              </div>
              <div class="solution-panel__media">
                <img src="{{ asset('assets/img/blog/performance-playbook.jpg') }}" alt="Marketing analysts reviewing attribution dashboards" width="800" height="600"/>
                <div class="solution-panel__metric-badge">
                  <span class="solution-panel__metric-num">3.8x</span>
                  <span class="solution-panel__metric-text">Audited median Return on Ad Spend across active growth retainers</span>
                </div>
              </div>
            </div>

            <!-- Panel 4: Brand & Commercial Production -->
            <div class="solution-panel" data-solution-panel="sol-production">
              <div class="solution-panel__body">
                <div>
                  <span class="solution-panel__kicker">PILLAR BRAND &amp; PRODUCTION</span>
                  <h3 class="solution-panel__title">Bilingual Brand Systems, Commercial Film &amp; 35mm Photography</h3>
                  <p class="solution-panel__desc">
                    Our in-house cinema and design unit crafts authoritative Arabic/English brand identities, product launch films, and high-converting paid-social video assets.
                  </p>
                  <ul class="solution-panel__deliverables">
                    <li>Arabic &amp; Latin Brand Identity &amp; Design Guidelines</li>
                    <li>In-House Commercial Film &amp; Documentary Crew</li>
                    <li>Product Tabletop &amp; Editorial Lifestyle Photography</li>
                    <li>9:16 Paid Social Video Hook Variations</li>
                  </ul>
                </div>
                <a href="{{ url('/blog/photos') }}" class="btn btn--primary">
                  <span>View Qahwa Shoot Photo Essay</span>
                  <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                  </svg>
                </a>
              </div>
              <div class="solution-panel__media">
                <img src="{{ asset('assets/img/blog/behind-the-lens.jpg') }}" alt="Behind the scenes of a commercial coffee shoot" width="800" height="600"/>
                <div class="solution-panel__metric-badge">
                  <span class="solution-panel__metric-num">+190%</span>
                  <span class="solution-panel__metric-text">Lift in recurring D2C subscription orders post-campaign launch</span>
                </div>
              </div>
            </div>

            <!-- Panel 5: CRO & AI -->
            <div class="solution-panel" data-solution-panel="sol-cro">
              <div class="solution-panel__body">
                <div>
                  <span class="solution-panel__kicker">PILLAR CRO &amp; AI AUTOMATION</span>
                  <h3 class="solution-panel__title">Arabic-First Conversion Optimization &amp; AI Workflow Agents</h3>
                  <p class="solution-panel__desc">
                    We eliminate checkout friction through native RTL/LTR design systems, multivariate A/B testing, and domain-trained AI assistants that automate customer triage and internal operations.
                  </p>
                  <ul class="solution-panel__deliverables">
                    <li>Bilingual Checkout &amp; Onboarding CRO Experiments</li>
                    <li>100% CSS Logical Property Design Systems</li>
                    <li>Domain-Trained RAG AI Copilots &amp; Support Triage</li>
                    <li>Executive BigQuery &amp; Looker Telemetry Pipelines</li>
                  </ul>
                </div>
                <a href="{{ url('/blog/post') }}" class="btn btn--primary">
                  <span>Read Arabic-First CRO Essay</span>
                  <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                  </svg>
                </a>
              </div>
              <div class="solution-panel__media">
                <img src="{{ asset('assets/img/blog/arabic-first.jpg') }}" alt="Designer sketching Arabic RTL wireframes on paper" width="800" height="600"/>
                <div class="solution-panel__metric-badge">
                  <span class="solution-panel__metric-num">+38.4%</span>
                  <span class="solution-panel__metric-text">Mobile checkout completion lift on Arabic-language sessions</span>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- Industries / Verticals Strip -->
        <div class="sectors-grid reveal">
          <a href="{{ url('/work') }}" class="sector-box">
            <span class="sector-box__num">SECTOR 01</span>
            <h3 class="sector-box__title">FinTech &amp; Digital Banking</h3>
            <span class="sector-box__sub">Wallets, KYC &amp; Ledgers</span>
          </a>
          <a href="{{ url('/work') }}" class="sector-box">
            <span class="sector-box__num">SECTOR 02</span>
            <h3 class="sector-box__title">Retail &amp; D2C Commerce</h3>
            <span class="sector-box__sub">Shopify Plus &amp; Omnichannel</span>
          </a>
          <a href="{{ url('/work') }}" class="sector-box">
            <span class="sector-box__num">SECTOR 03</span>
            <h3 class="sector-box__title">Healthcare &amp; MedTech</h3>
            <span class="sector-box__sub">Telemedicine &amp; Patient CRM</span>
          </a>
          <a href="{{ url('/work') }}" class="sector-box">
            <span class="sector-box__num">SECTOR 04</span>
            <h3 class="sector-box__title">Real Estate &amp; PropTech</h3>
            <span class="sector-box__sub">3D Portals &amp; Demand Gen</span>
          </a>
          <a href="{{ url('/work') }}" class="sector-box">
            <span class="sector-box__num">SECTOR 05</span>
            <h3 class="sector-box__title">Logistics &amp; Fleet</h3>
            <span class="sector-box__sub">Control Towers &amp; Telemetry</span>
          </a>
          <a href="{{ url('/work') }}" class="sector-box">
            <span class="sector-box__num">SECTOR 06</span>
            <h3 class="sector-box__title">Enterprise &amp; SaaS</h3>
            <span class="sector-box__sub">Cloud Modernization &amp; AI</span>
          </a>
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
            <h2 id="work-heading" class="t-h2">Platforms built to perform. Campaigns built to convert.</h2>
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
          <article class="project-card project-card--wide reveal">
            <a href="{{ url('/work/nilepay') }}" class="project-card__link">
              <div class="frame project-card__frame frame--zoom">
                <img src="{{ asset('assets/img/work/nilepay.jpg') }}" alt="Woman tapping smartphone to pay at a sunlit Cairo cafe counter" width="1200" height="750"/>
              </div>
              <div class="project-card__body">
                <span class="project-card__client">NILEPAY FINTECH &middot; SOFTWARE + GROWTH LAUNCH</span>
                <span class="project-card__year">2026</span>
                <h3 class="project-card__title">
                  Rebuilding mobile merchant checkout and scaling active wallets to 1.4M users across Egypt &amp; KSA.
                </h3>
                <ul class="tag-list">
                  <li class="tag">Flutter &amp; Laravel Core</li>
                  <li class="tag">Paid Acquisition</li>
                  <li class="tag">+310% Activation</li>
                </ul>
              </div>
            </a>
          </article>
          <!-- END LOOP: project-item -->

          <!-- LOOP: project-item -->
          <article class="project-card project-card--narrow project-card--offset reveal">
            <a href="{{ url('/work/nilepay') }}" class="project-card__link">
              <div class="frame project-card__frame frame--zoom">
                <img src="{{ asset('assets/img/work/dar-ceramics.jpg') }}" alt="Artisan shaping clay on a pottery wheel in a sunlit Cairo studio" width="900" height="675"/>
              </div>
              <div class="project-card__body">
                <span class="project-card__client">DAR CERAMICS &middot; HEADLESS COMMERCE &amp; BRAND FILM</span>
                <span class="project-card__year">2026</span>
                <h3 class="project-card__title">
                  Direct-to-consumer store architecture and European export campaign yielding 4.6x ROAS.
                </h3>
                <ul class="tag-list">
                  <li class="tag">Shopify Plus &amp; Custom 3D Sizer</li>
                  <li class="tag">Commercial Film</li>
                </ul>
              </div>
            </a>
          </article>
          <!-- END LOOP: project-item -->

          <!-- LOOP: project-item -->
          <article class="project-card project-card--narrow reveal">
            <a href="{{ url('/work/nilepay') }}" class="project-card__link">
              <div class="frame project-card__frame frame--zoom">
                <img src="{{ asset('assets/img/work/medlink.jpg') }}" alt="Product designers reviewing telemedicine wireframes pinned to a studio wall" width="900" height="675"/>
              </div>
              <div class="project-card__body">
                <span class="project-card__client">MEDLINK HEALTH &middot; TELEMEDICINE PLATFORM</span>
                <span class="project-card__year">2025</span>
                <h3 class="project-card__title">
                  End-to-end encrypted video clinic and patient retention engine for 380+ specialists.
                </h3>
                <ul class="tag-list">
                  <li class="tag">WebRTC &amp; HIPAA Core</li>
                  <li class="tag">Lifecycle CRM</li>
                </ul>
              </div>
            </a>
          </article>
          <!-- END LOOP: project-item -->

          <!-- LOOP: project-item -->
          <article class="project-card project-card--wide project-card--offset reveal">
            <a href="{{ url('/work/nilepay') }}" class="project-card__link">
              <div class="frame project-card__frame frame--zoom">
                <img src="{{ asset('assets/img/work/palm-residences.jpg') }}" alt="Behind the scenes of a sunset real-estate film shoot on a stone terrace" width="1200" height="750"/>
              </div>
              <div class="project-card__body">
                <span class="project-card__client">PALM RESIDENCES &middot; INTERACTIVE PORTAL &amp; DEMAND GEN</span>
                <span class="project-card__year">2025</span>
                <h3 class="project-card__title">
                  Real-time unit inventory engine and omnichannel launch generating USD 84M in qualified bookings.
                </h3>
                <ul class="tag-list">
                  <li class="tag">Interactive Masterplan</li>
                  <li class="tag">Film &amp; Paid Media</li>
                </ul>
              </div>
            </a>
          </article>
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
            <h2 id="impact-heading" class="t-h2">Measurable engineering velocity and commercial return.</h2>
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
    <!-- PARTIAL: founders -->
<section id="founders" class="founders" aria-labelledby="founders-heading">
  <div class="founders__inner">
    <header class="founders__header">
      <h2 id="founders-heading" class="founders__title">The founders</h2>
      <div class="founders__intro">
        <p class="founders__lead">
          Every engagement at Empetix Solution is led directly by our three co-founders across executive strategy, operations, and software engineering.
        </p>
        <a href="{{ url('/team') }}" class="founders__story-link">Meet the full team</a>
      </div>
    </header>

    <ul class="founders__list">
      <!-- LOOP: founder -->
      <li class="founders__item">
        <a href="{{ url('/founders/ayman') }}" class="founders__link">
          <div class="founders__media">
            <img
              class="founders__photo"
              src="{{ asset('assets/img/team/ayman-ibrahim.jpg') }}"
              alt="Portrait of Ayman Ibrahim, Co-founder and chief executive officer"
              width="800"
              height="1000"
              loading="lazy"
            />
          </div>
          <h3 class="founders__name">Ayman Ibrahim</h3>
          <p class="founders__role">Co-founder and chief executive officer</p>
        </a>
      </li>
      <!-- END LOOP: founder -->

      <li class="founders__item">
        <a href="{{ url('/founders/bishoy') }}" class="founders__link">
          <div class="founders__media">
            <img
              class="founders__photo"
              src="{{ asset('assets/img/team/bishoy-magdy.jpg') }}"
              alt="Portrait of Bishoy Magdy, Co-founder and chief operating officer"
              width="800"
              height="1000"
              loading="lazy"
            />
          </div>
          <h3 class="founders__name">Bishoy Magdy</h3>
          <p class="founders__role">Co-founder and chief operating officer</p>
        </a>
      </li>

      <li class="founders__item">
        <a href="{{ url('/founders/mohanad') }}" class="founders__link">
          <div class="founders__media">
            <img
              class="founders__photo"
              src="{{ asset('assets/img/team/mohanad-shehata.jpg') }}"
              alt="Portrait of Mohanad Ahmed Shehata, Co-founder and chief technology officer"
              width="800"
              height="1000"
              loading="lazy"
            />
          </div>
          <h3 class="founders__name">Mohanad Ahmed Shehata</h3>
          <p class="founders__role">Co-founder and chief technology officer</p>
        </a>
      </li>
    </ul>
  </div>
</section>
<!-- END PARTIAL: founders -->

    <!-- ====================================================================
         6. STRATEGIC CORPORATE PARTNERS (شركاء من الشركات)
         ==================================================================== -->
    <section class="section section--alt section--ruled" aria-labelledby="partners-home-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="partners-home-heading" class="t-h2">Trusted by category leaders and global technology alliances.</h2>
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
          <a href="{{ url('/partners') }}" class="logo-wall__item">
            <span class="logo-wall__tier">FINTECH</span>
            <img src="{{ asset('assets/img/partners/nilepay.svg') }}" alt="NilePay FinTech Partner" width="220" height="56"/>
          </a>
          <!-- END LOOP: partner-logo -->
          <!-- LOOP: partner-logo -->
          <a href="{{ url('/partners') }}" class="logo-wall__item">
            <span class="logo-wall__tier">CLOUD</span>
            <img src="{{ asset('assets/img/partners/aws-partner.svg') }}" alt="AWS Advanced Cloud Partner" width="220" height="56"/>
          </a>
          <!-- END LOOP: partner-logo -->
          <!-- LOOP: partner-logo -->
          <a href="{{ url('/partners') }}" class="logo-wall__item">
            <span class="logo-wall__tier">COMMERCE</span>
            <img src="{{ asset('assets/img/partners/shopify-plus.svg') }}" alt="Shopify Plus Enterprise Partner" width="220" height="56"/>
          </a>
          <!-- END LOOP: partner-logo -->
          <!-- LOOP: partner-logo -->
          <a href="{{ url('/partners') }}" class="logo-wall__item">
            <span class="logo-wall__tier">GROWTH</span>
            <img src="{{ asset('assets/img/partners/meta-business.svg') }}" alt="Meta Business Performance Partner" width="220" height="56"/>
          </a>
          <!-- END LOOP: partner-logo -->
          <!-- LOOP: partner-logo -->
          <a href="{{ url('/partners') }}" class="logo-wall__item">
            <span class="logo-wall__tier">PAYMENTS</span>
            <img src="{{ asset('assets/img/partners/stripe-partner.svg') }}" alt="Stripe Verified Partner" width="220" height="56"/>
          </a>
          <!-- END LOOP: partner-logo -->
          <!-- LOOP: partner-logo -->
          <a href="{{ url('/partners') }}" class="logo-wall__item">
            <span class="logo-wall__tier">HEALTH</span>
            <img src="{{ asset('assets/img/partners/medlink.svg') }}" alt="MedLink Health Partner" width="220" height="56"/>
          </a>
          <!-- END LOOP: partner-logo -->
          <!-- LOOP: partner-logo -->
          <a href="{{ url('/partners') }}" class="logo-wall__item">
            <span class="logo-wall__tier">LOGISTICS</span>
            <img src="{{ asset('assets/img/partners/cargoline.svg') }}" alt="CargoLine MENA Partner" width="220" height="56"/>
          </a>
          <!-- END LOOP: partner-logo -->
          <!-- LOOP: partner-logo -->
          <a href="{{ url('/partners') }}" class="logo-wall__item">
            <span class="logo-wall__tier">REAL ESTATE</span>
            <img src="{{ asset('assets/img/partners/palm-residences.svg') }}" alt="Palm Developments Partner" width="220" height="56"/>
          </a>
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
            <h2 id="blog-home-heading" class="t-h2">Engineering essays, video episodes &amp; behind-the-scenes photo dispatches.</h2>
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
          <article class="post-card reveal">
            <a href="{{ url('/blog/post') }}" class="post-card__link">
              <div class="frame post-card__frame frame--zoom">
                <img src="{{ asset('assets/img/blog/arabic-first.jpg') }}" alt="Designer sketching right-to-left Arabic interface grids on paper" width="800" height="533"/>
                <span class="post-card__badge">ARTICLE + VIDEO + PHOTOS</span>
              </div>
              <div class="post-card__meta">
                <span class="post-card__cat">DESIGN &amp; CRO</span>
                <time datetime="2026-11-14">14 NOV 2026</time>
                <span>8 MIN READ</span>
              </div>
              <h3 class="post-card__title">Engineering Arabic-First Design Systems &amp; High-Conversion Funnels</h3>
              <p class="post-card__excerpt">
                Why mirroring an English layout fails Arabic users—and how logical CSS properties, optical typography, and localized checkout flows lifted conversion by 38%.
              </p>
            </a>
          </article>
          <!-- END LOOP: blog-card -->

          <!-- LOOP: blog-card -->
          <article class="post-card reveal">
            <a href="{{ url('/blog/video') }}" class="post-card__link">
              <div class="frame post-card__frame frame--zoom">
                <img src="{{ asset('assets/img/blog/design-sprint.jpg') }}" alt="Empetix team voting on product sprint wireframes pinned to a concrete wall" width="800" height="533"/>
                <span class="post-card__play" aria-hidden="true">
                  <svg viewBox="0 0 16 16" fill="currentColor"><polygon points="4,2 14,8 4,14"/></svg>
                </span>
                <span class="post-card__badge">VIDEO EPISODE &middot; 14:20</span>
              </div>
              <div class="post-card__meta">
                <span class="post-card__cat">VIDEO DOCUMENTARY</span>
                <time datetime="2026-11-05">05 NOV 2026</time>
                <span>FULL VIDEO + CHAPTERS</span>
              </div>
              <h3 class="post-card__title">Inside an Empetix 5-Day Product &amp; Growth Sprint (Watch Full Episode)</h3>
              <p class="post-card__excerpt">
                Go inside our Cairo studio as engineers, product designers, and media buyers take a FinTech product from problem statement to tested prototype in five days.
              </p>
            </a>
          </article>
          <!-- END LOOP: blog-card -->

          <!-- LOOP: blog-card -->
          <article class="post-card reveal">
            <a href="{{ url('/blog/photos') }}" class="post-card__link">
              <div class="frame post-card__frame frame--zoom">
                <img src="{{ asset('assets/img/blog/behind-the-lens.jpg') }}" alt="Behind the scenes of a specialty coffee commercial shoot under warm studio lighting" width="800" height="533"/>
                <span class="post-card__badge">PHOTO ESSAY &middot; GALLERY</span>
              </div>
              <div class="post-card__meta">
                <span class="post-card__cat">CAMPAIGN PRODUCTION</span>
                <time datetime="2026-10-22">22 OCT 2026</time>
                <span>LIGHTBOX + VIDEO</span>
              </div>
              <h3 class="post-card__title">Behind the Lens: Directing the Qahwa Roasters Omnichannel Launch Shoot</h3>
              <p class="post-card__excerpt">
                A behind-the-scenes photo essay and lighting breakdown from our two-day commercial film and still photography production in Downtown Cairo.
              </p>
            </a>
          </article>
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
          <h2 id="careers-band-title" class="cta-band__title">Do the sharpest work of your career without the agency theatre.</h2>
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
