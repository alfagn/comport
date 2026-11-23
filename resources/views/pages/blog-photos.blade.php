@extends('layouts.public', ['activePage' => 'blog'])

@section('meta')
  <meta name="description" content="Explore our behind-the-scenes photo essay with full-screen lightbox gallery, lighting breakdown, and commercial video reel."/>
  <title>Behind the Lens: Directing the Qahwa Roasters Launch Shoot (Photo Essay) — Empetix Blog</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="Behind the Lens: Directing the Qahwa Roasters Launch Shoot (Photo Essay) — Empetix Blog"/>
  <meta property="og:description" content="Explore our behind-the-scenes photo essay with full-screen lightbox gallery, lighting breakdown, and commercial video reel."/>
  <meta property="og:url" content="https://empetix.vercel.app/blog-photos.html"/>
  <meta property="og:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:secure_url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution Logo"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="Behind the Lens: Directing the Qahwa Roasters Launch Shoot (Photo Essay) — Empetix Blog"/>
  <meta name="twitter:description" content="Explore our behind-the-scenes photo essay with full-screen lightbox gallery, lighting breakdown, and commercial video reel."/>
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
          <li aria-current="page">Behind the Lens: Qahwa Roasters Shoot</li>
        </ol>

        <div class="article-head__meta">
          <span class="article-head__cat">PHOTO ESSAY &amp; CAMPAIGN PRODUCTION</span>
          <time datetime="2026-10-22">22 OCTOBER 2026</time>
          <span>6 HIGH-RES PHOTOS + COMMERCIAL VIDEO REEL</span>
        </div>

        <h1 class="article-head__title">Behind the Lens: Directing the Qahwa Roasters Omnichannel Launch Shoot</h1>
        <p class="article-head__dek">
          How our in-house film and photography unit shot 42 commercial deliverables in two days—and how pairing tactile 35mm imagery with a custom subscription checkout lifted recurring coffee orders by 190%.
        </p>
      </div>
    </header>

    <!-- Full-Width Clickable Photo Essay Mosaic -->
    <section class="section section--flush-top" aria-label="Photo essay gallery">
      <div class="container">
        <div class="gallery">
          <div class="gallery__item">
            <button type="button" class="gallery__btn" data-lightbox-src="{{ asset('assets/img/blog/shoot-1.jpg') }}" data-lightbox-caption="Frame 01 — Pouring micro-foam under a 120cm octagonal softbox at 3200K warm tungsten balance.">
              <div class="frame frame--zoom">
                <img src="{{ asset('assets/img/blog/shoot-1.jpg') }}" alt="Close-up of barista pouring latte art under warm studio light" width="1200" height="800"/>
              </div>
              <div class="gallery__caption">
                <span>FRAME HERO POUR &mdash; 50MM PRIME, F/2.0</span>
                <span>CLICK FOR FULL-SCREEN LIGHTBOX +</span>
              </div>
            </button>
          </div>

          <div class="gallery__item">
            <button type="button" class="gallery__btn" data-lightbox-src="{{ asset('assets/img/blog/shoot-2.jpg') }}" data-lightbox-caption="Frame 02 — Styling handmade ceramic vessels on raw Egyptian linen for the subscription box landing page.">
              <div class="frame frame--zoom">
                <img src="{{ asset('assets/img/blog/shoot-2.jpg') }}" alt="Handmade ceramic vessels arranged on natural linen on a wooden table" width="750" height="900"/>
              </div>
              <div class="gallery__caption">
                <span>FRAME TABLETOP SET STYLING</span>
                <span>ZOOM +</span>
              </div>
            </button>
          </div>

          <div class="gallery__item">
            <button type="button" class="gallery__btn" data-lightbox-src="{{ asset('assets/img/blog/shoot-3.jpg') }}" data-lightbox-caption="Frame 03 — Golden-hour terrace unit filming the lifestyle sequence with diffused bounce light.">
              <div class="frame frame--zoom">
                <img src="{{ asset('assets/img/blog/shoot-3.jpg') }}" alt="Film crew with camera and diffuser shooting on a terrace at sunset" width="800" height="533"/>
              </div>
              <div class="gallery__caption">
                <span>FRAME GOLDEN-HOUR EXTERIOR UNIT</span>
                <span>ZOOM +</span>
              </div>
            </button>
          </div>

          <div class="gallery__item">
            <button type="button" class="gallery__btn" data-lightbox-src="{{ asset('assets/img/work/dar-ceramics.jpg') }}" data-lightbox-caption="Frame 04 — Documenting the ceramic cup-makers who craft Qahwa's signature tasting vessels.">
              <div class="frame frame--zoom">
                <img src="{{ asset('assets/img/work/dar-ceramics.jpg') }}" alt="Artisan hands shaping clay on a pottery wheel" width="1000" height="667"/>
              </div>
              <div class="gallery__caption">
                <span>FRAME ARTISAN ORIGIN SEQUENCE</span>
                <span>CLICK FOR FULL-SCREEN LIGHTBOX +</span>
              </div>
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Production Breakdown & Commercial Video -->
    <section id="article-content" class="section section--ruled">
      <div class="container article-layout">
        <aside class="article-layout__aside">
          <div class="byline">
            <div class="byline__avatar">
              <img src="{{ asset('assets/img/team/bishoy-magdy.jpg') }}" alt="Bishoy Magdy" width="96" height="96"/>
            </div>
            <div>
              <p class="byline__name">Bishoy Magdy</p>
              <p class="byline__role">Co-Founder &amp; COO</p>
            </div>
          </div>

          <div class="aside-block">
            <p class="aside-block__title">Production Specs</p>
            <ul class="toc">
              <li>Camera: ARRI Alexa Mini LF &amp; Leica SL2</li>
              <li>Location: Downtown Cairo Roastery</li>
              <li>Deliverables: 1 Hero Film, 14 Paid Social Cuts, 28 Product Stills</li>
              <li>ROAS: 4.2x Blended in Month 1</li>
            </ul>
          </div>
        </aside>

        <article class="article-layout__body prose">
          <h2>Designing every camera frame around where it lives in the conversion funnel</h2>
          <p>
            Most commercial shoots happen in a vacuum: a production house delivers a cinematic 60-second film, and the performance marketing team is left cropping vertical cuts that don&rsquo;t fit mobile safe zones or product detail pages.
          </p>
          <p>
            Because Empetix engineered Qahwa&rsquo;s subscription e-commerce store AND directed the launch shoot, every frame on our shot list was mapped to a specific slot in the funnel:
          </p>
          <ul>
            <li><strong>Top-of-funnel 9:16 hooks:</strong> Tactile close-ups of espresso extraction and ceramic craftsmanship timed to a 1.8-second visual hook window.</li>
            <li><strong>Interactive Flavor Quiz visuals:</strong> Overhead macro plates of roast profiles integrated directly into our custom React subscription configurator.</li>
            <li><strong>Retargeting founder vignettes:</strong> Candid documentary interviews with the head roaster addressing freshness and delivery speed.</li>
          </ul>

          <h3>Watch the Director&rsquo;s Cut Commercial Reel</h3>
          <figure>
            <div class="video" data-video-player="qahwa-reel">
              <img class="video__poster" src="{{ asset('assets/img/blog/behind-the-lens.jpg') }}" alt="Qahwa Roasters commercial director's cut poster" width="1200" height="675"/>
              <video class="video__el" preload="metadata" poster="assets/img/blog/behind-the-lens.jpg">
                <source src="{{ asset('assets/video/engineering-talk.mp4') }}" type="video/mp4"/>
              </video>
              <button type="button" class="video__btn" data-video-play aria-label="Play Qahwa Roasters director's cut video">
                <span class="video__btn-inner">
                  <span class="video__btn-icon">
                    <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><polygon points="4,2 14,8 4,14"/></svg>
                  </span>
                  <span>Play Commercial Reel (01:45)</span>
                </span>
              </button>
            </div>
            <figcaption>VIDEO 01 &mdash; QAHWA ROASTERS LAUNCH FILM &amp; PAID SOCIAL MASTER CUT</figcaption>
          </figure>
        </article>
      </div>
    </section>

    <div class="container">
      <a href="{{ url('/blog/post') }}" class="pager">
        <span class="pager__label">Next Post &middot; Engineering &amp; CRO</span>
        <span class="pager__title">
          <span>Engineering Arabic-First Design Systems &amp; High-Conversion Funnels</span>
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
