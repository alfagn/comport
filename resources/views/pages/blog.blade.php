@extends('layouts.public', ['activePage' => 'blog'])

@section('meta')
  <meta name="description" content="Read full engineering articles, watch studio video episodes, and explore behind-the-scenes photo essays from Empetix."/>
  <title>The Empetix Journal: Engineering Articles, Video Episodes & Photo Essays</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="The Empetix Journal: Engineering Articles, Video Episodes & Photo Essays"/>
  <meta property="og:description" content="Read full engineering articles, watch studio video episodes, and explore behind-the-scenes photo essays from Empetix."/>
  <meta property="og:url" content="https://empetix.vercel.app/blog.html"/>
  <meta property="og:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:secure_url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution Logo"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="The Empetix Journal: Engineering Articles, Video Episodes & Photo Essays"/>
  <meta name="twitter:description" content="Read full engineering articles, watch studio video episodes, and explore behind-the-scenes photo essays from Empetix."/>
  <meta name="twitter:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
@endsection

@section('content')
<main id="main-content">


    <section class="page-hero">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ url('/') }}">Home</a></li>
          <li aria-current="page">Blog &amp; Media</li>
        </ol>
        <h1 class="page-hero__title">The Empetix Journal: Engineering, Growth, Video &amp; Photo Essays.</h1>
        <div class="page-hero__row">
          <div class="page-hero__label">
            <span class="eyebrow">Studio Publications</span>
          </div>
          <p class="page-hero__lead">
            Long-form technical articles, full video episodes with chapter transcripts, and behind-the-scenes photography galleries from our software and marketing teams. Click any entry to open the full post.
          </p>
          <div class="page-hero__aside">
            <span class="t-meta">VIDEOS &middot; PHOTOS &middot; ESSAYS</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Video Post Banner -->
    <section class="section section--flush-top">
      <div class="container">
        <article class="post-card post-card--featured reveal">
          <a href="{{ url('/blog/video') }}" class="post-card__link">
            <div class="frame post-card__frame frame--zoom">
              <img src="{{ asset('assets/img/blog/design-sprint.jpg') }}" alt="Empetix product and growth team placing voting dots on interface sketches during a 5-day sprint" width="1200" height="750"/>
              <span class="post-card__play" aria-hidden="true">
                <svg viewBox="0 0 16 16" fill="currentColor"><polygon points="4,2 14,8 4,14"/></svg>
              </span>
              <span class="post-card__badge">FEATURED VIDEO EPISODE &middot; 14:20</span>
            </div>
            <div class="post-card__text">
              <div class="post-card__meta">
                <span class="post-card__cat">VIDEO DOCUMENTARY + FULL POST</span>
                <time datetime="2026-11-05">05 NOV 2026</time>
              </div>
              <h2 class="post-card__title">Inside an Empetix 5-Day Product &amp; Growth Sprint (Full Video + Transcript + Photos)</h2>
              <p class="post-card__excerpt">
                Watch how our engineers, product designers, and performance marketers take a FinTech checkout product from raw problem statement to user-tested prototype and live acquisition test in five days.
              </p>
              <span class="arrow-link">
                <span>Watch Video &amp; Read Full Post</span>
                <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                  <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </span>
            </div>
          </a>
        </article>
      </div>
    </section>

    <!-- Filterable Blog Archive -->
    <section class="section section--flush-top" aria-label="Blog posts archive">
      <div class="container">
        <div class="filter" data-filter-bar="blog-archive" role="toolbar" aria-label="Filter blog posts by format and topic">
          <span class="filter__label">Filter posts:</span>
          <button type="button" class="filter__btn" data-filter="all" aria-pressed="true">All Posts<sup>6</sup></button>
          <button type="button" class="filter__btn" data-filter="video" aria-pressed="false">Video Episodes<sup>3</sup></button>
          <button type="button" class="filter__btn" data-filter="photos" aria-pressed="false">Photo Essays<sup>3</sup></button>
          <button type="button" class="filter__btn" data-filter="engineering" aria-pressed="false">Engineering<sup>3</sup></button>
          <button type="button" class="filter__btn" data-filter="marketing" aria-pressed="false">Growth Marketing<sup>3</sup></button>
          <span class="filter__count" data-filter-count>6 entries</span>
        </div>

        <div id="blog-archive" class="post-grid">
          <!-- LOOP: news-item -->
          <article class="post-card" data-category="engineering marketing photos video">
            <a href="{{ url('/blog/post') }}" class="post-card__link">
              <div class="frame post-card__frame frame--zoom">
                <img src="{{ asset('assets/img/blog/arabic-first.jpg') }}" alt="Designer sketching Arabic right-to-left interface wireframes on paper" width="800" height="533"/>
                <span class="post-card__badge">ARTICLE + VIDEO + 4 PHOTOS</span>
              </div>
              <div class="post-card__meta">
                <span class="post-card__cat">DESIGN SYSTEMS &amp; CRO</span>
                <time datetime="2026-11-14">14 NOV 2026</time>
                <span>8 MIN READ</span>
              </div>
              <h2 class="post-card__title">Engineering Arabic-First Design Systems &amp; High-Conversion Funnels</h2>
              <p class="post-card__excerpt">
                A complete engineering and conversion guide to CSS logical properties, Arabic typographic rhythm, and bilingual checkout funnels—with video walkthrough and photo gallery.
              </p>
            </a>
          </article>
          <!-- END LOOP: news-item -->

          <!-- LOOP: news-item -->
          <article class="post-card" data-category="video engineering marketing">
            <a href="{{ url('/blog/video') }}" class="post-card__link">
              <div class="frame post-card__frame frame--zoom">
                <img src="{{ asset('assets/img/blog/design-sprint.jpg') }}" alt="Team voting with dot stickers on product wireframes pinned to a wall" width="800" height="533"/>
                <span class="post-card__play" aria-hidden="true">
                  <svg viewBox="0 0 16 16" fill="currentColor"><polygon points="4,2 14,8 4,14"/></svg>
                </span>
                <span class="post-card__badge">VIDEO EPISODE &middot; 14:20</span>
              </div>
              <div class="post-card__meta">
                <span class="post-card__cat">VIDEO DOCUMENTARY</span>
                <time datetime="2026-11-05">05 NOV 2026</time>
                <span>VIDEO + TRANSCRIPT</span>
              </div>
              <h2 class="post-card__title">Inside an Empetix 5-Day Product &amp; Growth Sprint (Full Episode)</h2>
              <p class="post-card__excerpt">
                Full 14-minute studio documentary with interactive chapter timestamps, day-by-day deliverables breakdown, and workshop photography.
              </p>
            </a>
          </article>
          <!-- END LOOP: news-item -->

          <!-- LOOP: news-item -->
          <article class="post-card" data-category="photos marketing video">
            <a href="{{ url('/blog/photos') }}" class="post-card__link">
              <div class="frame post-card__frame frame--zoom">
                <img src="{{ asset('assets/img/blog/behind-the-lens.jpg') }}" alt="Barista pouring latte art on set during commercial film shoot" width="800" height="533"/>
                <span class="post-card__badge">PHOTO ESSAY &middot; 8 PHOTOS + REEL</span>
              </div>
              <div class="post-card__meta">
                <span class="post-card__cat">COMMERCIAL PRODUCTION</span>
                <time datetime="2026-10-22">22 OCT 2026</time>
                <span>LIGHTBOX GALLERY</span>
              </div>
              <h2 class="post-card__title">Behind the Lens: Directing the Qahwa Roasters Omnichannel Launch Shoot</h2>
              <p class="post-card__excerpt">
                Explore 8 high-resolution behind-the-scenes photographs, lighting diagrams, director notes, and the final commercial reel that drove +190% subscriptions.
              </p>
            </a>
          </article>
          <!-- END LOOP: news-item -->

          <!-- LOOP: news-item -->
          <article class="post-card" data-category="marketing">
            <a href="{{ url('/blog/post') }}" class="post-card__link">
              <div class="frame post-card__frame frame--zoom">
                <img src="{{ asset('assets/img/blog/performance-playbook.jpg') }}" alt="Marketing analysts reviewing attribution telemetry on dual monitors" width="800" height="533"/>
                <span class="post-card__badge">GROWTH PLAYBOOK</span>
              </div>
              <div class="post-card__meta">
                <span class="post-card__cat">PAID ACQUISITION</span>
                <time datetime="2026-10-09">09 OCT 2026</time>
                <span>11 MIN READ</span>
              </div>
              <h2 class="post-card__title">Server-Side Attribution in 2026: Wiring Laravel Events Directly to Meta CAPI &amp; Google Ads</h2>
              <p class="post-card__excerpt">
                How we replaced fragile browser pixels with deterministic backend webhooks to recover 34% of lost conversion signals and lower blended CAC.
              </p>
            </a>
          </article>
          <!-- END LOOP: news-item -->

          <!-- LOOP: news-item -->
          <article class="post-card" data-category="video engineering">
            <a href="{{ url('/blog/video') }}" class="post-card__link">
              <div class="frame post-card__frame frame--zoom">
                <img src="{{ asset('assets/img/blog/talks-engineering.jpg') }}" alt="Engineers gathered around a table during a technical architecture talk" width="800" height="533"/>
                <span class="post-card__play" aria-hidden="true">
                  <svg viewBox="0 0 16 16" fill="currentColor"><polygon points="4,2 14,8 4,14"/></svg>
                </span>
                <span class="post-card__badge">TECH TALK VIDEO &middot; 22:10</span>
              </div>
              <div class="post-card__meta">
                <span class="post-card__cat">BACKEND ENGINEERING</span>
                <time datetime="2026-09-24">24 SEP 2026</time>
                <span>VIDEO + CODE NOTES</span>
              </div>
              <h2 class="post-card__title">Zero-Downtime Ledger Migrations in Laravel: Lessons from 1.4 Million Active Wallets</h2>
              <p class="post-card__excerpt">
                CTO Mohanad Ahmed Shehata walks through our dual-write migration pattern, idempotency keys, and automated reconciliation tests.
              </p>
            </a>
          </article>
          <!-- END LOOP: news-item -->

          <!-- LOOP: news-item -->
          <article class="post-card" data-category="photos">
            <a href="{{ url('/blog/photos') }}" class="post-card__link">
              <div class="frame post-card__frame frame--zoom">
                <img src="{{ asset('assets/img/blog/studio-week.jpg') }}" alt="Two developers pair-programming at a wooden desk in soft daylight" width="800" height="533"/>
                <span class="post-card__badge">PHOTO DISPATCH &middot; 8 FRAMES</span>
              </div>
              <div class="post-card__meta">
                <span class="post-card__cat">STUDIO CULTURE</span>
                <time datetime="2026-09-10">10 SEP 2026</time>
                <span>PHOTO ESSAY</span>
              </div>
              <h2 class="post-card__title">A Week Inside Empetix Cairo: Code Reviews, Critique Walls &amp; Rooftop Suppers</h2>
              <p class="post-card__excerpt">
                An unfiltered 35mm photo journal documenting five days with our software, design, and film teams in Cairo.
              </p>
            </a>
          </article>
          <!-- END LOOP: news-item -->
        </div>
      </div>
    </section>
@endsection
