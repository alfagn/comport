@extends('layouts.public', ['activePage' => 'blog'])

@section('meta')
  <meta name="description" content="Watch the full 14-minute video documentary with interactive chapters, transcript, and studio photography."/>
  <title>Inside an Empetix 5-Day Product & Growth Sprint (Full Video Episode) — Empetix Blog</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="Inside an Empetix 5-Day Product & Growth Sprint (Full Video Episode) — Empetix Blog"/>
  <meta property="og:description" content="Watch the full 14-minute video documentary with interactive chapters, transcript, and studio photography."/>
  <meta property="og:url" content="https://empetix.vercel.app/blog-video.html"/>
  <meta property="og:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:secure_url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution Logo"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="Inside an Empetix 5-Day Product & Growth Sprint (Full Video Episode) — Empetix Blog"/>
  <meta name="twitter:description" content="Watch the full 14-minute video documentary with interactive chapters, transcript, and studio photography."/>
  <meta name="twitter:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
@endsection

@section('content')
<main id="main-content">


    <div class="read-progress" aria-hidden="true">
      <div id="read-progress-bar" class="read-progress__bar"></div>
    </div>

    <header class="article-head">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ url('/') }}">Home</a></li>
          <li><a href="{{ url('/blog') }}">Blog &amp; Media</a></li>
          <li aria-current="page">5-Day Product &amp; Growth Sprint (Video)</li>
        </ol>

        <div class="article-head__meta">
          <span class="article-head__cat">VIDEO EPISODE STUDIO DOCUMENTARY</span>
          <time datetime="2026-11-05">05 NOVEMBER 2026</time>
          <span>RUNTIME 14:20 &middot; INTERACTIVE CHAPTERS + PHOTOS</span>
        </div>

        <h1 class="article-head__title">Inside an Empetix 5-Day Product &amp; Growth Sprint</h1>
        <p class="article-head__dek">
          Watch the complete studio episode showing how our software architects, product designers, and media buyers take a product from commercial bottleneck to user-tested prototype and live acquisition test in one week.
        </p>
      </div>
    </header>

    <!-- Main Interactive Video Player + Chapters Grid -->
    <section class="section section--flush-top">
      <div class="container">
        <div class="grid">
          <div class="lg-8">
            <div class="video" data-video-player="sprint-episode">
              <img class="video__poster" src="{{ asset('assets/img/blog/design-sprint.jpg') }}" alt="Empetix team voting on product wireframes pinned to concrete studio wall" width="1280" height="720"/>
              <video class="video__el" preload="metadata" poster="assets/img/blog/design-sprint.jpg">
                <source src="{{ asset('assets/video/design-sprint.mp4') }}" type="video/mp4"/>
              </video>
              <button type="button" class="video__btn" data-video-play aria-label="Play full 14-minute sprint documentary">
                <span class="video__btn-inner">
                  <span class="video__btn-icon">
                    <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><polygon points="4,2 14,8 4,14"/></svg>
                  </span>
                  <span>Play Full Episode (14:20)</span>
                </span>
              </button>
            </div>
            <div class="video__meta">
              <span>FILMED AT EMPETIX CAIRO STUDIO &middot; 4K DOWNSCALED STREAM</span>
              <span>FEATURING OMAR HADDAD, LINA MANSOUR &amp; SARA EL-MASRY</span>
            </div>
          </div>

          <div class="lg-4">
            <p class="eyebrow">Interactive Chapters (Click to Seek)</p>
            <div class="chapters">
              <button type="button" class="chapters__btn" data-video-target="sprint-episode" data-seek="0">
                <span class="chapters__time">00:00</span>
                <span>Day 1 &mdash; Mapping the Revenue &amp; Technical Bottleneck</span>
              </button>
              <button type="button" class="chapters__btn" data-video-target="sprint-episode" data-seek="2">
                <span class="chapters__time">03:15</span>
                <span>Day 2 &mdash; Divergent Interface Sketching &amp; API Contracts</span>
              </button>
              <button type="button" class="chapters__btn" data-video-target="sprint-episode" data-seek="4">
                <span class="chapters__time">06:40</span>
                <span>Day 3 &mdash; Critique Wall, Dot Voting &amp; Funnel Architecture</span>
              </button>
              <button type="button" class="chapters__btn" data-video-target="sprint-episode" data-seek="6">
                <span class="chapters__time">09:50</span>
                <span>Day 4 &mdash; Building the Interactive Prototype &amp; Ad Hooks</span>
              </button>
              <button type="button" class="chapters__btn" data-video-target="sprint-episode" data-seek="8">
                <span class="chapters__time">12:10</span>
                <span>Day 5 &mdash; Live Customer Interviews &amp; Paid Smoke Test</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Full Episode Post & Stills Gallery -->
    <section id="article-content" class="section section--ruled">
      <div class="container article-layout">
        <aside class="article-layout__aside">
          <div class="byline">
            <div class="byline__avatar">
              <img src="{{ asset('assets/img/team/ayman-ibrahim.jpg') }}" alt="Ayman Ibrahim" width="96" height="96"/>
            </div>
            <div>
              <p class="byline__name">Ayman Ibrahim</p>
              <p class="byline__role">Co-Founder &amp; CEO</p>
            </div>
          </div>

          <div class="aside-block">
            <p class="aside-block__title">Episode Deliverables</p>
            <ul class="toc">
              <li>1. Clickable Figma &amp; React Prototype</li>
              <li>2. Laravel API Schema &amp; ERD</li>
              <li>3. 5 Recorded User Test Sessions</li>
              <li>4. Paid Acquisition Smoke-Test Report</li>
            </ul>
          </div>
        </aside>

        <article class="article-layout__body prose">
          <h2>Why traditional design sprints fail without engineers and media buyers in the room</h2>
          <p>
            A standard UX design sprint produces a pretty prototype on Friday afternoon—only for the engineering team to declare it infeasible on Monday morning, or for the marketing team to discover that the core value proposition costs USD 95 per click to acquire.
          </p>
          <p>
            In an Empetix Sprint, a Staff Laravel/Flutter Architect and a Performance Marketing Lead participate alongside the Product Designer and the client&rsquo;s founders for all five days.
          </p>

          <h3>Episode Stills Gallery (Click any frame to open in Lightbox)</h3>
          <div class="gallery">
            <div class="gallery__item">
              <button type="button" class="gallery__btn" data-lightbox-src="{{ asset('assets/img/blog/design-sprint.jpg') }}" data-lightbox-caption="Day 3 — Silent dot-voting on candidate checkout architectures.">
                <div class="frame frame--zoom">
                  <img src="{{ asset('assets/img/blog/design-sprint.jpg') }}" alt="Team placing coloured dot stickers on wireframe sketches" width="900" height="600"/>
                </div>
                <div class="gallery__caption">
                  <span>STILL DAY 3 DOT VOTING</span>
                  <span>ZOOM +</span>
                </div>
              </button>
            </div>

            <div class="gallery__item">
              <button type="button" class="gallery__btn" data-lightbox-src="{{ asset('assets/img/home/hero.jpg') }}" data-lightbox-caption="Day 1 — Founders and architects aligning on unit economics and API constraints.">
                <div class="frame frame--zoom">
                  <img src="{{ asset('assets/img/home/hero.jpg') }}" alt="Team gathered around long wooden table in Cairo studio" width="600" height="750"/>
                </div>
                <div class="gallery__caption">
                  <span>STILL DAY 1 ALIGNMENT</span>
                  <span>ZOOM +</span>
                </div>
              </button>
            </div>

            <div class="gallery__item">
              <button type="button" class="gallery__btn" data-lightbox-src="{{ asset('assets/img/work/medlink.jpg') }}" data-lightbox-caption="Day 2 — Reviewing low-fidelity user flows before writing a single line of UI code.">
                <div class="frame frame--zoom">
                  <img src="{{ asset('assets/img/work/medlink.jpg') }}" alt="Designers inspecting paper wireframes pinned to studio wall" width="750" height="500"/>
                </div>
                <div class="gallery__caption">
                  <span>STILL DAY 2 WIREFRAMES</span>
                  <span>ZOOM +</span>
                </div>
              </button>
            </div>

            <div class="gallery__item">
              <button type="button" class="gallery__btn" data-lightbox-src="{{ asset('assets/img/blog/performance-playbook.jpg') }}" data-lightbox-caption="Day 5 — Analyzing live ad CTR and landing-page scroll depth from the 48-hour smoke test.">
                <div class="frame frame--zoom">
                  <img src="{{ asset('assets/img/blog/performance-playbook.jpg') }}" alt="Analysts reviewing conversion telemetry on large monitors" width="900" height="600"/>
                </div>
                <div class="gallery__caption">
                  <span>STILL DAY 5 TELEMETRY</span>
                  <span>ZOOM +</span>
                </div>
              </button>
            </div>
          </div>

          <h2>Full Episode Transcript Highlights</h2>
          <p>
            <strong>[00:42] Ayman Ibrahim (CEO):</strong> &ldquo;On Monday morning we lock the room with one rule: we are not here to debate opinions. By Friday at 4:00 PM we will have five real customers using a working prototype and real paid traffic hitting two competing value propositions.&rdquo;
          </p>
          <p>
            <strong>[06:55] Mohanad Ahmed Shehata (CTO):</strong> &ldquo;While Lina&rsquo;s team sketches the merchant checkout flow on Tuesday, we map the exact payment gateway webhooks, latency budgets, and database indexes so there are zero surprises when sprint one of production engineering starts.&rdquo;
          </p>
          <p>
            <strong>[12:18] Bishoy Magdy (VP Growth):</strong> &ldquo;By running a USD 1,500 micro-budget smoke test across three creative angles on Day 4, we walk into Friday&rsquo;s executive review knowing not only that users can complete the flow, but which headline delivers a 3.4% click-through rate.&rdquo;
          </p>
        </article>
      </div>
    </section>

    <div class="container">
      <a href="{{ url('/blog/photos') }}" class="pager">
        <span class="pager__label">Next Post &middot; Photo Essay + Commercial Reel</span>
        <span class="pager__title">
          <span>Behind the Lens: Directing the Qahwa Roasters Launch Shoot</span>
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="currentColor" stroke-width="2"/></svg>
        </span>
      </a>
    </div>

    <!-- Full-Screen Photo Lightbox Dialog -->
    <dialog id="photo-lightbox" class="lightbox" aria-label="Full-screen photo viewer">
      <div class="lightbox__bar">
        <span class="lightbox__counter" data-lightbox-counter>01 / 04</span>
        <button type="button" class="lightbox__btn" data-lightbox-close>Close (Esc)</button>
      </div>
      <div class="lightbox__stage">
        <img class="lightbox__img" data-lightbox-img src="{{ asset('assets/img/blog/arabic-first.jpg') }}" alt=""/>
      </div>
      <div class="lightbox__foot">
        <p class="lightbox__caption" data-lightbox-caption></p>
        <div class="lightbox__nav">
          <button type="button" class="lightbox__btn" data-lightbox-prev aria-label="Previous photo">&larr; Prev</button>
          <button type="button" class="lightbox__btn" data-lightbox-next aria-label="Next photo">Next &rarr;</button>
        </div>
      </div>
    </dialog>
@endsection
