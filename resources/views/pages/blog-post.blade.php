@extends('layouts.public', ['activePage' => 'blog'])

@section('meta')
  <meta name="description" content="Full article, video walkthrough, and photo gallery on building bilingual RTL/LTR design systems and high-converting checkouts."/>
  <title>Engineering Arabic-First Design Systems & High-Conversion Funnels — Empetix Blog</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="Engineering Arabic-First Design Systems & High-Conversion Funnels — Empetix Blog"/>
  <meta property="og:description" content="Full article, video walkthrough, and photo gallery on building bilingual RTL/LTR design systems and high-converting checkouts."/>
  <meta property="og:url" content="https://empetix.vercel.app/blog-post.html"/>
  <meta property="og:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:secure_url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution Logo"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="Engineering Arabic-First Design Systems & High-Conversion Funnels — Empetix Blog"/>
  <meta name="twitter:description" content="Full article, video walkthrough, and photo gallery on building bilingual RTL/LTR design systems and high-converting checkouts."/>
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
          <li aria-current="page">Arabic-First Design Systems</li>
        </ol>

        <div class="article-head__meta">
          <span class="article-head__cat">DESIGN SYSTEMS &amp; CRO</span>
          <time datetime="2026-11-14">14 NOVEMBER 2026</time>
          <span>8 MIN READ &middot; INCLUDES VIDEO &amp; 4 PHOTOS</span>
        </div>

        <h1 class="article-head__title">Engineering Arabic-First Design Systems &amp; High-Conversion Funnels</h1>
        <p class="article-head__dek">
          Why simply flipping an English interface with dir=&ldquo;rtl&rdquo; breaks reading rhythm and trust—and how our engineering and CRO teams build native bilingual platforms that convert 38% higher across MENA.
        </p>
      </div>
    </header>

    <!-- Cover Photo -->
    <div class="container article-media">
      <div class="frame frame--21x9">
        <img src="{{ asset('assets/img/blog/arabic-first.jpg') }}" alt="Empetix product designer sketching right-to-left Arabic wireframe grids on paper beside a laptop" width="1600" height="685"/>
      </div>
      <div class="bleed__caption">
        <span>PLATE 01 &mdash; SKETCHING BIDIRECTIONAL CHECKOUT FLOWS AT EMPETIX CAIRO</span>
        <span>PHOTOGRAPHED ON 35MM</span>
      </div>
    </div>

    <!-- Article Body + Sticky Sidebar -->
    <section id="article-content" class="section section--flush-top">
      <div class="container article-layout">
        <!-- Sticky Author & TOC Sidebar -->
        <aside class="article-layout__aside" aria-label="Article metadata and table of contents">
          <div class="byline">
            <div class="byline__avatar">
              <img src="{{ asset('assets/img/team/bishoy-magdy.jpg') }}" alt="Bishoy Magdy" width="96" height="96"/>
            </div>
            <div>
              <p class="byline__name">Bishoy Magdy</p>
              <p class="byline__role">Co-Founder &amp; Chief Operating Officer</p>
            </div>
          </div>

          <div class="aside-block">
            <p class="aside-block__title">In This Post</p>
            <ol class="toc">
              <li><a href="#sec-optical">01. Optical Rhythm vs. Mirroring</a></li>
              <li><a href="#sec-logical">02. CSS Logical Properties Architecture</a></li>
              <li><a href="#sec-video">03. Video Walkthrough (06:40)</a></li>
              <li><a href="#sec-gallery">04. Studio Photo Gallery (4 Photos)</a></li>
              <li><a href="#sec-cro">05. Checkout Conversion Telemetry</a></li>
            </ol>
          </div>

          <div class="aside-block">
            <p class="aside-block__title">Share Article</p>
            <div class="share">
              <button type="button" data-copy-url>Copy Link</button>
              <a href="https://www.linkedin.com" rel="noopener noreferrer">LinkedIn</a>
            </div>
          </div>
        </aside>

        <!-- Full Article Prose -->
        <article class="article-layout__body prose">
          <h2 id="sec-optical">01. Optical rhythm is not a mirror image</h2>
          <p>
            Across Egypt, Saudi Arabia, and the UAE, more than 78% of mobile commerce traffic interacts with bilingual products. Yet most software teams treat Arabic as a late-stage localization ticket: they design in English with tight negative letter-spacing, small uppercase eyebrows, and 1.1 line-height, then flip the DOM direction two weeks before launch.
          </p>
          <p>
            The result looks immediately foreign to native Arabic readers. Arabic script connects cursively along a baseline with taller ascenders and deeper descenders (`ك`, `ل`, `ي`, `ح`). Applying negative letter-spacing (`letter-spacing: -0.04em`) fractures ligatures in Chromium and WebKit, while cramped line-heights clip vowel marks and diacritics.
          </p>

          <blockquote>
            &ldquo;When an Arabic checkout screen feels like a translated foreign template, user trust drops at the exact moment you ask for a credit card or national ID.&rdquo;
            <cite>&mdash; Bishoy Magdy, Chief Design Officer at Empetix</cite>
          </blockquote>

          <h2 id="sec-logical">02. One stylesheet using 100% CSS Logical Properties</h2>
          <p>
            At Empetix, we never maintain a separate `rtl.css` override file. Maintaining two stylesheets guarantees visual drift within three sprints. Instead, our design token layer in `variables.css` and every component rule use native CSS Logical Properties:
          </p>
          <ul>
            <li><strong>Inline flow instead of left/right:</strong> `margin-inline`, `padding-inline-start`, `inset-inline-end`, and `border-inline-start` automatically flip when `dir="rtl"` is toggled on `&lt;html&gt;`.</li>
            <li><strong>Direction-scoped typography tokens:</strong> Under `[dir="rtl"]`, `--tracking-display` and `--tracking-caps` reset to `0`, while line-height expands by 12% to give Arabic script room to breathe.</li>
            <li><strong>Tabular numerals (`tabular-nums`):</strong> Pricing tables, OTP codes, and IBAN fields retain consistent numeral alignment across both directions.</li>
          </ul>

          <!-- Embedded Video inside Blog Post -->
          <h2 id="sec-video">03. Watch the live engineering &amp; CRO walkthrough</h2>
          <p>
            In the 6-minute studio recording below, Bishoy Magdy and Principal Engineer Youssef Nabil walk through the actual Figma tokens, CSS logical grid, and checkout heatmap telemetry from our NilePay and Dar Ceramics launches:
          </p>

          <figure>
            <div class="video" data-video-player="post-video-1">
              <img class="video__poster" src="{{ asset('assets/img/blog/design-sprint.jpg') }}" alt="Empetix design system workshop video preview" width="1200" height="675"/>
              <video class="video__el" preload="metadata" poster="assets/img/blog/design-sprint.jpg">
                <source src="{{ asset('assets/video/design-sprint.mp4') }}" type="video/mp4"/>
              </video>
              <button type="button" class="video__btn" data-video-play aria-label="Play design system walkthrough video">
                <span class="video__btn-inner">
                  <span class="video__btn-icon">
                    <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><polygon points="4,2 14,8 4,14"/></svg>
                  </span>
                  <span>Watch Studio Walkthrough (06:40)</span>
                </span>
              </button>
            </div>
            <figcaption>VIDEO 01 &mdash; LIVE COMPARISON OF LTR/RTL CHECKOUT TELEMETRY &amp; CSS LOGICAL TOKENS</figcaption>
          </figure>

          <!-- Interactive Photo Gallery inside Blog Post -->
          <h2 id="sec-gallery">04. Studio photo gallery (Click any photo to zoom)</h2>
          <p>
            Photographs from our Cairo studio wall critique and usability testing sessions across Arabic and English mobile viewports:
          </p>

          <div class="gallery" aria-label="Article photo gallery">
            <div class="gallery__item">
              <button type="button" class="gallery__btn" data-lightbox-src="{{ asset('assets/img/blog/arabic-first.jpg') }}" data-lightbox-caption="Fig 01 — Sketching right-to-left typographic hierarchy and baseline grids on paper.">
                <div class="frame frame--zoom">
                  <img src="{{ asset('assets/img/blog/arabic-first.jpg') }}" alt="Sketching right-to-left typographic hierarchy on paper" width="900" height="600"/>
                </div>
                <div class="gallery__caption">
                  <span>FIG RTL WIREFRAME STUDY</span>
                  <span>CLICK TO ENLARGE +</span>
                </div>
              </button>
            </div>

            <div class="gallery__item">
              <button type="button" class="gallery__btn" data-lightbox-src="{{ asset('assets/img/blog/design-sprint.jpg') }}" data-lightbox-caption="Fig 02 — Product designers and growth engineers voting on checkout friction points.">
                <div class="frame frame--zoom">
                  <img src="{{ asset('assets/img/blog/design-sprint.jpg') }}" alt="Team voting on checkout wireframes pinned to studio wall" width="600" height="750"/>
                </div>
                <div class="gallery__caption">
                  <span>FIG CRITIQUE WALL</span>
                  <span>ZOOM +</span>
                </div>
              </button>
            </div>

            <div class="gallery__item">
              <button type="button" class="gallery__btn" data-lightbox-src="{{ asset('assets/img/work/nilepay-detail.jpg') }}" data-lightbox-caption="Fig 03 — Field usability testing of one-handed thumb reach on Arabic mobile checkout.">
                <div class="frame frame--zoom">
                  <img src="{{ asset('assets/img/work/nilepay-detail.jpg') }}" alt="Hands testing mobile app on terracotta table" width="750" height="500"/>
                </div>
                <div class="gallery__caption">
                  <span>FIG MOBILE THUMB-ZONE TEST</span>
                  <span>ZOOM +</span>
                </div>
              </button>
            </div>

            <div class="gallery__item">
              <button type="button" class="gallery__btn" data-lightbox-src="{{ asset('assets/img/home/studio.jpg') }}" data-lightbox-caption="Fig 04 — Pair-programming the CSS logical token architecture and automated visual regression tests.">
                <div class="frame frame--zoom">
                  <img src="{{ asset('assets/img/home/studio.jpg') }}" alt="Two developers pair-programming at wooden desk" width="900" height="600"/>
                </div>
                <div class="gallery__caption">
                  <span>FIG PAIR-PROGRAMMING TOKENS</span>
                  <span>CLICK TO ENLARGE +</span>
                </div>
              </button>
            </div>
          </div>

          <h2 id="sec-cro">05. What happened to conversion rates after launch</h2>
          <p>
            Across four ecommerce and FinTech platforms where we replaced auto-translated RTL stylesheets with our native Arabic-first component architecture and micro-copy:
          </p>
          <ol>
            <li><strong>+38.4% mobile checkout completion</strong> among Arabic-language sessions in Egypt and Saudi Arabia.</li>
            <li><strong>-29% form validation errors</strong> on phone number, address, and payment card inputs by keeping numeric inputs LTR-isolated inside RTL forms.</li>
            <li><strong>Zero duplicate CSS maintenance</strong> for the client&rsquo;s internal engineering team post-handover.</li>
          </ol>

          <!-- Author Card -->
          <div class="author-card">
            <div class="author-card__img">
              <img src="{{ asset('assets/img/team/bishoy-magdy.jpg') }}" alt="Bishoy Magdy" width="160" height="160"/>
            </div>
            <div>
              <p class="t-meta">WRITTEN BY</p>
              <h3 class="author-card__name">Bishoy Magdy &mdash; Co-Founder &amp; Chief Operating Officer</h3>
              <p class="author-card__bio">
                Bishoy leads studio operations, product delivery, and cross-functional design and growth execution at Empetix Solution. <a href="{{ url('/founders/bishoy') }}">View profile</a>.
              </p>
            </div>
          </div>
        </article>
      </div>
    </section>

    <!-- Related Posts -->
    <section class="section section--alt section--ruled" aria-labelledby="related-heading">
      <div class="container">
        <div class="section-head">
          <div class="section-head__label">
            <span class="eyebrow">Continue Reading</span>
          </div>
          <div class="section-head__title">
            <h2 id="related-heading" class="t-h3">More from the Empetix Journal</h2>
          </div>
        </div>

        <div class="post-rows">
          <article class="post-row">
            <a href="{{ url('/blog/video') }}" class="post-row__link">
              <div class="post-row__meta">
                <span class="post-card__cat">VIDEO EPISODE &middot; 14:20</span>
                <time datetime="2026-11-05">05 NOV 2026</time>
              </div>
              <h3 class="post-row__title">Inside an Empetix 5-Day Product &amp; Growth Sprint (Watch Full Video + Transcript)</h3>
              <div class="frame post-row__thumb">
                <img src="{{ asset('assets/img/blog/design-sprint.jpg') }}" alt="Design sprint workshop" width="240" height="160"/>
              </div>
            </a>
          </article>

          <article class="post-row">
            <a href="{{ url('/blog/photos') }}" class="post-row__link">
              <div class="post-row__meta">
                <span class="post-card__cat">PHOTO ESSAY &middot; 8 PHOTOS</span>
                <time datetime="2026-10-22">22 OCT 2026</time>
              </div>
              <h3 class="post-row__title">Behind the Lens: Directing the Qahwa Roasters Omnichannel Launch Campaign</h3>
              <div class="frame post-row__thumb">
                <img src="{{ asset('assets/img/blog/behind-the-lens.jpg') }}" alt="Commercial coffee shoot" width="240" height="160"/>
              </div>
            </a>
          </article>
        </div>
      </div>
    </section>

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
