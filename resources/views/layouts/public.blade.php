<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  @if(setting('seo_allow_indexing', '1') === '0')
    <meta name="robots" content="noindex, nofollow"/>
  @else
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1"/>
  @endif
  <meta name="author" content="Empetix Solutions | حلول إمبيتكس"/>
  <meta name="publisher" content="Empetix Solutions"/>
  <meta name="keywords" content="{{ seo_keywords() }}"/>
  <link rel="canonical" href="{{ url()->current() }}"/>
  <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ url('/sitemap.xml') }}"/>
  <link rel="alternate" hreflang="en" href="{{ url()->current() }}"/>
  <link rel="alternate" hreflang="ar" href="{{ url()->current() }}"/>
  <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}"/>

  @yield('meta')
  @yield('head_extra')

  {{-- Comprehensive Schema.org JSON-LD: Google Knowledge Graph & AI Models (ChatGPT, Perplexity, Claude, Gemini) --}}
  @include('partials.schema-org')


  <link rel="preload" as="font" type="font/woff2" href="{{ asset('assets/fonts/archivo-700.woff2') }}" crossorigin/>
  <link rel="preload" as="font" type="font/woff2" href="{{ asset('assets/fonts/public-sans-400.woff2') }}" crossorigin/>

  <link rel="icon" href="{{ brand_favicon() }}"/>
  <link rel="preload" as="image" href="{{ content_asset(sec('global', 'copy_c47b5facbcd3')) }}" fetchpriority="high"/>
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
  <span id="preloader-sr" class="preloader__sr" aria-live="polite">{{ sec('global', 'copy_9ae629abced1') }}</span>

  <div class="preloader__stage">
    <div class="preloader__logo-stack">
      <!-- 1. Dim Grayscale Base Silhouette -->
      <img
        class="preloader__logo preloader__logo--base"
        src="{{ brand_logo() }}"
        alt="{{ sec('global', 'copy_4fa930d35d88') }}"
        width="881"
        height="302"
        fetchpriority="high"
        decoding="sync"
      />

      <!-- 2. Precision 12-Piece Letter & Equalizer Assembly of the Official 881x302 Logo -->
      <div class="preloader__pieces" aria-hidden="true">
        <img class="preloader__piece preloader__piece--e1" src="{{ brand_logo() }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
        <img class="preloader__piece preloader__piece--m1" src="{{ brand_logo() }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
        <img class="preloader__piece preloader__piece--m2" src="{{ brand_logo() }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
        <img class="preloader__piece preloader__piece--m3" src="{{ brand_logo() }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
        <img class="preloader__piece preloader__piece--m4" src="{{ brand_logo() }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
        <img class="preloader__piece preloader__piece--m5" src="{{ brand_logo() }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
        <img class="preloader__piece preloader__piece--p" src="{{ brand_logo() }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
        <img class="preloader__piece preloader__piece--e2" src="{{ brand_logo() }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
        <img class="preloader__piece preloader__piece--t" src="{{ brand_logo() }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
        <img class="preloader__piece preloader__piece--i" src="{{ brand_logo() }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
        <img class="preloader__piece preloader__piece--x" src="{{ brand_logo() }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
        <img class="preloader__piece preloader__piece--solution" src="{{ brand_logo() }}" alt="" width="881" height="302" fetchpriority="high" decoding="sync"/>
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
<a href="#main-content" class="skip-link">{{ sec('global', 'copy_b9b90b76f6f1') }}</a>

<div id="site-head" class="site-head">
  <!-- Row 1: Top Utility Bar (Links on the Left, Search Button Only on the Right) -->
  <div class="utility" role="region" aria-label="Secondary navigation">
    <div class="utility__inner">
      <ul class="utility__links">
        {{-- Dashboard-editable links (Navigation → Utility bar) with resilient editorial fallback --}}
        @php($utilityNav = nav_items('utility'))
        @if($utilityNav->isNotEmpty())
          @foreach($utilityNav as $navItem)
            <li class="utility__item"><a href="{{ str_starts_with($navItem->url, 'http') ? $navItem->url : url($navItem->url) }}" class="utility__link"@if($navItem->label === 'Blog') data-mega-trigger="journal" aria-expanded="false" aria-controls="mega-journal"@endif>{{ $navItem->label }}</a></li>
          @endforeach
        @else
          <li class="utility__item"><a href="{{ url('/careers') }}" class="utility__link">Careers</a></li>
          <li class="utility__item"><a href="{{ url('/work') }}" class="utility__link">Projects</a></li>
          <li class="utility__item"><a href="{{ url('/team') }}" class="utility__link">Team</a></li>
          <li class="utility__item"><a href="{{ url('/blog') }}" class="utility__link" data-mega-trigger="journal" aria-expanded="false" aria-controls="mega-journal">Blog & Insights</a></li>
          <li class="utility__item"><a href="{{ url('/contact') }}" class="utility__link">Contact</a></li>
        @endif
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
      <form class="search-panel__form" action="{{ route('blog') }}" method="get">
        <label for="search-input" class="sr-only">{{ sec('global', 'copy_4de56f097187') }}</label>
        <input id="search-input" type="search" name="q" class="search-panel__input" placeholder="{{ sec('footer', 'search_placeholder', 'Search solutions, case studies, engineering essays, video episodes...') }}" autocomplete="off"/>
        <button type="submit" class="btn btn--accent btn--sm">{{ sec('global', 'copy_3e9b94d3a503') }}</button>
        <button type="button" id="search-close" class="btn btn--ghost btn--sm">{{ sec('global', 'copy_7fde00f1ef77') }}</button>
      </form>
      <div class="search-panel__hints">
        <span>{{ sec('footer', 'search_quickjump', 'Quick jump:') }}</span>
        <a href="{{ url('/work/alfagn') }}">{{ sec('global', 'copy_99681c08acc5') }}</a>
        <a href="{{ url('/blog/five-day-product-growth-sprint') }}">{{ sec('global', 'copy_9bd3315c2cda') }}</a>
        <a href="{{ url('/blog/behind-the-lens-qahwa-launch') }}">{{ sec('global', 'copy_c845f21a3cfc') }}</a>
        <a href="{{ url('/about') }}#founders">{{ sec('global', 'copy_56466eb90fcf') }}</a>
        <a href="{{ url('/contact') }}"@if(($activePage ?? '') === 'contact') aria-current="page"@endif>{{ sec('global', 'copy_f4b9a5c91fb5') }}</a>
      </div>
    </div>
  </div>

  <!-- Row 2: Gridded Main Navigation Bar -->
  <header class="nav-bar">
    <div class="nav-bar__inner">
      <a href="{{ url('/') }}"@if(($activePage ?? '') === 'home') aria-current="page"@endif class="brand" aria-label="Empetix Solution Home">
        <img
          class="brand__logo-img"
          src="{{ brand_logo(dark: true) }}"
          alt="{{ sec('global', 'copy_4fa930d35d88') }}"
          width="881"
          height="302"
        />
      </a>

      <nav class="nav" aria-label="Primary navigation">
        <ul class="nav__list">
          <li class="nav__item">
            <a href="{{ url('/') }}#solutions-hub" class="nav__link" data-mega-trigger="capabilities" aria-expanded="false" aria-controls="mega-capabilities">
              <span>{{ sec('global', 'copy_edddda7990f5') }}</span>
              <svg class="nav__chevron" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </a>
          </li>
          <li class="nav__item">
            <a href="{{ url('/work') }}"@if(($activePage ?? '') === 'work') aria-current="page"@endif class="nav__link">
              <span>{{ sec('global', 'copy_5faa9a4a6ae2') }}</span>
            </a>
          </li>
          <li class="nav__item">
            <a href="{{ url('/blog') }}"@if(($activePage ?? '') === 'blog') aria-current="page"@endif class="nav__link" data-mega-trigger="journal" aria-expanded="false" aria-controls="mega-journal">
              <span>{{ sec('global', 'copy_565b27a38e2b') }}</span>
              <svg class="nav__chevron" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </a>
          </li>
          <li class="nav__item">
            <a href="{{ url('/partners') }}"@if(($activePage ?? '') === 'partners') aria-current="page"@endif class="nav__link">
              <span>{{ sec('global', 'copy_a2aef16268ef') }}</span>
            </a>
          </li>
          <li class="nav__item">
            <a href="{{ url('/about') }}"@if(($activePage ?? '') === 'about') aria-current="page"@endif class="nav__link">
              <span>{{ sec('global', 'copy_321facc49ee7') }}</span>
            </a>
          </li>
        </ul>
      </nav>

      <div class="nav-bar__actions">
        <a href="{{ url('/contact') }}" class="nav-bar__cta btn btn--accent btn--sm">
          <span>Start a Project</span>
          <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
          </svg>
        </a>
      </div>

      <div class="nav-bar__mobile-actions">
        <button type="button" class="mobile-search-toggle" data-search-toggle aria-expanded="false" aria-controls="search-panel" aria-label="Open site search">
          <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <circle cx="8.5" cy="8.5" r="6" stroke="currentColor" stroke-width="2"/>
            <path d="M13 13L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </button>
        <button type="button" id="menu-toggle" class="menu-toggle" aria-expanded="false" aria-controls="mobile-drawer">
          <span>{{ sec('global', 'copy_1f09c87bba77') }}</span>
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
          <span class="eyebrow">{{ sec('global', 'copy_ba60d8ed7f8e') }}</span>
          <h2 class="mega__title">{{ sec('mega', 'capabilities_title', 'One studio. Code and growth under one roof.') }}</h2>
          <p class="mega__desc">{{ sec('mega', 'capabilities_desc', 'We pair senior software engineers with performance marketers and commercial directors so products ship fast and scale profitably.') }}</p>
        </div>
        <a href="{{ url('/work') }}"@if(($activePage ?? '') === 'work') aria-current="page"@endif class="arrow-link">
          <span>{{ sec('global', 'copy_3127442311db') }}</span>
          <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
          </svg>
        </a>
      </div>

      @foreach(nav_mega('mega-capabilities') as $column)
        <div class="mega__col">
          <p class="mega__col-title">{{ $column->label }}</p>
          @if($column->children->contains(fn ($link) => $link->image))
            @foreach($column->children as $link)
              <a href="{{ str_starts_with($link->url, 'http') ? $link->url : url($link->url) }}" class="mega__feature">
                @if($link->image)
                  <div class="frame frame--16x9 frame--zoom">
                    <img src="{{ asset($link->image) }}" alt="{{ $link->label }}" width="640" height="360"/>
                  </div>
                @endif
                @if($link->meta)<span class="t-meta">{{ $link->meta }}</span>@endif
                <p class="mega__feature-title">{{ $link->label }}</p>
              </a>
            @endforeach
          @else
            <ul>
              @foreach($column->children as $link)
                <li>
                  <a href="{{ str_starts_with($link->url, 'http') ? $link->url : url($link->url) }}" class="mega__link">
                    <span class="mega__link-title">
                      <span>{{ $link->label }}</span>
                      <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
                    </span>
                    @if($link->sub)<span class="mega__link-sub">{{ $link->sub }}</span>@endif
                  </a>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
      @endforeach
</div>
  </div>

  <!-- Panel 2: Journal / Blogs Preview -->
  <div id="mega-journal" class="mega__panel" data-mega-panel="journal">
    <div class="container mega__grid">
      <div class="mega__intro">
        <div>
          <span class="eyebrow">{{ sec('global', 'copy_cfa25d342ff3') }}</span>
          <h2 class="mega__title">{{ sec('mega', 'journal_title', 'Articles, video episodes & photo essays.') }}</h2>
          <p class="mega__desc">{{ sec('mega', 'journal_desc', 'Field notes from our engineers, product designers, and performance marketers.') }}</p>
        </div>
        <a href="{{ url('/blog') }}"@if(($activePage ?? '') === 'blog') aria-current="page"@endif class="arrow-link">
          <span>{{ sec('global', 'copy_ce0f8021330a') }}</span>
          <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
          </svg>
        </a>
      </div>

      @foreach(nav_mega('mega-journal') as $column)
        <div class="mega__col">
          <p class="mega__col-title">{{ $column->label }}</p>
          @foreach($column->children as $link)
            <a href="{{ str_starts_with($link->url, 'http') ? $link->url : url($link->url) }}" class="mega__feature">
              @if($link->image)
                <div class="frame frame--3x2 frame--zoom">
                  <img src="{{ asset($link->image) }}" alt="{{ $link->label }}" width="600" height="400"/>
                </div>
              @endif
              @if($link->meta)<span class="t-meta">{{ $link->meta }}</span>@endif
              <p class="mega__feature-title">{{ $link->label }}</p>
            </a>
          @endforeach
        </div>
      @endforeach
    </div>
  </div>
</div>
<!-- END PARTIAL: mega-menu -->
</div>

<!-- Mobile Drawer -->
<aside id="mobile-drawer" class="drawer" aria-label="Mobile navigation" aria-hidden="true">
  <div class="container drawer__head">
    <a href="{{ url('/') }}"@if(($activePage ?? '') === 'home') aria-current="page"@endif class="brand">
      <span class="brand__word">{{ sec('global', 'copy_f3cca2387d54') }}</span>
    </a>
    <button type="button" id="drawer-close" class="drawer__close">{{ sec('global', 'copy_7fde00f1ef77') }}</button>
  </div>
  <div class="container drawer__body">
    <ul class="drawer__list">
      <li><a href="{{ url('/') }}"@if(($activePage ?? '') === 'home') aria-current="page"@endif class="drawer__link">{{ sec('global', 'copy_66b93f927675') }}</a></li>
      {{-- Dashboard-editable links (Navigation → Mobile drawer). --}}
      @foreach(nav_items('drawer') as $navItem)
        <li><a href="{{ str_starts_with($navItem->url, 'http') ? $navItem->url : url($navItem->url) }}" class="drawer__link">{{ $navItem->label }}</a></li>
      @endforeach
    </ul>

    <div class="drawer__meta">
      <p>{{ sec('global', 'copy_a16ec9b1a9dc') }}</p>
      <p>{{ sec('global', 'copy_fe641bf80e36') }}</p>
      <p>{{ sec('global', 'copy_99171044a562') }}</p>
    </div>
  </div>
</aside>


  <main id="main-content" @class(['team-page' => ($activePage ?? '') === 'team'])>
@yield('content')
  </main>

<!-- PARTIAL: footer -->
<footer class="site-foot" aria-label="Site footer">
  <div class="container">
    <!-- 1. Top Framed Executive CTA Banner -->
    <div class="foot-banner">
      <div>
        <h2 class="foot-banner__title">{{ sec('footer', 'banner_title', 'Tell us about the product you want to build.') }}</h2>
        <p class="foot-banner__sub">
          {{ sec('footer', 'banner_sub', 'Speak directly with our co-founders and engineering leads. We respond to all project briefs within 24 business hours.') }}
        </p>
      </div>
      <div class="foot-banner__actions">
        <a href="{{ url('/contact') }}" class="btn btn--accent btn--lg">
          <span>{{ sec('global', 'copy_0a2e9effe060') }}</span>
          <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
          </svg>
        </a>
        <a href="{{ url('/careers') }}" class="btn btn--ghost btn--lg">
          <span>{{ sec('global', 'copy_af45d288e6ae') }}</span>
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
            src="{{ brand_logo() }}"
            alt="{{ sec('global', 'copy_4fa930d35d88') }}"
            width="881"
            height="302"
          />
        </a>

        <p class="foot-grid__summary">
          {{ sec('footer', 'brand_summary', 'Empetix Solutions is a software engineering and performance marketing partner. We build enterprise web & mobile platforms and scale the brands behind them.') }}
        </p>

        <div class="foot-hq">
          <div class="foot-hq__item">
            <svg class="foot-hq__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <path d="M8 1.5C5.2 1.5 3 3.7 3 6.5C3 10.2 8 14.5 8 14.5C8 14.5 13 10.2 13 6.5C13 3.7 10.8 1.5 8 1.5Z" stroke="currentColor" stroke-width="1.4"/>
              <circle cx="8" cy="6.5" r="1.8" fill="currentColor"/>
            </svg>
            <span>{{ setting('address', 'Building 14, District 5 Campus, New Cairo, Cairo, Egypt') }}</span>
          </div>
          <div class="foot-hq__item">
            <svg class="foot-hq__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <path d="M2.5 4H13.5V12H2.5V4Z" stroke="currentColor" stroke-width="1.4"/>
              <path d="M2.5 4.5L8 8.5L13.5 4.5" stroke="currentColor" stroke-width="1.4"/>
            </svg>
            <a href="mailto:{{ setting('email', 'hello@empetix.com') }}">{{ setting('email', 'hello@empetix.com') }}</a>
          </div>
          <div class="foot-hq__item">
            <svg class="foot-hq__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <path d="M3.5 2.5H6.5L8 6L6 7.5C6.8 9.2 8.3 10.7 10 11.5L11.5 9.5L15 11V14C15 14.5 14.5 15 13.8 15C7.2 14.6 2.9 10.3 2.5 3.7C2.5 3 3 2.5 3.5 2.5Z" stroke="currentColor" stroke-width="1.4"/>
            </svg>
            <a href="tel:{{ setting('phone', '+20227928400') }}">{{ setting('phone', '+20 2 2792 8400') }}</a>
          </div>
        </div>
      </div>

      <!-- Col 2 (2 cols): Company -->
      <div class="foot-grid__col">
        <h3 class="foot-col__title">{{ sec('global', 'copy_afa2ec755fdf') }}</h3>
        <ul class="foot-col__list">
          <li><a href="{{ url('/') }}">{{ sec('global', 'copy_66b93f927675') }}</a></li>
          <li><a href="{{ url('/about') }}">{{ sec('global', 'copy_321facc49ee7') }}</a></li>
          <li><a href="{{ url('/team') }}">{{ sec('global', 'copy_ca4d3e8fdf98') }}</a></li>
          <li><a href="{{ url('/work') }}">{{ sec('global', 'copy_addc49d04a88') }}</a></li>
          <li><a href="{{ url('/partners') }}">{{ sec('global', 'copy_91619d630bd7') }}</a></li>
          <li><a href="{{ url('/blog') }}">{{ sec('global', 'copy_565b27a38e2b') }}</a></li>
          <li><a href="{{ url('/careers') }}">{{ sec('global', 'copy_b29ba4267f24') }}</a></li>
          <li><a href="{{ url('/contact') }}">{{ sec('global', 'copy_f4b9a5c91fb5') }}</a></li>
        </ul>
      </div>

      <!-- Col 3 (2 cols): Software Solutions -->
      <div class="foot-grid__col">
        <h3 class="foot-col__title">{{ sec('global', 'copy_03be197a2651') }}</h3>
        <ul class="foot-col__list">
          <li><a href="{{ url('/work') }}">{{ sec('global', 'copy_43f2d8ef5f41') }}</a></li>
          <li><a href="{{ url('/work') }}">{{ sec('global', 'copy_1ffceb06c763') }}</a></li>
          <li><a href="{{ url('/work') }}">{{ sec('global', 'copy_f0c7b48874fc') }}</a></li>
          <li><a href="{{ url('/work/alfagn') }}">{{ sec('global', 'copy_e9d3ab4269dc') }}</a></li>
          <li><a href="{{ url('/work') }}">{{ sec('global', 'copy_ca3c76e3e0a6') }}</a></li>
          <li><a href="{{ url('/work') }}">{{ sec('global', 'copy_bcd691d43e8f') }}</a></li>
        </ul>
      </div>

      <!-- Col 4 (2 cols): Growth & Marketing -->
      <div class="foot-grid__col">
        <h3 class="foot-col__title">{{ sec('global', 'copy_5a780da735e3') }}</h3>
        <ul class="foot-col__list">
          <li><a href="{{ url('/work') }}">{{ sec('global', 'copy_a0cd4b53344d') }}</a></li>
          <li><a href="{{ url('/blog/post') }}">{{ sec('global', 'copy_53a9bc564772') }}</a></li>
          <li><a href="{{ url('/work') }}">{{ sec('global', 'copy_5a18d0785981') }}</a></li>
          <li><a href="{{ url('/blog/photos') }}">{{ sec('global', 'copy_714c4a6f2a9c') }}</a></li>
          <li><a href="{{ url('/work') }}">{{ sec('global', 'copy_12de7e0930d1') }}</a></li>
          <li><a href="{{ url('/work') }}">{{ sec('global', 'copy_965533ad102a') }}</a></li>
        </ul>
      </div>

      <!-- Col 5 (2 cols): Newsletter & Social Connect -->
      <div class="foot-grid__newsletter">
        <h3 class="foot-col__title">{{ sec('footer', 'newsletter_title', 'Insights Dispatch') }}</h3>
        <p class="foot-grid__summary">
          {{ sec('footer', 'newsletter_summary', 'Monthly engineering monographs, video episodes, and growth playbooks.') }}
        </p>
        <form class="foot-sub-form" action="{{ route('subscribe') }}" method="post">
            @csrf
          <label for="foot-email-input" class="sr-only">{{ sec('global', 'copy_a9f62d79c357') }}</label>
          <input id="foot-email-input" name="email" type="email" class="foot-sub-form__input" required placeholder="{{ sec('footer', 'newsletter_placeholder', 'Enter your work email...') }}"/>
          <button type="submit" class="btn btn--accent btn--sm">{{ sec('footer', 'newsletter_button', 'Subscribe →') }}</button>
          <div class="form__status" data-form-status role="status" aria-live="polite"></div>
        @if(session('subscribed'))
              <p role="status" style="color:#1a7f4b;font-weight:600">{{ session('subscribed') }}</p>
            @endif
          </form>

        <div class="foot-socials" aria-label="Social media channels">
          <a href="{{ setting('linkedin', 'https://www.linkedin.com') }}" class="foot-social-btn" rel="noopener noreferrer" aria-label="LinkedIn">
            <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M3.5 2A1.5 1.5 0 1 0 3.5 5 1.5 1.5 0 0 0 3.5 2ZM2.2 6.2H4.8V14H2.2V6.2ZM6.4 6.2H8.9V7.3H9C9.4 6.6 10.3 6 11.6 6 14.2 6 14.7 7.7 14.7 10V14H12.1V10.4C12.1 9.5 12.1 8.4 10.8 8.4 9.5 8.4 9.3 9.4 9.3 10.4V14H6.4V6.2Z"/></svg>
          </a>
          <a href="https://www.github.com" class="foot-social-btn" rel="noopener noreferrer" aria-label="GitHub">
            <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 1.3A6.7 6.7 0 0 0 5.9 14.4C6.2 14.5 6.3 14.3 6.3 14.1V12.9C4.4 13.3 4 12.1 4 12.1 3.7 11.3 3.2 11.1 3.2 11.1 2.6 10.7 3.3 10.7 3.3 10.7 4 10.8 4.3 11.4 4.3 11.4 4.9 12.4 5.9 12.1 6.3 11.9 6.4 11.5 6.6 11.2 6.8 11 5.3 10.8 3.7 10.3 3.7 7.7 3.7 7 4 6.4 4.4 5.9 4.3 5.7 4.1 5 4.5 4.1 4.5 4.1 5 3.9 6.3 4.8A6.5 6.5 0 0 1 9.7 4.8C11 3.9 11.5 4.1 11.5 4.1 11.9 5 11.7 5.7 11.6 5.9 12 6.4 12.3 7 12.3 7.7 12.3 10.3 10.7 10.8 9.2 11 9.5 11.2 9.7 11.7 9.7 12.4V14.1C9.7 14.3 9.8 14.5 10.1 14.4A6.7 6.7 0 0 0 8 1.3Z"/></svg>
          </a>
          <a href="{{ setting('youtube', 'https://www.youtube.com') }}" class="foot-social-btn" rel="noopener noreferrer" aria-label="YouTube">
            <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M14.7 4.8A1.8 1.8 0 0 0 13.4 3.5C12.3 3.2 8 3.2 8 3.2S3.7 3.2 2.6 3.5A1.8 1.8 0 0 0 1.3 4.8C1 5.9 1 8 1 8S1 10.1 1.3 11.2A1.8 1.8 0 0 0 2.6 12.5C3.7 12.8 8 12.8 8 12.8S12.3 12.8 13.4 12.5A1.8 1.8 0 0 0 14.7 11.2C15 10.1 15 8 15 8S15 5.9 14.7 4.8ZM6.6 10.1V5.9L10.3 8L6.6 10.1Z"/></svg>
          </a>
          <a href="{{ setting('instagram', 'https://www.instagram.com') }}" class="foot-social-btn" rel="noopener noreferrer" aria-label="Instagram">
            <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 3.4C9.5 3.4 9.7 3.4 10.3 3.4 11.8 3.5 12.5 4.2 12.6 5.7 12.6 6.3 12.6 6.5 12.6 8 12.6 9.5 12.6 9.7 12.6 10.3 12.5 11.8 11.8 12.5 10.3 12.6 9.7 12.6 9.5 12.6 8 12.6 6.5 12.6 6.3 12.6 5.7 12.6 4.2 12.5 3.5 11.8 3.4 10.3 3.4 9.7 3.4 9.5 3.4 8 3.4 6.5 3.4 6.3 3.4 5.7 3.5 4.2 4.2 3.5 5.7 3.4 6.3 3.4 6.5 3.4 8 3.4ZM8 2C6.4 2 6.2 2 5.6 2 3.5 2.1 2.1 3.5 2 5.6 2 6.2 2 6.4 2 8 2 9.6 2 9.8 2 10.4 2.1 12.5 3.5 13.9 5.6 14 6.2 14 6.4 14 8 14 9.6 14 9.8 14 10.4 14 12.5 13.9 13.9 12.5 14 10.4 14 9.8 14 9.6 14 8 14 6.4 14 6.2 14 5.6 13.9 3.5 12.5 2.1 10.4 2 9.8 2 9.6 2 8 2ZM8 4.9A3.1 3.1 0 1 0 8 11.1 3.1 3.1 0 0 0 8 4.9ZM8 10A2 2 0 1 1 8 6 2 2 0 0 1 8 10ZM11.2 4.1A0.7 0.7 0 1 0 11.2 5.5 0.7 0.7 0 0 0 11.2 4.1Z"/></svg>
          </a>
        </div>
      </div>
    </div>

    <!-- 3. Bottom Legal & Copyright Bar -->
    <div class="foot-bottom">
      <p>{{ sec('global', 'copy_ce0795d9d101') }}</p>
      <ul class="foot-bottom__links" aria-label="Legal links">
        <li><a href="{{ url('/contact') }}">{{ sec('global', 'copy_505e2c9ec064') }}</a></li>
        <li><a href="{{ url('/contact') }}">{{ sec('global', 'copy_7c38db12a0f5') }}</a></li>
        <li><a href="{{ url('/contact') }}">{{ sec('global', 'copy_3181b3a91220') }}</a></li>
        <li><a href="#main-content">{{ sec('global', 'copy_39885245a3fb') }}</a></li>
      </ul>
    </div>
  </div>
</footer>



  {{-- Interaction modules: nav (mega menu, drawer), scroll reveal, animated
       counters, media frames, careers filtering — same bundle the static
       site shipped with. Without these the page renders but stays inert. --}}
  <script defer src="{{ asset('assets/js/modules/nav.js') }}"></script>
  <script defer src="{{ asset('assets/js/modules/reveal.js') }}"></script>
  <script defer src="{{ asset('assets/js/modules/counter.js') }}"></script>
  <script defer src="{{ asset('assets/js/modules/media.js') }}"></script>
  <script defer src="{{ asset('assets/js/modules/careers.js') }}"></script>
  @if(($activePage ?? '') === 'team')
    <script defer src="{{ asset('assets/js/modules/team.js') }}"></script>
  @endif
  <script type="module" src="{{ asset('assets/js/main.js') }}"></script>
@yield('scripts')
</body>
</html>
