<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="facebook-domain-verification" content="bvdfe0u00d59bquv5ky6k4375wfygg" />
  @if(setting('seo_allow_indexing', '1') === '0')
    <meta name="robots" content="noindex, nofollow"/>
  @else
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1"/>
  @endif
  <meta name="author" content="Empetix Solutions"/>
  <meta name="publisher" content="Empetix Solutions"/>
  <link rel="canonical" href="{{ seo_current_url() }}"/>
  <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ seo_url('/sitemap.xml') }}"/>

  @yield('meta')
  @yield('head_extra')

  {{-- Comprehensive Schema.org JSON-LD: Google Knowledge Graph & AI Models (ChatGPT, Perplexity, Claude, Gemini) --}}
  @include('partials.schema-org')


  <link rel="preload" as="font" type="font/woff2" href="{{ asset('assets/fonts/archivo-700.woff2') }}" crossorigin/>
  <link rel="preload" as="font" type="font/woff2" href="{{ asset('assets/fonts/public-sans-400.woff2') }}" crossorigin/>

  <link rel="icon" href="{{ brand_favicon() }}"/>
  <link rel="preload" as="image" href="{{ brand_logo() }}" fetchpriority="high"/>
  @foreach([
    'assets/css/base/variables.css',
    'assets/css/base/reset.css',
    'assets/css/base/typography.css',
    'assets/css/layout/grid.css',
    'assets/css/layout/header.css',
    'assets/css/layout/footer.css',
    'assets/css/components/preloader.css',
    'assets/css/components/button.css',
    'assets/css/components/mega-menu.css',
    'assets/css/components/hero.css',
    'assets/css/components/card.css',
    'assets/css/components/founders.css',
    'assets/css/components/lists.css',
    'assets/css/components/media.css',
    'assets/css/components/form.css',
    'assets/css/components/cookie-consent.css',
    'assets/css/components/support-chatbot.css',
    'assets/css/pages/home.css',
    'assets/css/pages/article.css',
    'assets/css/pages/inner.css',
    'assets/css/pages/founder.css',
    'assets/css/pages/team.css',
    'assets/css/pages/careers.css',
  ] as $cssFile)
  <link rel="stylesheet" href="{{ versioned_asset($cssFile) }}"/>
  @endforeach
  <script src="{{ versioned_asset('assets/js/preloader-gate.js') }}"></script>
  <noscript>
    <link rel="stylesheet" href="{{ versioned_asset('assets/css/noscript.css') }}"/>
  </noscript>

</head>
<body>
@if(\App\Models\ServicePage::where('locale', 'en')->where('is_published', true)->exists())
<nav class="published-service-links" aria-label="Service pages" style="background:#0a1426;color:white;padding:10px 24px;text-align:center;font-size:14px">
  <a href="{{ url('/services') }}">Services</a>
</nav>
@endif

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
        src="{{ brand_logo() }}"
        alt="Empetix Solution"
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
<a href="#main-content" class="skip-link">Skip to main content</a>

<div id="site-head" class="site-head">
  <!-- Row 1: Top Utility Bar (Links on the Left, Search Button Only on the Right) -->
  <div class="utility" role="region" aria-label="Secondary navigation">
    <div class="utility__inner">
      <ul class="utility__links">
        {{-- Dashboard-editable links (Navigation → Utility bar) with resilient editorial fallback --}}
        @php
          $utilityNav = nav_items('utility')->filter(function ($navItem) {
              $uUrl = strtolower(trim($navItem->url, '/'));
              $uKey = match(true) {
                  str_contains($uUrl, 'career') => 'careers',
                  str_contains($uUrl, 'work') || str_contains($uUrl, 'project') => 'work',
                  str_contains($uUrl, 'team') || str_contains($uUrl, 'founder') => 'team',
                  str_contains($uUrl, 'partner') => 'partners',
                  str_contains($uUrl, 'blog') || str_contains($uUrl, 'post') => 'blog',
                  str_contains($uUrl, 'contact') => 'contact',
                  str_contains($uUrl, 'about') => 'about',
                  default => null,
              };
              return ! $uKey || page_active($uKey);
          });
        @endphp
        @if($utilityNav->isNotEmpty())
          @foreach($utilityNav as $navItem)
            <li class="utility__item"><a href="{{ str_starts_with($navItem->url, 'http') ? $navItem->url : url($navItem->url) }}" class="utility__link"@if($navItem->label === 'Blog') data-mega-trigger="journal" aria-expanded="false" aria-controls="mega-journal"@endif>{{ $navItem->label }}</a></li>
          @endforeach
        @else
          @if(page_active('careers')) <li class="utility__item"><a href="{{ url('/careers') }}" class="utility__link">Careers</a></li> @endif
          @if(page_active('work')) <li class="utility__item"><a href="{{ url('/work') }}" class="utility__link">Projects</a></li> @endif
          @if(page_active('team')) <li class="utility__item"><a href="{{ url('/team') }}" class="utility__link">Team</a></li> @endif
          @if(page_active('blog')) <li class="utility__item"><a href="{{ url('/blog') }}" class="utility__link" data-mega-trigger="journal" aria-expanded="false" aria-controls="mega-journal">Blog &amp; Insights</a></li> @endif
          @if(page_active('contact')) <li class="utility__item"><a href="{{ url('/contact') }}" class="utility__link">Contact</a></li> @endif
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
        <label for="search-input" class="sr-only">Search projects, blog posts, and roles</label>
        <input id="search-input" type="search" name="q" class="search-panel__input" placeholder="{{ sec('header', 'search_placeholder', 'Search solutions, case studies, engineering essays, video episodes...') }}" autocomplete="off"/>
        <button type="submit" class="btn btn--accent btn--sm">Search</button>
        <button type="button" id="search-close" class="btn btn--ghost btn--sm">Close</button>
      </form>
      <div class="search-panel__hints">
        <span>Quick jump:</span>
        @foreach(\App\Models\Project::active()->take(2)->get() as $qProj)
          <a href="{{ route('work.show', $qProj->slug) }}">{{ $qProj->client ?: \Illuminate\Support\Str::limit($qProj->title, 24) }}</a>
        @endforeach
        @if($qPost = \App\Models\Post::published()->first())
          <a href="{{ route('blog.show', $qPost->slug) }}">{{ \Illuminate\Support\Str::limit($qPost->title, 26) }}</a>
        @endif
        @if(page_active('about')) <a href="{{ url('/about') }}#founders">Meet the Founders</a> @endif
        @if(page_active('contact')) <a href="{{ url('/contact') }}"@if(($activePage ?? '') === 'contact') aria-current="page"@endif>Contact Us</a> @endif
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
          @if(page_active('work'))
            <li class="nav__item">
              <a href="{{ url('/work') }}"@if(($activePage ?? '') === 'work') aria-current="page"@endif class="nav__link">
                <span>Projects &amp; Platforms</span>
              </a>
            </li>
          @endif
          @if(page_active('blog'))
            <li class="nav__item">
              <a href="{{ url('/blog') }}"@if(($activePage ?? '') === 'blog') aria-current="page"@endif class="nav__link" data-mega-trigger="journal" aria-expanded="false" aria-controls="mega-journal">
                <span>Blogs &amp; Media</span>
                <svg class="nav__chevron" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                  <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </a>
            </li>
          @endif
          @if(page_active('partners'))
            <li class="nav__item">
              <a href="{{ url('/partners') }}"@if(($activePage ?? '') === 'partners') aria-current="page"@endif class="nav__link">
                <span>Partners &amp; Industries</span>
              </a>
            </li>
          @endif
          @if(page_active('about'))
            <li class="nav__item">
              <a href="{{ url('/about') }}"@if(($activePage ?? '') === 'about') aria-current="page"@endif class="nav__link">
                <span>About &amp; Founders</span>
              </a>
            </li>
          @endif
        </ul>
      </nav>

      @if(page_active('contact'))
        <div class="nav-bar__actions">
          <a href="{{ url('/contact') }}"@if(($activePage ?? '') === 'contact') aria-current="page"@endif class="nav-bar__cta">
            <span>Contact Us</span>
            <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
            </svg>
          </a>
        </div>
      @endif

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

  <!-- PARTIAL: mega-menu (100% Real Dynamic CMS Data) -->
@php
  $liveMegaServices = \App\Models\Service::active()->get();
  $publishedServicePagesBySlug = \App\Models\ServicePage::where('locale', 'en')->where('is_published', true)->get()->keyBy('slug');
  $liveFeaturedProject = page_active('work')
      ? \App\Models\Project::active()->orderByDesc('is_featured')->orderBy('sort_order')->first()
      : null;
  $liveMegaPosts = page_active('blog')
      ? \App\Models\Post::published()->take(3)->get()
      : collect();
@endphp
<div id="mega-menu" class="mega" role="region" aria-label="Expanded studio directory">
  <!-- Panel 1: Capabilities & Solutions (Live Services + Live Featured Case Study) -->
  <div id="mega-capabilities" class="mega__panel" data-mega-panel="capabilities">
    <div class="container mega__grid">
      <div class="mega__intro">
        <div>
          <span class="eyebrow">01 / Capabilities</span>
          <h2 class="mega__title">{{ sec('header', 'capabilities_title', 'One studio. Software, cloud, and AI under one roof.') }}</h2>
          <p class="mega__desc">{{ sec('header', 'capabilities_desc', 'We pair principal software architects, mobile engineers, and cloud infrastructure leads so mission-critical platforms ship fast and scale reliably.') }}</p>
        </div>
        @if(page_active('work'))
          <a href="{{ url('/work') }}"@if(($activePage ?? '') === 'work') aria-current="page"@endif class="arrow-link">
            <span>Explore All Case Studies</span>
            <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
            </svg>
          </a>
        @endif
      </div>

      @if($liveMegaServices->isNotEmpty())
        @php
          $halfCount = (int) ceil($liveMegaServices->count() / 2);
          $serviceCol1 = $liveMegaServices->take($halfCount);
          $serviceCol2 = $liveMegaServices->slice($halfCount);
          // Also include any custom links added by admin under Navigation -> mega-capabilities
          $customCapLinks = nav_mega('mega-capabilities')
              ->flatMap(fn ($col) => $col->children)
              ->filter(fn ($l) => empty($l->image) && ! in_array($l->label, [
                  'Web & Mobile Platforms',
                  'E-Commerce & FinTech Core',
                  'AI Automation & Data Pipelines',
                  'Enterprise LMS & WebRTC Classrooms',
                  'ERP Systems & Payment Gateways',
                  'Cloud DevOps & Observability',
                  'Performance & Paid Acquisition',
                  'Brand Systems & Campaign Production',
                  'CRO, SEO & Lifecycle Retention',
              ], true));
        @endphp

        <div class="mega__col">
          <p class="mega__col-title">Software Engineering</p>
          <ul>
            @foreach($serviceCol1 as $svc)
              @php
                $svcUrl = ($svc->slug && $publishedServicePagesBySlug->has($svc->slug))
                    ? url($publishedServicePagesBySlug->get($svc->slug)->publicPath())
                    : ($svc->cta_url ? (str_starts_with($svc->cta_url, 'http') ? $svc->cta_url : url($svc->cta_url)) : url('/#solutions-hub'));
              @endphp
              <li>
                <a href="{{ $svcUrl }}" class="mega__link">
                  <span class="mega__link-title">
                    <span>{{ $svc->title }}</span>
                    <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
                  </span>
                  @if($svc->heading || $svc->description)
                    <span class="mega__link-sub">{{ \Illuminate\Support\Str::limit($svc->heading ?: $svc->description, 72) }}</span>
                  @endif
                </a>
              </li>
            @endforeach
          </ul>
        </div>

        <div class="mega__col">
          <p class="mega__col-title">Cloud, AI &amp; Enterprise Systems</p>
          <ul>
            @foreach($serviceCol2 as $svc)
              @php
                $svcUrl = ($svc->slug && $publishedServicePagesBySlug->has($svc->slug))
                    ? url($publishedServicePagesBySlug->get($svc->slug)->publicPath())
                    : ($svc->cta_url ? (str_starts_with($svc->cta_url, 'http') ? $svc->cta_url : url($svc->cta_url)) : url('/#solutions-hub'));
              @endphp
              <li>
                <a href="{{ $svcUrl }}" class="mega__link">
                  <span class="mega__link-title">
                    <span>{{ $svc->title }}</span>
                    <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
                  </span>
                  @if($svc->heading || $svc->description)
                    <span class="mega__link-sub">{{ \Illuminate\Support\Str::limit($svc->heading ?: $svc->description, 72) }}</span>
                  @endif
                </a>
              </li>
            @endforeach
            @foreach($customCapLinks as $cLink)
              <li>
                <a href="{{ str_starts_with($cLink->url, 'http') ? $cLink->url : url($cLink->url) }}" class="mega__link">
                  <span class="mega__link-title">
                    <span>{{ $cLink->label }}</span>
                    <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
                  </span>
                  @if($cLink->sub)<span class="mega__link-sub">{{ $cLink->sub }}</span>@endif
                </a>
              </li>
            @endforeach
          </ul>
        </div>

        <div class="mega__col">
          <p class="mega__col-title">Featured Case Study</p>
          @if($liveFeaturedProject)
            @php
              $featImg = $liveFeaturedProject->image ?: $liveFeaturedProject->logo;
              $featImgUrl = $featImg
                  ? (str_starts_with((string) $featImg, 'uploads/') ? asset('storage/'.$featImg) : asset($featImg))
                  : asset('assets/img/work/alfagn/alfagn_seo.png');
            @endphp
            <a href="{{ route('work.show', $liveFeaturedProject->slug) }}" class="mega__feature">
              <div class="frame frame--16x9 frame--zoom">
                <img src="{{ $featImgUrl }}" alt="{{ $liveFeaturedProject->title }}" width="640" height="360"/>
              </div>
              <span class="t-meta">{{ strtoupper(($liveFeaturedProject->client ?: $liveFeaturedProject->kicker ?: 'CASE STUDY').($liveFeaturedProject->year ? ' · '.$liveFeaturedProject->year : '')) }}</span>
              <p class="mega__feature-title">{{ \Illuminate\Support\Str::limit($liveFeaturedProject->title, 85) }}</p>
            </a>
          @endif
        </div>
      @else
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
      @endif
    </div>
  </div>

  <!-- Panel 2: Journal / Blogs Preview (Live Published Posts) -->
  <div id="mega-journal" class="mega__panel" data-mega-panel="journal">
    <div class="container mega__grid">
      <div class="mega__intro">
        <div>
          <span class="eyebrow">02 / Empetix Journal</span>
          <h2 class="mega__title">{{ sec('header', 'journal_title', 'Engineering articles, system talks & architecture notes.') }}</h2>
          <p class="mega__desc">{{ sec('header', 'journal_desc', 'Field notes, distributed systems deep-dives, and technical briefings from our engineering leads and founders.') }}</p>
        </div>
        @if(page_active('blog'))
          <a href="{{ url('/blog') }}"@if(($activePage ?? '') === 'blog') aria-current="page"@endif class="arrow-link">
            <span>Open Full Blog Archive</span>
            <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
            </svg>
          </a>
        @endif
      </div>

      @if($liveMegaPosts->isNotEmpty())
        @foreach($liveMegaPosts as $megaPost)
          @php
            $postImg = $megaPost->cover_image
                ? (str_starts_with((string) $megaPost->cover_image, 'uploads/') ? asset('storage/'.$megaPost->cover_image) : asset($megaPost->cover_image))
                : asset('assets/img/blog/talks-engineering.jpg');
          @endphp
          <div class="mega__col">
            <p class="mega__col-title">{{ strtoupper($megaPost->category ?: 'ENGINEERING BRIEF') }}</p>
            <a href="{{ route('blog.show', $megaPost->slug) }}" class="mega__feature">
              <div class="frame frame--3x2 frame--zoom">
                <img src="{{ $postImg }}" alt="{{ $megaPost->title }}" width="600" height="400"/>
              </div>
              <span class="t-meta">{{ strtoupper($megaPost->published_at ? $megaPost->published_at->format('d M Y') : 'EMPETIX JOURNAL') }}</span>
              <p class="mega__feature-title">{{ $megaPost->title }}</p>
            </a>
          </div>
        @endforeach
      @else
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
      @endif
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
      {{-- Dashboard-editable links (Navigation → Mobile drawer). --}}
      @foreach(nav_items('drawer') as $navItem)
        <li><a href="{{ str_starts_with($navItem->url, 'http') ? $navItem->url : url($navItem->url) }}" class="drawer__link">{{ $navItem->label }}</a></li>
      @endforeach
    </ul>

    <div class="drawer__meta">
      <p>New Business: {{ setting('email', 'hello@empetix.com') }}</p>
      <p>Careers: {{ setting('talent_email', 'talent@empetix.com') }}</p>
      <p>{{ setting('address', 'New Cairo, Egypt') }}</p>
    </div>
  </div>
</aside>


  <main id="main-content" @class([$pageClass ?? (($activePage ?? '') === 'team' ? 'team-page' : null)])>
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
        @if(page_active('contact'))
          <a href="{{ url('/contact') }}" class="btn btn--accent btn--lg">
            <span>{{ sec('footer', 'banner_cta_primary', 'Start a Project') }}</span>
            <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
            </svg>
          </a>
        @endif
        @if(page_active('careers'))
          <a href="{{ url('/careers') }}" class="btn btn--ghost btn--lg">
            <span>{{ sec('footer', 'banner_cta_secondary', 'Careers Portal') }}</span>
          </a>
        @endif
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
            alt="Empetix Solution"
            width="881"
            height="302"
          />
        </a>

        <p class="foot-grid__summary">
          {{ sec('footer', 'brand_summary', 'Empetix Solutions is an enterprise software engineering, SaaS, and cloud architecture studio. We build mission-critical web, mobile, and AI platforms engineered for scale.') }}
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
            <a href="tel:{{ setting('phone', '+201055209612') }}">{{ setting('phone', '+20 105 520 9612') }}</a>
          </div>
        </div>
      </div>

      <!-- Col 2 (2 cols): Company -->
      <div class="foot-grid__col">
        <h3 class="foot-col__title">Company</h3>
        <ul class="foot-col__list">
          @if(page_active('home')) <li><a href="{{ url('/') }}">Home</a></li> @endif
          @if(page_active('about')) <li><a href="{{ url('/about') }}">About &amp; Founders</a></li> @endif
          @if(page_active('team')) <li><a href="{{ url('/team') }}">Our Team</a></li> @endif
          @if(page_active('work')) <li><a href="{{ url('/work') }}">Selected Projects</a></li> @endif
          @if(page_active('partners')) <li><a href="{{ url('/partners') }}">Corporate Partners</a></li> @endif
          @if(page_active('blog')) <li><a href="{{ url('/blog') }}">Blogs &amp; Media</a></li> @endif
          @if(page_active('careers')) <li><a href="{{ url('/careers') }}">Careers Portal</a></li> @endif
          @if(page_active('contact')) <li><a href="{{ url('/contact') }}">Contact Us</a></li> @endif
        </ul>
      </div>

      <!-- Col 3 (2 cols): Live Capabilities (Real Services from DB) -->
      <div class="foot-grid__col">
        <h3 class="foot-col__title">Capabilities</h3>
        <ul class="foot-col__list">
          @forelse($liveMegaServices->take(6) as $footSvc)
            @php
              $footSvcUrl = ($footSvc->slug && $publishedServicePagesBySlug->has($footSvc->slug))
                  ? url($publishedServicePagesBySlug->get($footSvc->slug)->publicPath())
                  : url('/#solutions-hub');
            @endphp
            <li><a href="{{ $footSvcUrl }}">{{ $footSvc->title }}</a></li>
          @empty
            <li><a href="{{ url('/#solutions-hub') }}">Web &amp; SaaS Platforms</a></li>
            <li><a href="{{ url('/#solutions-hub') }}">Mobile Apps (Flutter)</a></li>
            <li><a href="{{ url('/#solutions-hub') }}">Cloud &amp; Distributed APIs</a></li>
            <li><a href="{{ url('/#solutions-hub') }}">AI &amp; Data Pipelines</a></li>
          @endforelse
        </ul>
      </div>

      <!-- Col 4 (2 cols): Live Platforms & Industries (Real Projects & Sectors from DB) -->
      <div class="foot-grid__col">
        <h3 class="foot-col__title">Platforms &amp; Sectors</h3>
        <ul class="foot-col__list">
          @if(page_active('work'))
            @foreach(\App\Models\Project::active()->take(3)->get() as $footProj)
              <li><a href="{{ route('work.show', $footProj->slug) }}">{{ \Illuminate\Support\Str::limit($footProj->client ?: $footProj->title, 26) }}</a></li>
            @endforeach
          @endif
          @foreach(\App\Models\Industry::active()->take(3)->get() as $footInd)
            <li><a href="{{ page_active('partners') ? url('/partners') : url('/') }}">{{ $footInd->title }}</a></li>
          @endforeach
        </ul>
      </div>

      <!-- Col 5 (2 cols): Newsletter & Social Connect -->
      <div class="foot-grid__newsletter">
        <h3 class="foot-col__title">{{ sec('footer', 'newsletter_title', 'Engineering Dispatch') }}</h3>
        <p class="foot-grid__summary">
          {{ sec('footer', 'newsletter_summary', 'Monthly system architecture monographs, engineering talks, and platform release notes.') }}
        </p>
        <form class="foot-sub-form" action="{{ route('subscribe') }}" method="post">
            @csrf
          <label for="foot-email-input" class="sr-only">Work email address</label>
          <input id="foot-email-input" name="email" type="email" class="foot-sub-form__input" required placeholder="{{ sec('footer', 'newsletter_placeholder', 'Enter your work email...') }}"/>
          <button type="submit" class="btn btn--accent btn--sm">{{ sec('footer', 'newsletter_button', 'Subscribe →') }}</button>
          <div class="form__status" data-form-status role="status" aria-live="polite"></div>
        @if(session('subscribed'))
              <p role="status" style="color:#1a7f4b;font-weight:600">{{ session('subscribed') }}</p>
            @endif
          </form>

        <div class="foot-socials" aria-label="Social media channels">
          @if(setting('facebook', 'https://www.facebook.com'))
          <a href="{{ setting('facebook', 'https://www.facebook.com') }}" class="foot-social-btn" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
            <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M9.2 14.5V8.7H11.1L11.4 6.4H9.2V5C9.2 4.3 9.4 3.9 10.3 3.9H11.5V1.9C11.3 1.9 10.6 1.8 9.8 1.8 8.1 1.8 6.9 2.8 6.9 4.7V6.4H5V8.7H6.9V14.5H9.2Z"/></svg>
          </a>
          @endif
          <a href="{{ setting('linkedin', 'https://www.linkedin.com') }}" class="foot-social-btn" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
            <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M3.5 2A1.5 1.5 0 1 0 3.5 5 1.5 1.5 0 0 0 3.5 2ZM2.2 6.2H4.8V14H2.2V6.2ZM6.4 6.2H8.9V7.3H9C9.4 6.6 10.3 6 11.6 6 14.2 6 14.7 7.7 14.7 10V14H12.1V10.4C12.1 9.5 12.1 8.4 10.8 8.4 9.5 8.4 9.3 9.4 9.3 10.4V14H6.4V6.2Z"/></svg>
          </a>
          <a href="{{ setting('github', 'https://www.github.com') }}" class="foot-social-btn" rel="noopener noreferrer" aria-label="GitHub">
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
      <p>{{ sec('footer', 'copyright', '© 2026 Empetix Solutions LLC. All rights reserved.') }}</p>
      <ul class="foot-bottom__links" aria-label="Legal links">
        <li><a href="{{ url('/contact') }}">Privacy &amp; Legal</a></li>
        <li><button type="button" class="foot-bottom__cookie-btn" data-cookie-settings>{{ setting('cookie_settings_label', 'Cookie Settings') }}</button></li>
        <li><a href="#main-content">Back to Top ↑</a></li>
      </ul>
    </div>
  </div>
</footer>

@include('partials.cookie-consent')
@include('partials.support-chatbot')

  {{-- Interaction modules: nav (mega menu, drawer), scroll reveal, animated
       counters, media frames, careers filtering, and cookie consent. --}}
  <script defer src="{{ versioned_asset('assets/js/modules/nav.js') }}"></script>
  <script defer src="{{ versioned_asset('assets/js/modules/reveal.js') }}"></script>
  <script defer src="{{ versioned_asset('assets/js/modules/counter.js') }}"></script>
  <script defer src="{{ versioned_asset('assets/js/modules/media.js') }}"></script>
  <script defer src="{{ versioned_asset('assets/js/modules/careers.js') }}"></script>
  <script defer src="{{ versioned_asset('assets/js/modules/cookies.js') }}"></script>
  <script defer src="{{ versioned_asset('assets/js/modules/support-chatbot.js') }}"></script>
  @if(($activePage ?? '') === 'team')
    <script defer src="{{ versioned_asset('assets/js/modules/team.js') }}"></script>
  @endif
  <script type="module" src="{{ versioned_asset('assets/js/main.js') }}"></script>
@yield('scripts')
</body>
</html>
