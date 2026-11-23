<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  @yield('meta')
  @yield('head_extra')


  <link rel="preload" as="font" type="font/woff2" href="{{ asset('assets/fonts/archivo-700.woff2') }}" crossorigin/>
  <link rel="preload" as="font" type="font/woff2" href="{{ asset('assets/fonts/public-sans-400.woff2') }}" crossorigin/>

  <link rel="icon" type="image/svg+xml" href="{{ asset('assets/img/favicon.svg') }}"/>
  <link rel="preload" as="image" href="{{ asset('assets/img/logo/empetix-logo.png') }}" fetchpriority="high"/>
  <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}"/>
  <script src="{{ asset('assets/js/preloader-gate.js') }}"></script>
  <noscript>
    <link rel="stylesheet" href="{{ asset('assets/css/noscript.css') }}"/>
  </noscript>

</head>
<body>

<!-- PARTIAL: preloader -->
<div
  id="preloader"
  class="preloader"
  role="status"
  aria-label="Loading Empetix Solution"
>
  <span id="preloader-sr" class="preloader__sr" aria-live="polite">Loading 0%</span>

  <div class="preloader__stage">
    <div class="preloader__logo-stack">
      <!-- 1. Dim Grayscale Base Silhouette -->
      <img
        class="preloader__logo preloader__logo--base"
        src="{{ asset('assets/img/logo/empetix-logo.png') }}"
        alt="Empetix Solution"
        width="881"
        height="302"
        fetchpriority="high"
        decoding="sync"
      />

      <!-- 2. Precision 12-Piece Letter & Equalizer Assembly of the Official 881x302 Logo -->
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

      <!-- 3. Thin 2px Accent Scan Line following --p -->
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


<!-- PARTIAL: header -->
<a href="#main-content" class="skip-link">Skip to main content</a>

<div id="site-head" class="site-head">
  <!-- Row 1: Top Utility Bar (Links on the Left, Search Button Only on the Right) -->
  <div class="utility" role="region" aria-label="Secondary navigation">
    <div class="utility__inner">
      <ul class="utility__links">
        <li class="utility__item"><a href="{{ url('/careers') }}"@if(($activePage ?? '') === 'careers') aria-current="page"@endif class="utility__link">Careers</a></li>
        <li class="utility__item"><a href="{{ url('/work') }}"@if(($activePage ?? '') === 'work') aria-current="page"@endif class="utility__link">Projects</a></li>
        <li class="utility__item"><a href="{{ url('/partners') }}"@if(($activePage ?? '') === 'partners') aria-current="page"@endif class="utility__link">Partners</a></li>
        <li class="utility__item"><a href="{{ url('/team') }}"@if(($activePage ?? '') === 'team') aria-current="page"@endif class="utility__link">Team</a></li>
        <li class="utility__item">
          <a href="{{ url('/blog') }}"@if(($activePage ?? '') === 'blog') aria-current="page"@endif class="utility__link" data-mega-trigger="journal" aria-expanded="false" aria-controls="mega-journal">
            <span>Blog</span>
          </a>
        </li>
        <li class="utility__item"><a href="{{ url('/contact') }}"@if(($activePage ?? '') === 'contact') aria-current="page"@endif class="utility__link">Contact Us</a></li>
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
      <form class="search-panel__form" action="blog.html" method="get">
        <label for="search-input" class="sr-only">Search projects, blog posts, and roles</label>
        <input id="search-input" type="search" name="q" class="search-panel__input" placeholder="Search solutions, case studies, engineering essays, video episodes..." autocomplete="off"/>
        <button type="submit" class="btn btn--accent btn--sm">Search</button>
        <button type="button" id="search-close" class="btn btn--ghost btn--sm">Close</button>
      </form>
      <div class="search-panel__hints">
        <span>Quick jump:</span>
        <a href="{{ url('/work/nilepay') }}">NilePay Case Study</a>
        <a href="{{ url('/blog/video') }}">5-Day Sprint Video</a>
        <a href="{{ url('/blog/photos') }}">Qahwa Photo Essay</a>
        <a href="{{ url('/about') }}#founders">Meet the Founders</a>
        <a href="{{ url('/contact') }}"@if(($activePage ?? '') === 'contact') aria-current="page"@endif>Contact Us</a>
      </div>
    </div>
  </div>

  <!-- Row 2: Gridded Main Navigation Bar -->
  <header class="nav-bar">
    <div class="nav-bar__inner">
      <a href="{{ url('/') }}"@if(($activePage ?? '') === 'home') aria-current="page"@endif class="brand" aria-label="Empetix Solution Home">
        <img
          class="brand__logo-img"
          src="{{ asset('assets/img/logo/empetix-logo-dark.png') }}"
          alt="Empetix Solution"
          width="881"
          height="302"
        />
      </a>

      <nav class="nav" aria-label="Primary navigation">
        <ul class="nav__list">
          <li class="nav__item">
            <a href="{{ url('/') }}#solutions-hub" class="nav__link" data-mega-trigger="capabilities" aria-expanded="false" aria-controls="mega-capabilities">
              <span>Capabilities &amp; Solutions</span>
              <svg class="nav__chevron" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </a>
          </li>
          <li class="nav__item">
            <a href="{{ url('/work') }}"@if(($activePage ?? '') === 'work') aria-current="page"@endif class="nav__link">
              <span>Projects &amp; Platforms</span>
            </a>
          </li>
          <li class="nav__item">
            <a href="{{ url('/blog') }}"@if(($activePage ?? '') === 'blog') aria-current="page"@endif class="nav__link" data-mega-trigger="journal" aria-expanded="false" aria-controls="mega-journal">
              <span>Blogs &amp; Media</span>
              <svg class="nav__chevron" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </a>
          </li>
          <li class="nav__item">
            <a href="{{ url('/partners') }}"@if(($activePage ?? '') === 'partners') aria-current="page"@endif class="nav__link">
              <span>Partners &amp; Industries</span>
            </a>
          </li>
          <li class="nav__item">
            <a href="{{ url('/about') }}"@if(($activePage ?? '') === 'about') aria-current="page"@endif class="nav__link">
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

  <!-- PARTIAL: mega-menu -->
<div id="mega-menu" class="mega" role="region" aria-label="Expanded studio directory">
  <!-- Panel 1: Capabilities (Software + Marketing) -->
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
        <a href="{{ url('/work') }}"@if(($activePage ?? '') === 'work') aria-current="page"@endif class="arrow-link">
          <span>Explore All Case Studies</span>
          <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
          </svg>
        </a>
      </div>

      <div class="mega__col">
        <p class="mega__col-title">Software Engineering</p>
        <ul>
          <li>
            <a href="{{ url('/work') }}"@if(($activePage ?? '') === 'work') aria-current="page"@endif class="mega__link">
              <span class="mega__link-title">
                <span>Web &amp; Mobile Platforms</span>
                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
              </span>
              <span class="mega__link-sub">Laravel, Next.js, Flutter &amp; high-concurrency APIs</span>
            </a>
          </li>
          <li>
            <a href="{{ url('/work') }}"@if(($activePage ?? '') === 'work') aria-current="page"@endif class="mega__link">
              <span class="mega__link-title">
                <span>E-Commerce &amp; FinTech Core</span>
                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
              </span>
              <span class="mega__link-sub">Custom checkouts, wallets, ERP &amp; payment gateways</span>
            </a>
          </li>
          <li>
            <a href="{{ url('/work') }}"@if(($activePage ?? '') === 'work') aria-current="page"@endif class="mega__link">
              <span class="mega__link-title">
                <span>AI Automation &amp; Data Pipelines</span>
                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
              </span>
              <span class="mega__link-sub">Internal copilots, CRM workflows &amp; BI telemetry</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="mega__col">
        <p class="mega__col-title">Marketing &amp; Growth</p>
        <ul>
          <li>
            <a href="{{ url('/work') }}"@if(($activePage ?? '') === 'work') aria-current="page"@endif class="mega__link">
              <span class="mega__link-title">
                <span>Performance &amp; Paid Acquisition</span>
                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
              </span>
              <span class="mega__link-sub">Full-funnel ROAS across Meta, Google, TikTok &amp; LinkedIn</span>
            </a>
          </li>
          <li>
            <a href="{{ url('/blog/photos') }}" class="mega__link">
              <span class="mega__link-title">
                <span>Brand Systems &amp; Campaign Production</span>
                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
              </span>
              <span class="mega__link-sub">Visual identity, commercial video shoots &amp; art direction</span>
            </a>
          </li>
          <li>
            <a href="{{ url('/blog/post') }}" class="mega__link">
              <span class="mega__link-title">
                <span>CRO, SEO &amp; Lifecycle Retention</span>
                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
              </span>
              <span class="mega__link-sub">Arabic/English technical SEO, A/B testing &amp; CRM flows</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="mega__col">
        <p class="mega__col-title">Featured Case Study</p>
        <a href="{{ url('/work/nilepay') }}" class="mega__feature">
          <div class="frame frame--16x9 frame--zoom">
            <img src="{{ asset('assets/img/work/nilepay.jpg') }}" alt="Woman paying with smartphone at a sunlit Cairo cafe counter" width="640" height="360"/>
          </div>
          <span class="t-meta">NILEPAY FINTECH &middot; +310% ACTIVATION</span>
          <p class="mega__feature-title">Rebuilding mobile checkout and launch acquisition for 1.4M users.</p>
        </a>
      </div>
    </div>
  </div>

  <!-- Panel 2: Journal / Blogs Preview -->
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
        <a href="{{ url('/blog') }}"@if(($activePage ?? '') === 'blog') aria-current="page"@endif class="arrow-link">
          <span>Open Full Blog Archive</span>
          <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
          </svg>
        </a>
      </div>

      <div class="mega__col">
        <p class="mega__col-title">Full Article + Media</p>
        <a href="{{ url('/blog/post') }}" class="mega__feature">
          <div class="frame frame--3x2 frame--zoom">
            <img src="{{ asset('assets/img/blog/arabic-first.jpg') }}" alt="Designer sketching Arabic RTL interface layouts on paper" width="600" height="400"/>
          </div>
          <span class="t-meta">DESIGN &amp; CRO &middot; 8 MIN READ</span>
          <p class="mega__feature-title">Engineering Arabic-First Design Systems &amp; High-Conversion Funnels</p>
        </a>
      </div>

      <div class="mega__col">
        <p class="mega__col-title">Video Episode</p>
        <a href="{{ url('/blog/video') }}" class="mega__feature">
          <div class="frame frame--3x2 frame--zoom">
            <img src="{{ asset('assets/img/blog/design-sprint.jpg') }}" alt="Team voting on product sprint sketches on studio wall" width="600" height="400"/>
          </div>
          <span class="t-meta">VIDEO + TRANSCRIPT &middot; 14:20</span>
          <p class="mega__feature-title">Inside an Empetix 5-Day Product &amp; Growth Sprint</p>
        </a>
      </div>

      <div class="mega__col">
        <p class="mega__col-title">Photo Essay</p>
        <a href="{{ url('/blog/photos') }}" class="mega__feature">
          <div class="frame frame--3x2 frame--zoom">
            <img src="{{ asset('assets/img/blog/behind-the-lens.jpg') }}" alt="Behind the scenes of a commercial specialty coffee shoot" width="600" height="400"/>
          </div>
          <span class="t-meta">PHOTO GALLERY + VIDEO &middot; CAMPAIGN</span>
          <p class="mega__feature-title">Behind the Lens: Directing the Qahwa Roasters Launch Shoot</p>
        </a>
      </div>
    </div>
  </div>
</div>
<!-- END PARTIAL: mega-menu -->
</div>

<!-- Mobile Drawer -->
<aside id="mobile-drawer" class="drawer" aria-label="Mobile navigation" aria-hidden="true">
  <div class="container drawer__head">
    <a href="{{ url('/') }}"@if(($activePage ?? '') === 'home') aria-current="page"@endif class="brand">
      <span class="brand__word">Empetix</span>
    </a>
    <button type="button" id="drawer-close" class="drawer__close">Close</button>
  </div>
  <div class="container drawer__body">
    <ul class="drawer__list">
      <li><a href="{{ url('/') }}"@if(($activePage ?? '') === 'home') aria-current="page"@endif class="drawer__link">Home</a></li>
      <li><a href="{{ url('/work') }}"@if(($activePage ?? '') === 'work') aria-current="page"@endif class="drawer__link">Projects &amp; Platforms</a></li>
      <li><a href="{{ url('/about') }}"@if(($activePage ?? '') === 'about') aria-current="page"@endif class="drawer__link">About &amp; Founders</a></li>
      <li><a href="{{ url('/team') }}"@if(($activePage ?? '') === 'team') aria-current="page"@endif class="drawer__link">Our Team</a></li>
      <li><a href="{{ url('/partners') }}"@if(($activePage ?? '') === 'partners') aria-current="page"@endif class="drawer__link">Partners &amp; Industries</a></li>
      <li><a href="{{ url('/blog') }}"@if(($activePage ?? '') === 'blog') aria-current="page"@endif class="drawer__link">Blogs &amp; Media</a></li>
      <li><a href="{{ url('/careers') }}"@if(($activePage ?? '') === 'careers') aria-current="page"@endif class="drawer__link">Careers</a></li>
      <li><a href="{{ url('/contact') }}"@if(($activePage ?? '') === 'contact') aria-current="page"@endif class="drawer__link">Contact Us</a></li>
    </ul>

    <div class="drawer__meta">
      <p>New Business: hello&#64;empetix.com</p>
      <p>Careers: talent&#64;empetix.com</p>
      <p>New Cairo, Egypt</p>
    </div>
  </div>
</aside>


  <main id="main-content">
@yield('content')
  </main>

<!-- PARTIAL: footer -->
<footer class="site-foot" aria-label="Site footer">
  <div class="container">
    <!-- 1. Top Framed Executive CTA Banner -->
    <div class="foot-banner">
      <div>
        <h2 class="foot-banner__title">Tell us about the product you want to build.</h2>
        <p class="foot-banner__sub">
          Speak directly with our co-founders and engineering leads. We respond to all project briefs within 24 business hours.
        </p>
      </div>
      <div class="foot-banner__actions">
        <a href="{{ url('/contact') }}" class="btn btn--accent btn--lg">
          <span>Start a Project</span>
          <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
          </svg>
        </a>
        <a href="{{ url('/careers') }}" class="btn btn--ghost btn--lg">
          <span>Careers Portal</span>
        </a>
      </div>
    </div>

    <!-- 2. Main 12-Column Sitemap & Single Headquarters Grid -->
    <div class="foot-grid">
      <!-- Col 1 (4 cols): Brand + Single Headquarters Contact -->
      <div class="foot-grid__brand">
        <a href="{{ url('/') }}" class="brand" aria-label="Empetix Solution Home">
          <img
            class="brand__logo-img"
            src="{{ asset('assets/img/logo/empetix-logo.png') }}"
            alt="Empetix Solution"
            width="881"
            height="302"
          />
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
            <span>Building 14, District 5 Campus, New Cairo, Cairo, Egypt</span>
          </div>
          <div class="foot-hq__item">
            <svg class="foot-hq__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <path d="M2.5 4H13.5V12H2.5V4Z" stroke="currentColor" stroke-width="1.4"/>
              <path d="M2.5 4.5L8 8.5L13.5 4.5" stroke="currentColor" stroke-width="1.4"/>
            </svg>
            <a href="mailto:hello&#64;empetix.com">hello&#64;empetix.com</a>
          </div>
          <div class="foot-hq__item">
            <svg class="foot-hq__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <path d="M3.5 2.5H6.5L8 6L6 7.5C6.8 9.2 8.3 10.7 10 11.5L11.5 9.5L15 11V14C15 14.5 14.5 15 13.8 15C7.2 14.6 2.9 10.3 2.5 3.7C2.5 3 3 2.5 3.5 2.5Z" stroke="currentColor" stroke-width="1.4"/>
            </svg>
            <a href="tel:+20227928400">+20 2 2792 8400</a>
          </div>
        </div>
      </div>

      <!-- Col 2 (2 cols): Company -->
      <div class="foot-grid__col">
        <h3 class="foot-col__title">Company</h3>
        <ul class="foot-col__list">
          <li><a href="{{ url('/') }}">Home</a></li>
          <li><a href="{{ url('/about') }}">About &amp; Founders</a></li>
          <li><a href="{{ url('/team') }}">Our Team</a></li>
          <li><a href="{{ url('/work') }}">Selected Projects</a></li>
          <li><a href="{{ url('/partners') }}">Corporate Partners</a></li>
          <li><a href="{{ url('/blog') }}">Blogs &amp; Media</a></li>
          <li><a href="{{ url('/careers') }}">Careers (jobs.empetix)</a></li>
          <li><a href="{{ url('/contact') }}">Contact Us</a></li>
        </ul>
      </div>

      <!-- Col 3 (2 cols): Software Solutions -->
      <div class="foot-grid__col">
        <h3 class="foot-col__title">Software</h3>
        <ul class="foot-col__list">
          <li><a href="{{ url('/work') }}">Web &amp; SaaS Platforms</a></li>
          <li><a href="{{ url('/work') }}">Mobile Apps (Flutter)</a></li>
          <li><a href="{{ url('/work') }}">Laravel &amp; Cloud APIs</a></li>
          <li><a href="{{ url('/work/nilepay') }}">FinTech &amp; Payment Ledgers</a></li>
          <li><a href="{{ url('/work') }}">Headless E-Commerce</a></li>
          <li><a href="{{ url('/work') }}">AI &amp; Workflow Agents</a></li>
        </ul>
      </div>

      <!-- Col 4 (2 cols): Growth & Marketing -->
      <div class="foot-grid__col">
        <h3 class="foot-col__title">Marketing</h3>
        <ul class="foot-col__list">
          <li><a href="{{ url('/work') }}">Performance &amp; Paid Media</a></li>
          <li><a href="{{ url('/blog/post') }}">Conversion Optimization</a></li>
          <li><a href="{{ url('/work') }}">Brand Identity Systems</a></li>
          <li><a href="{{ url('/blog/photos') }}">Commercial Video &amp; Photo</a></li>
          <li><a href="{{ url('/work') }}">Server-Side Attribution</a></li>
          <li><a href="{{ url('/work') }}">SEO &amp; Content Engines</a></li>
        </ul>
      </div>

      <!-- Col 5 (2 cols): Newsletter & Social Connect -->
      <div class="foot-grid__newsletter">
        <h3 class="foot-col__title">Insights Dispatch</h3>
        <p class="foot-grid__summary">
          Monthly engineering monographs, video episodes, and growth playbooks.
        </p>
        <form class="foot-sub-form" action="{{ route('subscribe') }}" method="post" data-interactive-form data-success-msg="Subscribed to Empetix Solutions Insights.">
            @csrf
          <label for="foot-email-input" class="sr-only">Work email address</label>
          <input id="foot-email-input" type="email" class="foot-sub-form__input" required placeholder="Enter your work email..."/>
          <button type="submit" class="btn btn--accent btn--sm">Subscribe &rarr;</button>
          <div class="form__status" data-form-status role="status" aria-live="polite"></div>
        @if(session('subscribed'))
              <p role="status" style="color:#1a7f4b;font-weight:600">{{ session('subscribed') }}</p>
            @endif
          </form>

        <div class="foot-socials" aria-label="Social media channels">
          <a href="https://www.linkedin.com" class="foot-social-btn" rel="noopener noreferrer" aria-label="LinkedIn">
            <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M3.5 2A1.5 1.5 0 1 0 3.5 5 1.5 1.5 0 0 0 3.5 2ZM2.2 6.2H4.8V14H2.2V6.2ZM6.4 6.2H8.9V7.3H9C9.4 6.6 10.3 6 11.6 6 14.2 6 14.7 7.7 14.7 10V14H12.1V10.4C12.1 9.5 12.1 8.4 10.8 8.4 9.5 8.4 9.3 9.4 9.3 10.4V14H6.4V6.2Z"/></svg>
          </a>
          <a href="https://www.github.com" class="foot-social-btn" rel="noopener noreferrer" aria-label="GitHub">
            <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 1.3A6.7 6.7 0 0 0 5.9 14.4C6.2 14.5 6.3 14.3 6.3 14.1V12.9C4.4 13.3 4 12.1 4 12.1 3.7 11.3 3.2 11.1 3.2 11.1 2.6 10.7 3.3 10.7 3.3 10.7 4 10.8 4.3 11.4 4.3 11.4 4.9 12.4 5.9 12.1 6.3 11.9 6.4 11.5 6.6 11.2 6.8 11 5.3 10.8 3.7 10.3 3.7 7.7 3.7 7 4 6.4 4.4 5.9 4.3 5.7 4.1 5 4.5 4.1 4.5 4.1 5 3.9 6.3 4.8A6.5 6.5 0 0 1 9.7 4.8C11 3.9 11.5 4.1 11.5 4.1 11.9 5 11.7 5.7 11.6 5.9 12 6.4 12.3 7 12.3 7.7 12.3 10.3 10.7 10.8 9.2 11 9.5 11.2 9.7 11.7 9.7 12.4V14.1C9.7 14.3 9.8 14.5 10.1 14.4A6.7 6.7 0 0 0 8 1.3Z"/></svg>
          </a>
          <a href="https://www.youtube.com" class="foot-social-btn" rel="noopener noreferrer" aria-label="YouTube">
            <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M14.7 4.8A1.8 1.8 0 0 0 13.4 3.5C12.3 3.2 8 3.2 8 3.2S3.7 3.2 2.6 3.5A1.8 1.8 0 0 0 1.3 4.8C1 5.9 1 8 1 8S1 10.1 1.3 11.2A1.8 1.8 0 0 0 2.6 12.5C3.7 12.8 8 12.8 8 12.8S12.3 12.8 13.4 12.5A1.8 1.8 0 0 0 14.7 11.2C15 10.1 15 8 15 8S15 5.9 14.7 4.8ZM6.6 10.1V5.9L10.3 8L6.6 10.1Z"/></svg>
          </a>
          <a href="https://www.instagram.com" class="foot-social-btn" rel="noopener noreferrer" aria-label="Instagram">
            <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 3.4C9.5 3.4 9.7 3.4 10.3 3.4 11.8 3.5 12.5 4.2 12.6 5.7 12.6 6.3 12.6 6.5 12.6 8 12.6 9.5 12.6 9.7 12.6 10.3 12.5 11.8 11.8 12.5 10.3 12.6 9.7 12.6 9.5 12.6 8 12.6 6.5 12.6 6.3 12.6 5.7 12.6 4.2 12.5 3.5 11.8 3.4 10.3 3.4 9.7 3.4 9.5 3.4 8 3.4 6.5 3.4 6.3 3.4 5.7 3.5 4.2 4.2 3.5 5.7 3.4 6.3 3.4 6.5 3.4 8 3.4ZM8 2C6.4 2 6.2 2 5.6 2 3.5 2.1 2.1 3.5 2 5.6 2 6.2 2 6.4 2 8 2 9.6 2 9.8 2 10.4 2.1 12.5 3.5 13.9 5.6 14 6.2 14 6.4 14 8 14 9.6 14 9.8 14 10.4 14 12.5 13.9 13.9 12.5 14 10.4 14 9.8 14 9.6 14 8 14 6.4 14 6.2 14 5.6 13.9 3.5 12.5 2.1 10.4 2 9.8 2 9.6 2 8 2ZM8 4.9A3.1 3.1 0 1 0 8 11.1 3.1 3.1 0 0 0 8 4.9ZM8 10A2 2 0 1 1 8 6 2 2 0 0 1 8 10ZM11.2 4.1A0.7 0.7 0 1 0 11.2 5.5 0.7 0.7 0 0 0 11.2 4.1Z"/></svg>
          </a>
        </div>
      </div>
    </div>

    <!-- 3. Bottom Legal & Copyright Bar -->
    <div class="foot-bottom">
      <p>&copy; 2026 Empetix Solutions LLC. All rights reserved.</p>
      <ul class="foot-bottom__links" aria-label="Legal links">
        <li><a href="{{ url('/contact') }}">Privacy Policy</a></li>
        <li><a href="{{ url('/contact') }}">Terms of Service</a></li>
        <li><a href="{{ url('/contact') }}">Cookie Settings</a></li>
        <li><a href="#main-content">Back to Top &uarr;</a></li>
      </ul>
    </div>
  </div>
</footer>



@yield('scripts')
</body>
</html>
