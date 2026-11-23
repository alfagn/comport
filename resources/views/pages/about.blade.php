@extends('layouts.public', ['activePage' => 'about'])

@section('meta')
  <meta name="description" content="Meet the four co-founders leading Empetix's software engineering, product design, and growth marketing practices."/>
  <title>About Empetix & Meet the Founders (أصحاب المشروع) — Empetix</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="About Empetix & Meet the Founders (أصحاب المشروع) — Empetix"/>
  <meta property="og:description" content="Meet the four co-founders leading Empetix's software engineering, product design, and growth marketing practices."/>
  <meta property="og:url" content="https://empetix.vercel.app/about.html"/>
  <meta property="og:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:secure_url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution Logo"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="About Empetix & Meet the Founders (أصحاب المشروع) — Empetix"/>
  <meta name="twitter:description" content="Meet the four co-founders leading Empetix's software engineering, product design, and growth marketing practices."/>
  <meta name="twitter:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
@endsection

@section('content')
<main id="main-content">


    <section class="page-hero">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ url('/') }}">Home</a></li>
          <li aria-current="page">About &amp; Founders</li>
        </ol>
        <h1 class="page-hero__title">Built by engineers and storytellers who got tired of the hand-off gap.</h1>
        <div class="page-hero__row">
          <div class="page-hero__label">
            <span class="eyebrow">Established 2018 &middot; Cairo</span>
          </div>
          <p class="page-hero__lead">
            Empetix Solution was founded in New Cairo by three co-founders uniting executive strategy, operational excellence, and software engineering under one roof—helping ambitious businesses build technology, scale growth, and grow sustainably.
          </p>
          <div class="page-hero__aside">
            <span class="t-meta">48 SPECIALISTS &middot; 3 STUDIOS</span>
          </div>
        </div>
      </div>
    </section>

    <!-- ====================================================================
         1.5. COMPANY OVERVIEW FILM ("WHO WE ARE")
         ==================================================================== -->
    <section id="who-we-are" class="overview-film" aria-labelledby="who-we-are-heading">
      <div class="container">
        <header class="overview-film__header">
          <h2 id="who-we-are-heading" class="overview-film__title">Who we are</h2>
          <p class="overview-film__lead">
            Empetix Solution brings software engineering, growth marketing, and strategic investment together under one roof in New Cairo. Watch our 40-second overview to see how we build and scale digital businesses.
          </p>
        </header>

        <div class="overview-film__stage">
          <video
            class="overview-film__video"
            controls
            playsinline
            preload="metadata"
            poster="assets/img/home/overview-poster.jpg"
            width="1920"
            height="1080"
          >
            <source src="{{ asset('assets/video/empetix-overview.mp4') }}" type="video/mp4"/>
            Your browser does not support embedded video playback.
          </video>
        </div>

        <ul class="overview-film__pillars" aria-label="Empetix Solution core pillars">
          <li class="overview-film__pillar">
            <h3 class="overview-film__pillar-title">Software engineering</h3>
            <p class="overview-film__pillar-desc">
              Full-stack web and mobile platforms built for reliability, speed, and long-term scale.
            </p>
          </li>
          <li class="overview-film__pillar">
            <h3 class="overview-film__pillar-title">Growth marketing</h3>
            <p class="overview-film__pillar-desc">
              Engineers and marketers working as one unified team to turn clean data into customer acquisition.
            </p>
          </li>
          <li class="overview-film__pillar">
            <h3 class="overview-film__pillar-title">Strategic investment</h3>
            <p class="overview-film__pillar-desc">
              Backing bold founders with capital, senior technical leadership, and go-to-market execution.
            </p>
          </li>
        </ul>
      </div>
    </section>

    <!-- FOUNDERS / OWNERS SECTION (أصحاب المشروع) -->
    @include('partials.founders')

    <!-- OPERATING PRINCIPLES -->
    <section class="section is-inverse" aria-labelledby="principles-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="principles-heading" class="t-h2">How we work when the stakes are high.</h2>
          </div>
        </div>

        <div class="principles">
          <div class="principle reveal">
            <span class="principle__num">01</span>
            <h3 class="principle__title">Code and campaigns share one scoreboard.</h3>
            <p class="principle__text">
              We don&rsquo;t measure engineering in story points and marketing in impressions. Both teams are accountable for activation, retention, and net revenue.
            </p>
          </div>
          <div class="principle reveal">
            <span class="principle__num">02</span>
            <h3 class="principle__title">Senior principals on the tools.</h3>
            <p class="principle__text">
              We don&rsquo;t bait-and-switch with senior pitch decks and junior execution. Our founders and staff engineers write production code and direct campaigns daily.
            </p>
          </div>
          <div class="principle reveal">
            <span class="principle__num">03</span>
            <h3 class="principle__title">Arabic and English as equal first-class citizens.</h3>
            <p class="principle__text">
              We engineer bidirectional RTL/LTR interfaces and bilingual brand systems natively—never as a rushed translation layer at the end.
            </p>
          </div>
          <div class="principle reveal">
            <span class="principle__num">04</span>
            <h3 class="principle__title">Full IP ownership and clean handovers.</h3>
            <p class="principle__text">
              Every line of Laravel/React code, Figma token, ad account, and raw camera file belongs to our client, documented so your internal team can run with it.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- STUDIO TIMELINE -->
    <section class="section" aria-labelledby="timeline-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="timeline-heading" class="t-h2">Eight years of compounding craft.</h2>
          </div>
        </div>

        <div class="timeline">
          <div class="timeline__item reveal">
            <span class="timeline__year">2018</span>
            <h3 class="timeline__title">Founded in Downtown Cairo</h3>
            <p class="timeline__text">Omar, Lina, Karim, and Sara launch Empetix in a two-room studio at the Greek Campus, shipping three FinTech and e-commerce platforms in year one.</p>
          </div>
          <div class="timeline__item reveal">
            <span class="timeline__year">2021</span>
            <h3 class="timeline__title">In-House Film &amp; Performance Lab</h3>
            <p class="timeline__text">Added a dedicated commercial film crew, photo studio, and server-side attribution practice to close the loop between software launches and customer acquisition.</p>
          </div>
          <div class="timeline__item reveal">
            <span class="timeline__year">2024</span>
            <h3 class="timeline__title">Riyadh Studio &amp; London Desk</h3>
            <p class="timeline__text">Expanded on-the-ground engineering and growth squads to KAFD in Riyadh and Shoreditch in London to support GCC and European enterprise clients.</p>
          </div>
          <div class="timeline__item reveal">
            <span class="timeline__year">2026</span>
            <h3 class="timeline__title">48 Specialists &amp; 64+ Shipped Products</h3>
            <p class="timeline__text">Operating across 14 countries with audited client platforms processing over USD 1.9B in annual gross transaction volume.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- LEADERSHIP & PRACTICE HEADS DIRECTORY -->
    <section class="section section--alt section--ruled" aria-labelledby="directory-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="directory-heading" class="t-h3">Senior Principals &amp; Discipline Heads</h2>
          </div>
        </div>

        <ul class="team-list reveal">
          <li class="team-list__item"><span>Youssef Nabil</span><span class="team-list__role">Principal Laravel Architect</span></li>
          <li class="team-list__item"><span>Nourhan Shawky</span><span class="team-list__role">Head of Product Design Systems</span></li>
          <li class="team-list__item"><span>Hassan El-Kholy</span><span class="team-list__role">Staff Mobile Engineer (Flutter)</span></li>
          <li class="team-list__item"><span>Mariam Farouk</span><span class="team-list__role">Director of Paid Acquisition</span></li>
          <li class="team-list__item"><span>Ziad Mansour</span><span class="team-list__role">Director of Photography &amp; Film</span></li>
          <li class="team-list__item"><span>DinaAbdelrahman</span><span class="team-list__role">Head of CRO &amp; Analytics</span></li>
          <li class="team-list__item"><span>Mostafa Salem</span><span class="team-list__role">Lead DevOps &amp; Cloud Security</span></li>
          <li class="team-list__item"><span>Salma Hegazy</span><span class="team-list__role">Bilingual Brand &amp; Copy Director</span></li>
          <li class="team-list__item"><span>Tامر Al-Qahtani</span><span class="team-list__role">Managing Director, Riyadh Studio</span></li>
        </ul>
      </div>
    </section>
@endsection
