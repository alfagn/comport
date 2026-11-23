@extends('layouts.public', ['activePage' => 'work'])

@section('meta')
  <meta name="description" content="Explore software engineering, FinTech, e-commerce, and performance marketing case studies by Empetix."/>
  <title>Selected Projects & Case Studies — Empetix</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="Selected Projects & Case Studies — Empetix"/>
  <meta property="og:description" content="Explore software engineering, FinTech, e-commerce, and performance marketing case studies by Empetix."/>
  <meta property="og:url" content="https://empetix.vercel.app/work.html"/>
  <meta property="og:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:secure_url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution Logo"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="Selected Projects & Case Studies — Empetix"/>
  <meta name="twitter:description" content="Explore software engineering, FinTech, e-commerce, and performance marketing case studies by Empetix."/>
  <meta name="twitter:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
@endsection

@section('content')
<main id="main-content">


    <section class="page-hero">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ url('/') }}">Home</a></li>
          <li aria-current="page">Projects</li>
        </ol>
        <h1 class="page-hero__title">Selected software &amp; growth case studies.</h1>
        <div class="page-hero__row">
          <div class="page-hero__label">
            <span class="eyebrow">Portfolio 2023&ndash;2026</span>
          </div>
          <p class="page-hero__lead">
            Every project below pairs production software engineering with measurable commercial growth. Click any project to inspect the full architecture, creative assets, and business outcomes.
          </p>
          <div class="page-hero__aside">
            <span class="t-meta">6 FEATURED DOSSIERS</span>
          </div>
        </div>
      </div>
    </section>

    <section class="section section--flush-top" aria-label="Project archive">
      <div class="container">
        <!-- Interactive Filter Bar -->
        <div class="filter" data-filter-bar="work-archive" role="toolbar" aria-label="Filter projects by discipline">
          <span class="filter__label">Filter by discipline:</span>
          <button type="button" class="filter__btn" data-filter="all" aria-pressed="true">All Projects<sup>6</sup></button>
          <button type="button" class="filter__btn" data-filter="software" aria-pressed="false">Software Engineering<sup>4</sup></button>
          <button type="button" class="filter__btn" data-filter="marketing" aria-pressed="false">Growth &amp; Marketing<sup>4</sup></button>
          <button type="button" class="filter__btn" data-filter="commerce" aria-pressed="false">FinTech &amp; Commerce<sup>3</sup></button>
          <span class="filter__count" data-filter-count>6 entries</span>
        </div>

        <div id="work-archive" class="work-grid">
          <!-- LOOP: project-item -->
          <article class="project-card project-card--wide" data-category="software marketing commerce">
            <a href="{{ url('/work/nilepay') }}" class="project-card__link">
              <div class="frame project-card__frame frame--zoom">
                <img src="{{ asset('assets/img/work/nilepay.jpg') }}" alt="Woman tapping smartphone on card terminal at a Cairo cafe" width="1200" height="750"/>
              </div>
              <div class="project-card__body">
                <span class="project-card__client">NILEPAY FINTECH</span>
                <span class="project-card__year">2026</span>
                <h2 class="project-card__title">
                  <span>Mobile wallet architecture, instant merchant settlement &amp; nationwide launch campaign (1.4M users).</span>
                </h2>
                <ul class="tag-list">
                  <li class="tag">Flutter &amp; Laravel</li>
                  <li class="tag">Performance Acquisition</li>
                  <li class="tag">+310% Activation</li>
                </ul>
              </div>
            </a>
          </article>
          <!-- END LOOP: project-item -->

          <!-- LOOP: project-item -->
          <article class="project-card project-card--narrow project-card--offset" data-category="software marketing commerce">
            <a href="{{ url('/work/nilepay') }}" class="project-card__link">
              <div class="frame project-card__frame frame--zoom">
                <img src="{{ asset('assets/img/work/dar-ceramics.jpg') }}" alt="Potter shaping clay vessel in a sunlit workshop" width="900" height="675"/>
              </div>
              <div class="project-card__body">
                <span class="project-card__client">DAR CERAMICS</span>
                <span class="project-card__year">2026</span>
                <h2 class="project-card__title">
                  <span>Headless D2C commerce platform, table-setting visualizer &amp; European paid social engine (4.6x ROAS).</span>
                </h2>
                <ul class="tag-list">
                  <li class="tag">Headless Shopify Plus</li>
                  <li class="tag">Brand Film &amp; Paid Social</li>
                </ul>
              </div>
            </a>
          </article>
          <!-- END LOOP: project-item -->

          <!-- LOOP: project-item -->
          <article class="project-card project-card--full" data-category="software">
            <a href="{{ url('/work/nilepay') }}" class="project-card__link">
              <div class="frame project-card__frame frame--zoom">
                <img src="{{ asset('assets/img/work/cargoline.jpg') }}" alt="Logistics operators monitoring fleet routes in a control room" width="1600" height="700"/>
              </div>
              <div class="project-card__body">
                <span class="project-card__client">CARGOLINE MENA</span>
                <span class="project-card__year">2025</span>
                <h2 class="project-card__title">
                  <span>Real-time fleet dispatch control tower, driver Android telemetry &amp; automated customs brokerage for 4,200 trucks.</span>
                </h2>
                <ul class="tag-list">
                  <li class="tag">Laravel &amp; WebSockets</li>
                  <li class="tag">Fleet Telemetry</li>
                  <li class="tag">-24% Empty Miles</li>
                </ul>
              </div>
            </a>
          </article>
          <!-- END LOOP: project-item -->

          <!-- LOOP: project-item -->
          <article class="project-card project-card--half" data-category="software">
            <a href="{{ url('/work/nilepay') }}" class="project-card__link">
              <div class="frame project-card__frame frame--zoom">
                <img src="{{ asset('assets/img/work/medlink.jpg') }}" alt="Designers reviewing healthcare wireframes pinned to a studio wall" width="1000" height="750"/>
              </div>
              <div class="project-card__body">
                <span class="project-card__client">MEDLINK HEALTH</span>
                <span class="project-card__year">2025</span>
                <h2 class="project-card__title">
                  <span>Telemedicine video platform, e-prescription vault &amp; patient lifecycle retention across 14 clinics.</span>
                </h2>
                <ul class="tag-list">
                  <li class="tag">WebRTC Video Engine</li>
                  <li class="tag">Patient CRM</li>
                </ul>
              </div>
            </a>
          </article>
          <!-- END LOOP: project-item -->

          <!-- LOOP: project-item -->
          <article class="project-card project-card--half" data-category="marketing">
            <a href="{{ url('/work/nilepay') }}" class="project-card__link">
              <div class="frame project-card__frame frame--zoom">
                <img src="{{ asset('assets/img/work/palm-residences.jpg') }}" alt="Film crew shooting a family on a terrace at sunset" width="1000" height="750"/>
              </div>
              <div class="project-card__body">
                <span class="project-card__client">PALM RESIDENCES</span>
                <span class="project-card__year">2025</span>
                <h2 class="project-card__title">
                  <span>Interactive 3D masterplan portal, commercial film campaign &amp; high-intent lead engine (USD 84M sales).</span>
                </h2>
                <ul class="tag-list">
                  <li class="tag">Demand Generation</li>
                  <li class="tag">Sales Portal &amp; CRM</li>
                </ul>
              </div>
            </a>
          </article>
          <!-- END LOOP: project-item -->

          <!-- LOOP: project-item -->
          <article class="project-card project-card--wide" data-category="marketing commerce">
            <a href="{{ url('/blog/photos') }}" class="project-card__link">
              <div class="frame project-card__frame frame--zoom">
                <img src="{{ asset('assets/img/work/qahwa.jpg') }}" alt="Barista pouring latte art during a commercial studio shoot" width="1200" height="750"/>
              </div>
              <div class="project-card__body">
                <span class="project-card__client">QAHWA SPECIALTY ROASTERS</span>
                <span class="project-card__year">2024</span>
                <h2 class="project-card__title">
                  <span>Subscription coffee e-commerce store, packaging rebrand &amp; commercial film/photo launch.</span>
                </h2>
                <ul class="tag-list">
                  <li class="tag">Subscription Commerce</li>
                  <li class="tag">Film &amp; Photography</li>
                  <li class="tag">+190% Recurring Orders</li>
                </ul>
              </div>
            </a>
          </article>
          <!-- END LOOP: project-item -->
        </div>
      </div>
    </section>
@endsection
