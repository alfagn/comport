@extends('layouts.public', ['activePage' => 'about'])

@section('meta')
  <meta name="description" content="{{ sec('about', 'copy_d75b76a6e09c') }}"/>
  <title>{{ sec('about', 'copy_a35b294e59fe') }}</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="{{ sec('about', 'copy_a35b294e59fe') }}"/>
  <meta property="og:description" content="{{ sec('about', 'copy_d75b76a6e09c') }}"/>
  <meta property="og:url" content="https://empetix.vercel.app/about.html"/>
  <meta property="og:image" content="{{ content_asset(sec('global', 'social_preview')) }}"/>
  <meta property="og:image:url" content="{{ content_asset(sec('global', 'social_preview')) }}"/>
  <meta property="og:image:secure_url" content="{{ content_asset(sec('global', 'social_preview')) }}"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="{{ sec('about', 'copy_46054f40eb9f') }}"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="{{ sec('about', 'copy_a35b294e59fe') }}"/>
  <meta name="twitter:description" content="{{ sec('about', 'copy_d75b76a6e09c') }}"/>
  <meta name="twitter:image" content="{{ content_asset(sec('global', 'social_preview')) }}"/>
@endsection

@section('content')
<main id="main-content">


    <section class="page-hero">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ url('/') }}">{{ sec('about', 'copy_66b93f927675') }}</a></li>
          <li aria-current="page">{{ sec('about', 'breadcrumb', 'About &amp; Founders') }}</li>
        </ol>
        <h1 class="page-hero__title">{{ sec('about', 'hero_title', 'Built by engineers and storytellers who got tired of the hand-off gap.') }}</h1>
        <div class="page-hero__row">
          <div class="page-hero__label">
            <span class="eyebrow">{{ sec('about', 'established', 'Established 2018 &middot; Cairo') }}</span>
          </div>
          <p class="page-hero__lead">
            {{ sec('about', 'hero_lead', 'Empetix Solution was founded in New Cairo by three co-founders uniting executive strategy, operational excellence, and software engineering under one roof—helping ambitious businesses build technology, scale growth, and grow sustainably.') }}
          </p>
          <div class="page-hero__aside">
            <span class="t-meta">{{ sec('about', 'hero_meta', '48 SPECIALISTS &middot; 3 STUDIOS') }}</span>
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
          <h2 id="who-we-are-heading" class="overview-film__title">{{ sec('about', 'who_we_are_heading', 'Who we are') }}</h2>
          <p class="overview-film__lead">
            {{ sec('about', 'overview_lead', 'Empetix Solution brings software engineering, growth marketing, and strategic investment together under one roof in New Cairo. Watch our 40-second overview to see how we build and scale digital businesses.') }}
          </p>
        </header>

        <div class="overview-film__stage">
          <video
            class="overview-film__video"
            controls
            playsinline
            preload="metadata"
            poster="{{ content_asset(sec('about', 'overview_poster', 'assets/img/home/overview-poster.jpg')) }}"
            width="1920"
            height="1080"
          >
            <source src="{{ content_asset(sec('about', 'overview_video', 'assets/video/empetix-overview.mp4')) }}" type="video/mp4"/>
            {{ sec('about', 'video_fallback', 'Your browser does not support embedded video playback.') }}
          </video>
        </div>

        <ul class="overview-film__pillars" aria-label="Empetix Solution core pillars">
          @foreach(content_list('about.pillars') as $pillar)
            <li class="overview-film__pillar">
              <h3 class="overview-film__pillar-title">{{ $pillar->title }}</h3>
              <p class="overview-film__pillar-desc">{{ $pillar->body }}</p>
            </li>
          @endforeach
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
            <h2 id="principles-heading" class="t-h2">{{ sec('about', 'principles_heading', 'How we work when the stakes are high.') }}</h2>
          </div>
        </div>

        <div class="principles">
          @foreach(content_list('about.principles') as $principle)
            <div class="principle reveal">
              <span class="principle__num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
              <h3 class="principle__title">{{ $principle->title }}</h3>
              <p class="principle__text">{{ $principle->body }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    <!-- STUDIO TIMELINE -->
    <section class="section" aria-labelledby="timeline-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="timeline-heading" class="t-h2">{{ sec('about', 'timeline_heading', 'Eight years of compounding craft.') }}</h2>
          </div>
        </div>

        <div class="timeline">
          @foreach(content_list('about.timeline') as $milestone)
            <div class="timeline__item reveal">
              <span class="timeline__year">{{ $milestone->meta }}</span>
              <h3 class="timeline__title">{{ $milestone->title }}</h3>
              <p class="timeline__text">{{ $milestone->body }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    <!-- LEADERSHIP & PRACTICE HEADS DIRECTORY -->
    <section class="section section--alt section--ruled" aria-labelledby="directory-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="directory-heading" class="t-h3">{{ sec('about', 'directory_heading', 'Senior Principals & Discipline Heads') }}</h2>
          </div>
        </div>

        <ul class="team-list reveal">
          @foreach(content_list('about.directory') as $person)
            <li class="team-list__item"><span>{{ $person->title }}</span><span class="team-list__role">{{ $person->body }}</span></li>
          @endforeach
        </ul>
      </div>
    </section>
@endsection
