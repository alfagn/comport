@extends('layouts.public', ['activePage' => 'work'])

@section('meta')
  <meta name="description" content="How Empetix engineered NilePay's Flutter and Laravel wallet and scaled active users to 1.4M."/>
  <title>NilePay Case Study: Mobile FinTech & Growth Engine — Empetix</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="NilePay Case Study: Mobile FinTech & Growth Engine — Empetix"/>
  <meta property="og:description" content="How Empetix engineered NilePay's Flutter and Laravel wallet and scaled active users to 1.4M."/>
  <meta property="og:url" content="https://empetix.vercel.app/project-nilepay.html"/>
  <meta property="og:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:secure_url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution Logo"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="NilePay Case Study: Mobile FinTech & Growth Engine — Empetix"/>
  <meta name="twitter:description" content="How Empetix engineered NilePay's Flutter and Laravel wallet and scaled active users to 1.4M."/>
  <meta name="twitter:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
@endsection

@section('content')
<main id="main-content">


    <section class="article-head">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ url('/') }}">Home</a></li>
          <li><a href="{{ url('/work') }}">Projects</a></li>
          <li aria-current="page">NilePay FinTech</li>
        </ol>

        <div class="article-head__meta">
          <span class="article-head__cat">CASE STUDY FINTECH SOFTWARE + GROWTH</span>
          <span>CAIRO &amp; RIYADH</span>
          <span>2026</span>
        </div>

        <h1 class="article-head__title">NilePay: Engineering a 60fps mobile wallet and scaling to 1.4M active users.</h1>
        <p class="article-head__dek">
          How Empetix rebuilt NilePay&rsquo;s core checkout ledger in Laravel &amp; Flutter while running a full-funnel performance acquisition engine that cut customer acquisition cost by 41%.
        </p>

        <dl class="facts">
          <div>
            <dt>Client</dt>
            <dd>NilePay Financial Technologies</dd>
          </div>
          <div>
            <dt>Scope</dt>
            <dd>Flutter App, Laravel Ledger API, Brand Campaign &amp; Paid Growth</dd>
          </div>
          <div>
            <dt>Timeline</dt>
            <dd>16 Weeks to Launch + Ongoing Retainer</dd>
          </div>
          <div>
            <dt>Verified Outcome</dt>
            <dd>1.4M Active Wallets &middot; +310% Activation &middot; -41% CAC</dd>
          </div>
        </dl>
      </div>
    </section>

    <!-- Full-Bleed Hero Image -->
    <section class="bleed">
      <div class="container">
        <div class="frame bleed__frame bleed__frame--nilepay">
          <img src="{{ asset('assets/img/work/nilepay.jpg') }}" alt="Woman tapping smartphone on card terminal at a sunlit Cairo cafe counter" width="1600" height="900"/>
        </div>
        <div class="bleed__caption">
          <span>FIG 01 &mdash; NILEPAY CONTACTLESS MERCHANT CHECKOUT IN FIELD TESTING, ZAMALEK</span>
          <span>SHOT ON 35MM BY EMPETIX PRODUCTION CREW</span>
        </div>
      </div>
    </section>

    <!-- Audited Outcomes Strip -->
    <section class="section section--sm">
      <div class="container">
        <div class="stats">
          <div class="stat">
            <div class="stat__value">
              <span data-counter data-counter-target="310" data-counter-decimals="0">310</span>
              <span class="stat__suffix">%</span>
            </div>
            <p class="stat__label">Increase in first-transaction activation within 72 hours of install</p>
          </div>
          <div class="stat">
            <div class="stat__value">
              <span data-counter data-counter-target="1.4" data-counter-decimals="1">1.4</span>
              <span class="stat__suffix">M</span>
            </div>
            <p class="stat__label">Active monthly wallets across Egypt and Saudi Arabia</p>
          </div>
          <div class="stat">
            <div class="stat__value">
              <span data-counter data-counter-target="41" data-counter-decimals="0">41</span>
              <span class="stat__suffix">%</span>
            </div>
            <p class="stat__label">Reduction in paid customer acquisition cost (CAC)</p>
          </div>
          <div class="stat">
            <div class="stat__value">
              <span data-counter data-counter-target="180" data-counter-decimals="0">180</span>
              <span class="stat__suffix">ms</span>
            </div>
            <p class="stat__label">P95 QR &amp; NFC settlement latency on our Laravel ledger core</p>
          </div>
        </div>
      </div>
    </section>

    <!-- The Challenge & Architecture -->
    <section class="section section--ruled">
      <div class="container case-cols">
        <div class="case-cols__label">
          <span class="eyebrow">01 / The Challenge</span>
        </div>
        <div class="case-cols__body prose">
          <h2>A fast-growing FinTech held back by a 9-step onboarding flow and disconnected ad spend.</h2>
          <p>
            When NilePay approached Empetix, they were spending heavily on paid social acquisition, yet 74% of users dropped off between app install and their first wallet top-up. Their legacy hybrid app took 6.2 seconds to cold-start, and their external marketing agency had zero visibility into in-app KYC completion events.
          </p>
          <p>
            Our mandate was twofold: re-architect the mobile client and settlement API for sub-second speed, and rebuild the entire acquisition funnel around server-side verified activation events rather than raw installs.
          </p>
        </div>
      </div>
    </section>

    <!-- Video Walkthrough inside Case Study -->
    <section class="section section--alt">
      <div class="container">
        <div class="section-head">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 class="t-h3">Watch the 2-minute architecture and launch breakdown.</h2>
          </div>
        </div>

        <div class="video" data-video-player="nilepay-reel">
          <img class="video__poster" src="{{ asset('assets/img/work/nilepay-detail.jpg') }}" alt="Close-up of hands holding smartphone running NilePay over a terracotta table" width="1280" height="720"/>
          <video class="video__el" preload="metadata" poster="assets/img/work/nilepay-detail.jpg">
            <source src="{{ asset('assets/video/design-sprint.mp4') }}" type="video/mp4"/>
          </video>
          <button type="button" class="video__btn" data-video-play aria-label="Play NilePay case study video">
            <span class="video__btn-inner">
              <span class="video__btn-icon">
                <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><polygon points="4,2 14,8 4,14"/></svg>
              </span>
              <span>Play Case Study Film (02:15)</span>
            </span>
          </button>
        </div>
        <div class="video__meta">
          <span>DIRECTED &amp; ENGINEERED BY EMPETIX STUDIO</span>
          <span>AUDIO: ENGLISH WITH ARABIC SUBTITLES</span>
        </div>
      </div>
    </section>

    <!-- Engineering & Growth Execution -->
    <section class="section">
      <div class="container case-cols">
        <div class="case-cols__label">
          <span class="eyebrow">03 / Execution</span>
        </div>
        <div class="case-cols__body prose">
          <h2>From 9 onboarding screens to 90-second biometric KYC.</h2>
          <p>
            Our engineering squad replaced the legacy WebView container with a native Flutter client talking to an event-sourced Laravel 11 ledger API. By streaming OCR national-ID verification in the background while the user chooses their card skin, median KYC completion dropped from 11 minutes to 88 seconds.
          </p>
          <blockquote>
            &ldquo;Empetix is the first partner we have worked with where the engineer writing our payment webhook and the media buyer optimizing our Meta conversion API sat in the same standup.&rdquo;
            <cite>&mdash; Tarek El-Gendy, Chief Product Officer at NilePay</cite>
          </blockquote>
          <p>
            Simultaneously, our growth team wired server-side Conversions API (CAPI) signals directly from the Laravel ledger into Meta, Google, and TikTok—training ad algorithms to bid on funded wallets rather than cheap installs.
          </p>
        </div>
      </div>
    </section>

    <!-- Next Project Pager -->
    <div class="container">
      <a href="{{ url('/work') }}" class="pager">
        <span class="pager__label">Next Case Study &middot; 02</span>
        <span class="pager__title">
          <span>Dar Ceramics: Headless Commerce &amp; European Export Film</span>
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="currentColor" stroke-width="2"/></svg>
        </span>
      </a>
    </div>
@endsection
