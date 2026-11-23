@extends('layouts.public', ['activePage' => 'team'])

@section('meta')
  <meta name="description" content="Bishoy Magdy, Co-founder and chief operating officer at Empetix Solution."/>
  <title>Bishoy Magdy — Empetix Solution</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="Bishoy Magdy — Empetix Solution"/>
  <meta property="og:description" content="Bishoy Magdy, Co-founder and chief operating officer at Empetix Solution."/>
  <meta property="og:url" content="https://empetix.vercel.app/founder-bishoy.html"/>
  <meta property="og:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:secure_url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution Logo"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="Bishoy Magdy — Empetix Solution"/>
  <meta name="twitter:description" content="Bishoy Magdy, Co-founder and chief operating officer at Empetix Solution."/>
  <meta name="twitter:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
@endsection

@section('content')
<!-- Breadcrumb & Hero -->
    <section class="founder-band founder-band--white founder-hero-band" aria-labelledby="founder-name">
      <div class="founder-container">
        <nav class="founder-crumb" aria-label="Breadcrumb">
          <ol class="founder-crumb__list">
            <li class="founder-crumb__item"><a href="{{ url('/') }}" class="founder-crumb__link">Home</a></li>
            <li class="founder-crumb__item"><a href="{{ url('/team') }}" class="founder-crumb__link">Team</a></li>
            <li class="founder-crumb__item" aria-current="page">Bishoy Magdy</li>
          </ol>
        </nav>

        <div class="founder-hero">
          <div class="founder-hero__media">
            <img
              class="founder-hero__photo"
              src="{{ asset('assets/img/team/bishoy-magdy.jpg') }}"
              alt="Portrait of Bishoy Magdy, Co-founder and chief operating officer"
              width="800"
              height="1000"
            />
          </div>

          <div class="founder-hero__body">
            <h1 id="founder-name" class="founder-hero__name">Bishoy Magdy</h1>
            <p class="founder-hero__title">Co-founder and chief operating officer</p>
            <p class="founder-hero__lead">
              Bishoy leads company operations, delivery governance, and cross-functional execution at Empetix Solution. He connects software squads, marketing teams, and client stakeholders so projects ship on schedule and scale smoothly.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- About -->
    <section class="founder-band founder-band--surface" aria-labelledby="about-heading">
      <div class="founder-container">
        <div class="founder-about">
          <h2 id="about-heading" class="founder-heading">About Bishoy</h2>
          <div class="founder-about__body">
            <p class="founder-about__copy">
              As chief operating officer, Bishoy oversees day-to-day operations, squad planning, and execution standards across Empetix Solution&rsquo;s engineering, product design, and growth marketing practices in New Cairo.
            </p>
            <p class="founder-about__copy">
              He builds clear operating rhythms between technical teams and commercial specialists so clients experience transparent communication, predictable milestones, and dependable delivery from kickoff to launch.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Expertise -->
    <section class="founder-band founder-band--white" aria-labelledby="expertise-heading">
      <div class="founder-container">
        <h2 id="expertise-heading" class="founder-heading">Where Bishoy can help</h2>
        <ul class="founder-expertise">
          <!-- LOOP: expertise-item -->
          <li class="founder-expertise__item">
            <h3 class="founder-expertise__title">Delivery governance and execution</h3>
            <p class="founder-expertise__desc">
              Running disciplined sprint cadences and milestone reviews that keep complex engineering and growth launches on schedule.
            </p>
          </li>
          <!-- END LOOP: expertise-item -->

          <li class="founder-expertise__item">
            <h3 class="founder-expertise__title">Cross-functional squad operations</h3>
            <p class="founder-expertise__desc">
              Coordinating software engineers, designers, and performance marketers around one shared product roadmap.
            </p>
          </li>

          <li class="founder-expertise__item">
            <h3 class="founder-expertise__title">Client onboarding and account health</h3>
            <p class="founder-expertise__desc">
              Establishing transparent reporting, clear escalation paths, and sustained operational alignment with partner teams.
            </p>
          </li>

          <li class="founder-expertise__item">
            <h3 class="founder-expertise__title">Scaling studio processes</h3>
            <p class="founder-expertise__desc">
              Building repeatable workflows, hiring standards, and quality benchmarks that protect craft as teams expand.
            </p>
          </li>
        </ul>
      </div>
    </section>

    <!-- Selected Work -->
    <section class="founder-band founder-band--surface" aria-labelledby="work-heading">
      <div class="founder-container">
        <h2 id="work-heading" class="founder-heading">Selected work</h2>
        <ul class="founder-projects">
          <!-- LOOP: project -->
          <li class="founder-projects__item">
            <article class="founder-project">
              <div class="founder-project__media">
                <img
                  class="founder-project__photo"
                  src="{{ asset('assets/img/work/dar-ceramics-detail.jpg') }}"
                  alt="Ceramicist arranging handmade stoneware pieces under studio lighting for Dar Ceramics"
                  width="1376"
                  height="768"
                  loading="lazy"
                />
              </div>
              <h3 class="founder-project__client">Dar Ceramics</h3>
              <p class="founder-project__outcome">
                Coordinated bilingual storefront engineering, studio production, and cross-border launch operations for European expansion.
              </p>
              <a href="{{ url('/work') }}" class="founder-text-link">Read the case study</a>
            </article>
          </li>
          <!-- END LOOP: project -->

          <li class="founder-projects__item">
            <article class="founder-project">
              <div class="founder-project__media">
                <img
                  class="founder-project__photo"
                  src="{{ asset('assets/img/work/qahwa.jpg') }}"
                  alt="Barista pouring milk into espresso during the Qahwa Roasters commercial production shoot"
                  width="1264"
                  height="848"
                  loading="lazy"
                />
              </div>
              <h3 class="founder-project__client">Qahwa Roasters</h3>
              <p class="founder-project__outcome">
                Unified e-commerce subscription delivery, commercial production timelines, and paid acquisition rollout under one launch plan.
              </p>
              <a href="{{ url('/blog/photos') }}" class="founder-text-link">Read the case study</a>
            </article>
          </li>
        </ul>
      </div>
    </section>

    <!-- Other Founders -->
    <section class="founder-band founder-band--white" aria-labelledby="other-founders-heading">
      <div class="founder-container">
        <header class="founder-others__header">
          <h2 id="other-founders-heading" class="founder-heading">Other founders</h2>
          <a href="{{ url('/team') }}" class="founder-text-link">Meet the full team</a>
        </header>

        <ul class="founder-others__list">
          <!-- LOOP: founder -->
          <li class="founder-others__item">
            <a href="{{ url('/founders/ayman') }}" class="founder-others__link">
              <div class="founder-others__media">
                <img
                  class="founder-others__photo"
                  src="{{ asset('assets/img/team/ayman-ibrahim.jpg') }}"
                  alt="Portrait of Ayman Ibrahim, Co-founder and chief executive officer"
                  width="800"
                  height="1000"
                  loading="lazy"
                />
              </div>
              <h3 class="founder-others__name">Ayman Ibrahim</h3>
              <p class="founder-others__role">Co-founder and chief executive officer</p>
            </a>
          </li>
          <!-- END LOOP: founder -->

          <li class="founder-others__item">
            <a href="{{ url('/founders/mohanad') }}" class="founder-others__link">
              <div class="founder-others__media">
                <img
                  class="founder-others__photo"
                  src="{{ asset('assets/img/team/mohanad-shehata.jpg') }}"
                  alt="Portrait of Mohanad Ahmed Shehata, Co-founder and chief technology officer"
                  width="800"
                  height="1000"
                  loading="lazy"
                />
              </div>
              <h3 class="founder-others__name">Mohanad Ahmed Shehata</h3>
              <p class="founder-others__role">Co-founder and chief technology officer</p>
            </a>
          </li>
        </ul>
      </div>
    </section>
@endsection
