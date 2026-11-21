<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <meta name="description" content="{{ $metaDescription ?? \App\Modules\CMS\Models\SiteSetting::get('site_tagline', 'Empetix is a multinational software engineering and performance marketing studio building web/mobile platforms and high-ROAS growth engines.') }}"/>
  <title>{{ isset($title) ? $title . ' — ' . config('app.name', 'Empetix') : \App\Modules\CMS\Models\SiteSetting::get('site_name', 'Empetix') . ' — Software Engineering & Growth Marketing Studio' }}</title>

  <!-- Open Graph -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="{{ $title ?? \App\Modules\CMS\Models\SiteSetting::get('site_name', 'Empetix') }}"/>
  <meta property="og:description" content="{{ $metaDescription ?? \App\Modules\CMS\Models\SiteSetting::get('site_tagline', 'Empetix is a multinational software engineering and performance marketing studio building web/mobile platforms and high-ROAS growth engines.') }}"/>
  <meta property="og:image" content="{{ asset('assets/img/og-empetix.jpg') }}"/>

  <!-- Fonts & Assets -->
  <link rel="preload" as="font" type="font/woff2" href="{{ asset('assets/fonts/archivo-700.woff2') }}" crossorigin/>
  <link rel="preload" as="font" type="font/woff2" href="{{ asset('assets/fonts/public-sans-400.woff2') }}" crossorigin/>
  <link rel="icon" type="image/svg+xml" href="{{ asset('assets/img/favicon.svg') }}"/>
  <link rel="preload" as="image" href="{{ asset('assets/img/logo/empetix-logo.png') }}" fetchpriority="high"/>

  <!-- Core Main Stylesheet (Empetix Brand Design System) -->
  <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}"/>
  <script src="{{ asset('assets/js/preloader-gate.js') }}"></script>
  <noscript>
    <link rel="stylesheet" href="{{ asset('assets/css/noscript.css') }}"/>
  </noscript>

  @livewireStyles
  {{ $head ?? '' }}
</head>
<body class="{{ $bodyClass ?? '' }}">

  <!-- 1. PRELOADER -->
  <div id="preloader" class="preloader" role="status" aria-label="Loading Empetix Solution">
    <span id="preloader-sr" class="preloader__sr" aria-live="polite">Loading 0%</span>
    <div class="preloader__stage">
      <div class="preloader__logo-stack">
        <img class="preloader__logo preloader__logo--base" src="{{ asset('assets/img/logo/empetix-logo.png') }}" alt="Empetix Solution" width="881" height="302" fetchpriority="high" decoding="sync"/>
        <div class="preloader__pieces" aria-hidden="true">
          <img class="preloader__piece preloader__piece--e1" src="{{ asset('assets/img/logo/empetix-logo.png') }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
          <img class="preloader__piece preloader__piece--m1" src="{{ asset('assets/img/logo/empetix-logo.png') }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
          <img class="preloader__piece preloader__piece--m2" src="{{ asset('assets/img/logo/empetix-logo.png') }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
          <img class="preloader__piece preloader__piece--m3" src="{{ asset('assets/img/logo/empetix-logo.png') }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
          <img class="preloader__piece preloader__piece--m4" src="{{ asset('assets/img/logo/empetix-logo.png') }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
          <img class="preloader__piece preloader__piece--m5" src="{{ asset('assets/img/logo/empetix-logo.png') }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
          <img class="preloader__piece preloader__piece--p" src="{{ asset('assets/img/logo/empetix-logo.png') }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
          <img class="preloader__piece preloader__piece--e2" src="{{ asset('assets/img/logo/empetix-logo.png') }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
          <img class="preloader__piece preloader__piece--t" src="{{ asset('assets/img/logo/empetix-logo.png') }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
          <img class="preloader__piece preloader__piece--i" src="{{ asset('assets/img/logo/empetix-logo.png') }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
          <img class="preloader__piece preloader__piece--x" src="{{ asset('assets/img/logo/empetix-logo.png') }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
          <img class="preloader__piece preloader__piece--solution" src="{{ asset('assets/img/logo/empetix-logo.png') }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
        </div>
        <span class="preloader__scan" aria-hidden="true"></span>
      </div>
    </div>
    <div class="preloader__hud" aria-hidden="true">
      <span id="preloader-counter" class="preloader__counter">0</span>
    </div>
    <div class="preloader__track" aria-hidden="true">
      <div class="preloader__bar"></div>
    </div>
  </div>

  <a href="#main-content" class="skip-link">Skip to main content</a>

  <!-- 2. SITE HEADER -->
  <div id="site-head" class="site-head">
    <!-- Utility Bar -->
    <div class="utility" role="region" aria-label="Secondary navigation">
      <div class="utility__inner">
        <ul class="utility__links">
          <li class="utility__item"><a href="{{ route('site.careers') }}" class="utility__link {{ request()->routeIs('site.careers*') ? 'is-active' : '' }}">Careers</a></li>
          <li class="utility__item"><a href="{{ route('site.work') }}" class="utility__link {{ request()->routeIs('site.work*') ? 'is-active' : '' }}">Projects</a></li>
          <li class="utility__item"><a href="{{ route('site.partners') }}" class="utility__link {{ request()->routeIs('site.partners') ? 'is-active' : '' }}">Partners</a></li>
          <li class="utility__item"><a href="{{ route('site.team') }}" class="utility__link {{ request()->routeIs('site.team') ? 'is-active' : '' }}">Team</a></li>
          <li class="utility__item">
            <a href="{{ route('site.blog') }}" class="utility__link {{ request()->routeIs('site.blog*') ? 'is-active' : '' }}" data-mega-trigger="journal" aria-expanded="false" aria-controls="mega-journal">
              <span>Blog</span>
            </a>
          </li>
          <li class="utility__item"><a href="{{ route('site.contact') }}" class="utility__link {{ request()->routeIs('site.contact') ? 'is-active' : '' }}">Contact Us</a></li>
          <li class="utility__item" style="margin-inline-start: 12px; border-inline-start: 1px solid rgba(255,255,255,0.15); padding-inline-start: 12px;">
            <a href="{{ route('admin.dashboard') }}" class="utility__link" style="color: #FF4D1C; font-weight: 700;">
              <span>&#9881; Admin Portal</span>
            </a>
          </li>
        </ul>

        <button type="button" id="search-toggle" class="utility__search-block" aria-expanded="false" aria-controls="search-panel" aria-label="Open site search">
          <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <circle cx="8.5" cy="8.5" r="6" stroke="currentColor" stroke-width="2"/>
            <path d="M13 13L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Expandable Search Panel -->
    <div id="search-panel" class="search-panel" role="search" aria-label="Search Empetix Solutions">
      <div class="container">
        <form class="search-panel__form" action="{{ route('site.blog') }}" method="get">
          <label for="search-input" class="sr-only">Search projects, blog posts, and roles</label>
          <input id="search-input" type="search" name="q" class="search-panel__input" placeholder="Search solutions, case studies, engineering essays, video episodes..." autocomplete="off"/>
          <button type="submit" class="btn btn--accent btn--sm">Search</button>
          <button type="button" id="search-close" class="btn btn--ghost btn--sm">Close</button>
        </form>
        <div class="search-panel__hints">
          <span>Quick jump:</span>
          <a href="{{ route('site.work.detail', 'nilepay') }}">NilePay Case Study</a>
          <a href="{{ route('site.blog') }}">Engineering Blog</a>
          <a href="{{ route('site.about') }}#founders">Meet the Founders</a>
          <a href="{{ route('site.contact') }}">Contact Us</a>
          <a href="{{ route('admin.dashboard') }}">Admin CMS</a>
        </div>
      </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="nav-bar">
      <div class="nav-bar__inner">
        <a href="{{ route('home') }}" class="brand" aria-label="Empetix Solution Home">
          <img class="brand__logo-img" src="{{ asset('assets/img/logo/empetix-logo-dark.png') }}" alt="Empetix Solution" width="881" height="302"/>
        </a>

        <nav class="nav" aria-label="Primary navigation">
          <ul class="nav__list">
            <li class="nav__item">
              <a href="{{ route('home') }}#solutions-hub" class="nav__link" data-mega-trigger="capabilities" aria-expanded="false" aria-controls="mega-capabilities">
                <span>Capabilities &amp; Solutions</span>
                <svg class="nav__chevron" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.5"/></svg>
              </a>
            </li>
            <li class="nav__item">
              <a href="{{ route('site.work') }}" class="nav__link {{ request()->routeIs('site.work*') ? 'is-active' : '' }}">
                <span>Projects &amp; Platforms</span>
              </a>
            </li>
            <li class="nav__item">
              <a href="{{ route('site.blog') }}" class="nav__link {{ request()->routeIs('site.blog*') ? 'is-active' : '' }}" data-mega-trigger="journal" aria-expanded="false" aria-controls="mega-journal">
                <span>Blogs &amp; Media</span>
                <svg class="nav__chevron" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.5"/></svg>
              </a>
            </li>
            <li class="nav__item">
              <a href="{{ route('site.partners') }}" class="nav__link {{ request()->routeIs('site.partners') ? 'is-active' : '' }}">
                <span>Partners &amp; Industries</span>
              </a>
            </li>
            <li class="nav__item">
              <a href="{{ route('site.about') }}" class="nav__link {{ request()->routeIs('site.about') ? 'is-active' : '' }}">
                <span>About &amp; Founders</span>
              </a>
            </li>
          </ul>
        </nav>

        <div class="nav-bar__mobile-actions">
          <button type="button" class="mobile-search-toggle" data-search-toggle aria-expanded="false" aria-controls="search-panel" aria-label="Open site search">
            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
              <circle cx="8.5" cy="8.5" r="6" stroke="currentColor" stroke-width="2"/>
              <path d="M13 13L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </button>
          <button type="button" id="menu-toggle" class="menu-toggle" aria-expanded="false" aria-controls="mobile-drawer">
            <span>Menu</span>
            <span class="menu-toggle__bars" aria-hidden="true">
              <span></span>
              <span></span>
            </span>
          </button>
        </div>
      </div>
    </header>

    <!-- Mega Menu -->
    <div id="mega-menu" class="mega" role="region" aria-label="Expanded studio directory">
      <!-- Capabilities -->
      <div id="mega-capabilities" class="mega__panel" data-mega-panel="capabilities">
        <div class="container mega__grid">
          <div class="mega__intro">
            <div>
              <span class="eyebrow">01 / Capabilities</span>
              <h2 class="mega__title">One studio. Code and growth under one roof.</h2>
              <p class="mega__desc">
                We pair senior software engineers with performance marketers and commercial directors so products ship fast and scale profitably.
              </p>
            </div>
            <a href="{{ route('site.work') }}" class="arrow-link">
              <span>Explore All Case Studies</span>
              <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
            </a>
          </div>

          <div class="mega__col">
            <p class="mega__col-title">Software Engineering</p>
            <ul>
              <li>
                <a href="{{ route('site.work') }}" class="mega__link">
                  <span class="mega__link-title"><span>Web &amp; Mobile Platforms</span><svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg></span>
                  <span class="mega__link-sub">Laravel, Next.js, Flutter &amp; high-concurrency APIs</span>
                </a>
              </li>
              <li>
                <a href="{{ route('site.work') }}" class="mega__link">
                  <span class="mega__link-title"><span>E-Commerce &amp; FinTech Core</span><svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg></span>
                  <span class="mega__link-sub">Custom checkouts, wallets, ERP &amp; payment gateways</span>
                </a>
              </li>
              <li>
                <a href="{{ route('site.work') }}" class="mega__link">
                  <span class="mega__link-title"><span>AI Automation &amp; Data Pipelines</span><svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg></span>
                  <span class="mega__link-sub">Internal copilots, CRM workflows &amp; BI telemetry</span>
                </a>
              </li>
            </ul>
          </div>

          <div class="mega__col">
            <p class="mega__col-title">Marketing &amp; Growth</p>
            <ul>
              <li>
                <a href="{{ route('site.work') }}" class="mega__link">
                  <span class="mega__link-title"><span>Performance &amp; Paid Acquisition</span><svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg></span>
                  <span class="mega__link-sub">Full-funnel ROAS across Meta, Google, TikTok &amp; LinkedIn</span>
                </a>
              </li>
              <li>
                <a href="{{ route('site.blog') }}" class="mega__link">
                  <span class="mega__link-title"><span>Brand Systems &amp; Campaign Production</span><svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg></span>
                  <span class="mega__link-sub">Visual identity, commercial video shoots &amp; art direction</span>
                </a>
              </li>
              <li>
                <a href="{{ route('site.blog') }}" class="mega__link">
                  <span class="mega__link-title"><span>CRO, SEO &amp; Lifecycle Retention</span><svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg></span>
                  <span class="mega__link-sub">Arabic/English technical SEO, A/B testing &amp; CRM flows</span>
                </a>
              </li>
            </ul>
          </div>

          <div class="mega__col">
            <p class="mega__col-title">Featured Case Study</p>
            <a href="{{ route('site.work.detail', 'nilepay') }}" class="mega__feature">
              <div class="frame frame--16x9 frame--zoom">
                <img src="{{ asset('assets/img/work/nilepay.jpg') }}" alt="NilePay checkout" width="640" height="360"/>
              </div>
              <span class="t-meta">NILEPAY FINTECH &middot; +310% ACTIVATION</span>
              <p class="mega__feature-title">Rebuilding mobile checkout and launch acquisition for 1.4M users.</p>
            </a>
          </div>
        </div>
      </div>

      <!-- Journal -->
      <div id="mega-journal" class="mega__panel" data-mega-panel="journal">
        <div class="container mega__grid">
          <div class="mega__intro">
            <div>
              <span class="eyebrow">02 / Empetix Journal</span>
              <h2 class="mega__title">Articles, video episodes &amp; photo essays.</h2>
              <p class="mega__desc">
                Field notes from our engineers, product designers, and performance marketers.
              </p>
            </div>
            <a href="{{ route('site.blog') }}" class="arrow-link">
              <span>Open Full Blog Archive</span>
              <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
            </a>
          </div>

          <div class="mega__col">
            <p class="mega__col-title">Engineering Article</p>
            <a href="{{ route('site.blog') }}" class="mega__feature">
              <div class="frame frame--3x2 frame--zoom">
                <img src="{{ asset('assets/img/blog/arabic-first.jpg') }}" alt="Arabic RTL interface layouts" width="600" height="400"/>
              </div>
              <span class="t-meta">DESIGN &amp; CRO &middot; 8 MIN READ</span>
              <p class="mega__feature-title">Engineering Arabic-First Design Systems &amp; High-Conversion Funnels</p>
            </a>
          </div>

          <div class="mega__col">
            <p class="mega__col-title">Video Episode</p>
            <a href="{{ route('site.blog') }}" class="mega__feature">
              <div class="frame frame--3x2 frame--zoom">
                <img src="{{ asset('assets/img/blog/design-sprint.jpg') }}" alt="Sprint team sketches" width="600" height="400"/>
              </div>
              <span class="t-meta">VIDEO + TRANSCRIPT &middot; 14:20</span>
              <p class="mega__feature-title">Inside an Empetix 5-Day Product &amp; Growth Sprint</p>
            </a>
          </div>

          <div class="mega__col">
            <p class="mega__col-title">Photo Essay</p>
            <a href="{{ route('site.blog') }}" class="mega__feature">
              <div class="frame frame--3x2 frame--zoom">
                <img src="{{ asset('assets/img/blog/behind-the-lens.jpg') }}" alt="Specialty coffee shoot" width="600" height="400"/>
              </div>
              <span class="t-meta">PHOTO GALLERY + VIDEO &middot; CAMPAIGN</span>
              <p class="mega__feature-title">Behind the Lens: Directing the Qahwa Roasters Launch Shoot</p>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Mobile Drawer -->
  <aside id="mobile-drawer" class="drawer" aria-label="Mobile navigation" aria-hidden="true">
    <div class="container drawer__head">
      <a href="{{ route('home') }}" class="brand">
        <span class="brand__word">Empetix</span>
      </a>
      <button type="button" id="drawer-close" class="drawer__close">Close</button>
    </div>
    <div class="container drawer__body">
      <ul class="drawer__list">
        <li><a href="{{ route('home') }}" class="drawer__link">Home</a></li>
        <li><a href="{{ route('site.work') }}" class="drawer__link">Projects &amp; Platforms</a></li>
        <li><a href="{{ route('site.about') }}" class="drawer__link">About &amp; Founders</a></li>
        <li><a href="{{ route('site.team') }}" class="drawer__link">Our Team</a></li>
        <li><a href="{{ route('site.partners') }}" class="drawer__link">Partners &amp; Industries</a></li>
        <li><a href="{{ route('site.blog') }}" class="drawer__link">Blogs &amp; Media</a></li>
        <li><a href="{{ route('site.careers') }}" class="drawer__link">Careers</a></li>
        <li><a href="{{ route('site.contact') }}" class="drawer__link">Contact Us</a></li>
        <li><a href="{{ route('admin.dashboard') }}" class="drawer__link" style="color: #FF4D1C;">Admin CMS Portal &rarr;</a></li>
      </ul>

      <div class="drawer__meta">
        <p>New Business: {{ \App\Modules\CMS\Models\SiteSetting::get('contact_email', 'hello@empetix.com') }}</p>
        <p>Careers: talent@empetix.com</p>
        <p>{{ \App\Modules\CMS\Models\SiteSetting::get('contact_address', 'New Cairo, Egypt') }}</p>
      </div>
    </div>
  </aside>

  <!-- 3. MAIN PAGE CONTENT -->
  <main id="main-content">
    {{ $slot }}
  </main>

  <!-- 4. SITE FOOTER -->
  <footer class="site-foot" aria-label="Site footer">
    <div class="container">
      <!-- Executive CTA Banner -->
      <div class="foot-banner">
        <div>
          <h2 class="foot-banner__title">Tell us about the product you want to build.</h2>
          <p class="foot-banner__sub">
            Speak directly with our co-founders and engineering leads. We respond to all project briefs within 24 business hours.
          </p>
        </div>
        <div class="foot-banner__actions">
          <a href="{{ route('site.contact') }}" class="btn btn--accent btn--lg">
            <span>Start a Project</span>
            <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
          </a>
          <a href="{{ route('site.careers') }}" class="btn btn--ghost btn--lg">
            <span>Careers Portal</span>
          </a>
        </div>
      </div>

      <!-- Main 12-Column Sitemap & HQ Grid -->
      <div class="foot-grid">
        <div class="foot-grid__brand">
          <a href="{{ route('home') }}" class="brand" aria-label="Empetix Solution Home">
            <img class="brand__logo-img" src="{{ asset('assets/img/logo/empetix-logo.png') }}" alt="Empetix Solution" width="881" height="302"/>
          </a>

          <p class="foot-grid__summary">
            Empetix Solutions is a software engineering and performance marketing partner. We build enterprise web &amp; mobile platforms and scale the brands behind them.
          </p>

          <div class="foot-hq">
            <div class="foot-hq__item">
              <svg class="foot-hq__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M8 1.5C5.2 1.5 3 3.7 3 6.5C3 10.2 8 14.5 8 14.5C8 14.5 13 10.2 13 6.5C13 3.7 10.8 1.5 8 1.5Z" stroke="currentColor" stroke-width="1.4"/>
                <circle cx="8" cy="6.5" r="1.8" fill="currentColor"/>
              </svg>
              <span>{{ \App\Modules\CMS\Models\SiteSetting::get('contact_address', 'Building 14, District 5 Campus, New Cairo, Cairo, Egypt') }}</span>
            </div>
            <div class="foot-hq__item">
              <svg class="foot-hq__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M2.5 4H13.5V12H2.5V4Z" stroke="currentColor" stroke-width="1.4"/>
                <path d="M2.5 4.5L8 8.5L13.5 4.5" stroke="currentColor" stroke-width="1.4"/>
              </svg>
              <a href="mailto:{{ \App\Modules\CMS\Models\SiteSetting::get('contact_email', 'hello@empetix.com') }}">{{ \App\Modules\CMS\Models\SiteSetting::get('contact_email', 'hello@empetix.com') }}</a>
            </div>
            <div class="foot-hq__item">
              <svg class="foot-hq__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M3.5 2.5H6.5L8 6L6 7.5C6.8 9.2 8.3 10.7 10 11.5L11.5 9.5L15 11V14C15 14.5 14.5 15 13.8 15C7.2 14.6 2.9 10.3 2.5 3.7C2.5 3 3 2.5 3.5 2.5Z" stroke="currentColor" stroke-width="1.4"/>
              </svg>
              <a href="tel:{{ \App\Modules\CMS\Models\SiteSetting::get('contact_phone', '+20 2 2792 8400') }}">{{ \App\Modules\CMS\Models\SiteSetting::get('contact_phone', '+20 2 2792 8400') }}</a>
            </div>
          </div>
        </div>

        <div class="foot-grid__col">
          <h3 class="foot-col__title">Company</h3>
          <ul class="foot-col__list">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('site.about') }}">About &amp; Founders</a></li>
            <li><a href="{{ route('site.team') }}">Our Team</a></li>
            <li><a href="{{ route('site.work') }}">Selected Projects</a></li>
            <li><a href="{{ route('site.partners') }}">Corporate Partners</a></li>
            <li><a href="{{ route('site.blog') }}">Blogs &amp; Media</a></li>
            <li><a href="{{ route('site.careers') }}">Careers Portal</a></li>
            <li><a href="{{ route('site.contact') }}">Contact Us</a></li>
          </ul>
        </div>

        <div class="foot-grid__col">
          <h3 class="foot-col__title">Software</h3>
          <ul class="foot-col__list">
            <li><a href="{{ route('site.work') }}">Web &amp; SaaS Platforms</a></li>
            <li><a href="{{ route('site.work') }}">Mobile Apps (Flutter)</a></li>
            <li><a href="{{ route('site.work') }}">Laravel &amp; Cloud APIs</a></li>
            <li><a href="{{ route('site.work.detail', 'nilepay') }}">FinTech &amp; Payment Ledgers</a></li>
            <li><a href="{{ route('site.work') }}">Headless E-Commerce</a></li>
            <li><a href="{{ route('site.work') }}">AI &amp; Workflow Agents</a></li>
          </ul>
        </div>

        <div class="foot-grid__col">
          <h3 class="foot-col__title">Marketing</h3>
          <ul class="foot-col__list">
            <li><a href="{{ route('site.work') }}">Performance &amp; Paid Media</a></li>
            <li><a href="{{ route('site.blog') }}">Conversion Optimization</a></li>
            <li><a href="{{ route('site.work') }}">Brand Identity Systems</a></li>
            <li><a href="{{ route('site.blog') }}">Commercial Video &amp; Photo</a></li>
            <li><a href="{{ route('site.work') }}">Server-Side Attribution</a></li>
            <li><a href="{{ route('site.work') }}">SEO &amp; Content Engines</a></li>
          </ul>
        </div>

        <div class="foot-grid__newsletter">
          <h3 class="foot-col__title">Empetix Dispatch</h3>
          <p class="foot-grid__summary">
            Monthly engineering monographs, video episodes, and growth playbooks.
          </p>
          <form class="foot-sub-form" action="{{ route('site.contact') }}" method="get">
            <label for="foot-email-input" class="sr-only">Work email address</label>
            <input id="foot-email-input" type="email" class="foot-sub-form__input" required placeholder="Enter your work email..."/>
            <button type="submit" class="btn btn--accent btn--sm">Connect &rarr;</button>
          </form>

          <div class="foot-socials" aria-label="Social media channels">
            <a href="https://www.linkedin.com" class="foot-social-btn" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
              <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M3.5 2A1.5 1.5 0 1 0 3.5 5 1.5 1.5 0 0 0 3.5 2ZM2.2 6.2H4.8V14H2.2V6.2ZM6.4 6.2H8.9V7.3H9C9.4 6.6 10.3 6 11.6 6 14.2 6 14.7 7.7 14.7 10V14H12.1V10.4C12.1 9.5 12.1 8.4 10.8 8.4 9.5 8.4 9.3 9.4 9.3 10.4V14H6.4V6.2Z"/></svg>
            </a>
            <a href="https://www.github.com" class="foot-social-btn" target="_blank" rel="noopener noreferrer" aria-label="GitHub">
              <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 1.3A6.7 6.7 0 0 0 5.9 14.4C6.2 14.5 6.3 14.3 6.3 14.1V12.9C4.4 13.3 4 12.1 4 12.1 3.7 11.3 3.2 11.1 3.2 11.1 2.6 10.7 3.3 10.7 3.3 10.7 4 10.8 4.3 11.4 4.3 11.4 4.9 12.4 5.9 12.1 6.3 11.9 6.4 11.5 6.6 11.2 6.8 11 5.3 10.8 3.7 10.3 3.7 7.7 3.7 7 4 6.4 4.4 5.9 4.3 5.7 4.1 5 4.5 4.1 4.5 4.1 5 3.9 6.3 4.8A6.5 6.5 0 0 1 9.7 4.8C11 3.9 11.5 4.1 11.5 4.1 11.9 5 11.7 5.7 11.6 5.9 12 6.4 12.3 7 12.3 7.7 12.3 10.3 10.7 10.8 9.2 11 9.5 11.2 9.7 11.7 9.7 12.4V14.1C9.7 14.3 9.8 14.5 10.1 14.4A6.7 6.7 0 0 0 8 1.3Z"/></svg>
            </a>
            <a href="https://www.youtube.com" class="foot-social-btn" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
              <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M14.7 4.8A1.8 1.8 0 0 0 13.4 3.5C12.3 3.2 8 3.2 8 3.2S3.7 3.2 2.6 3.5A1.8 1.8 0 0 0 1.3 4.8C1 5.9 1 8 1 8S1 10.1 1.3 11.2A1.8 1.8 0 0 0 2.6 12.5C3.7 12.8 8 12.8 8 12.8S12.3 12.8 13.4 12.5A1.8 1.8 0 0 0 14.7 11.2C15 10.1 15 8 15 8S15 5.9 14.7 4.8ZM6.6 10.1V5.9L10.3 8L6.6 10.1Z"/></svg>
            </a>
          </div>
        </div>
      </div>

      <!-- Legal & Copyright -->
      <div class="foot-bottom">
        <p>&copy; {{ date('Y') }} Empetix Solutions LLC. All rights reserved.</p>
        <ul class="foot-bottom__links" aria-label="Legal links">
          <li><a href="{{ route('site.contact') }}">Privacy Policy</a></li>
          <li><a href="{{ route('site.contact') }}">Terms of Service</a></li>
          <li><a href="{{ route('admin.dashboard') }}">Super Admin</a></li>
          <li><a href="#main-content">Back to Top &uarr;</a></li>
        </ul>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script defer src="{{ asset('assets/js/modules/nav.js') }}"></script>
  <script defer src="{{ asset('assets/js/modules/reveal.js') }}"></script>
  <script defer src="{{ asset('assets/js/modules/counter.js') }}"></script>
  <script defer src="{{ asset('assets/js/modules/media.js') }}"></script>
  <script defer src="{{ asset('assets/js/modules/careers.js') }}"></script>
  <script type="module" src="{{ asset('assets/js/main.js') }}"></script>

  @livewireScripts
  {{ $scripts ?? '' }}
</body>
</html>