@extends('layouts.public', ['activePage' => 'team'])

@section('meta')
  <meta name="description" content="Ayman Ibrahim, Co-founder and chief executive officer at Empetix Solution."/>
  <title>Ayman Ibrahim — Empetix Solution</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="Ayman Ibrahim — Empetix Solution"/>
  <meta property="og:description" content="Ayman Ibrahim, Co-founder and chief executive officer at Empetix Solution."/>
  <meta property="og:url" content="https://empetix.vercel.app/founder-ayman.html"/>
  <meta property="og:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:secure_url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution Logo"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="Ayman Ibrahim — Empetix Solution"/>
  <meta name="twitter:description" content="Ayman Ibrahim, Co-founder and chief executive officer at Empetix Solution."/>
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
            <li class="founder-crumb__item" aria-current="page">Ayman Ibrahim</li>
          </ol>
        </nav>

        <div class="founder-hero">
          <div class="founder-hero__media">
            <img
              class="founder-hero__photo"
              src="{{ asset('assets/img/team/ayman-ibrahim.jpg') }}"
              alt="Portrait of Ayman Ibrahim, Co-founder and chief executive officer"
              width="800"
              height="1000"
            />
          </div>

          <div class="founder-hero__body">
            <h1 id="founder-name" class="founder-hero__name">Ayman Ibrahim</h1>
            <p class="founder-hero__title">Co-founder and chief executive officer</p>
            <p class="founder-hero__lead">
              Ayman leads company vision, strategic partnerships, and investment direction at Empetix Solution. He works directly with founders and executive teams to align software engineering, growth marketing, and capital around durable business outcomes.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- About -->
    <section class="founder-band founder-band--surface" aria-labelledby="about-heading">
      <div class="founder-container">
        <div class="founder-about">
          <h2 id="about-heading" class="founder-heading">About Ayman</h2>
          <div class="founder-about__body">
            <p class="founder-about__copy">
              As chief executive officer, Ayman oversees Empetix Solution&rsquo;s strategic direction across enterprise product delivery, growth partnerships, and venture investments from New Cairo.
            </p>
            <p class="founder-about__copy">
              He stays closely involved through executive discovery and governance so every software build and commercial expansion stays grounded in clear unit economics and long-term sustainability.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Expertise -->
    <section class="founder-band founder-band--white" aria-labelledby="expertise-heading">
      <div class="founder-container">
        <h2 id="expertise-heading" class="founder-heading">Where Ayman can help</h2>
        <ul class="founder-expertise">
          <!-- LOOP: expertise-item -->
          <li class="founder-expertise__item">
            <h3 class="founder-expertise__title">Executive strategy and partnerships</h3>
            <p class="founder-expertise__desc">
              Aligning technology investments, commercial goals, and multi-phase roadmaps for enterprise and growth-stage partners.
            </p>
          </li>
          <!-- END LOOP: expertise-item -->

          <li class="founder-expertise__item">
            <h3 class="founder-expertise__title">Strategic venture investment</h3>
            <p class="founder-expertise__desc">
              Backing ambitious founders with capital, senior technical leadership, and go-to-market execution under one roof.
            </p>
          </li>

          <li class="founder-expertise__item">
            <h3 class="founder-expertise__title">Commercial scoping and governance</h3>
            <p class="founder-expertise__desc">
              Turning complex business objectives into structured scopes, realistic timelines, and clear accountability.
            </p>
          </li>

          <li class="founder-expertise__item">
            <h3 class="founder-expertise__title">Enterprise client advisory</h3>
            <p class="founder-expertise__desc">
              Guiding executive leadership teams through digital transformation, platform launches, and market expansion.
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
                  src="{{ asset('assets/img/work/nilepay-detail.jpg') }}"
                  alt="Hands holding a smartphone running the NilePay mobile wallet application"
                  width="1376"
                  height="768"
                  loading="lazy"
                />
              </div>
              <h3 class="founder-project__client">NilePay</h3>
              <p class="founder-project__outcome">
                Rebuilt the mobile checkout experience and settlement backend to support everyday wallet payments across Egypt.
              </p>
              <a href="{{ url('/work/nilepay') }}" class="founder-text-link">Read the case study</a>
            </article>
          </li>
          <!-- END LOOP: project -->

          <li class="founder-projects__item">
            <article class="founder-project">
              <div class="founder-project__media">
                <img
                  class="founder-project__photo"
                  src="{{ asset('assets/img/work/cargoline.jpg') }}"
                  alt="Logistics dispatchers reviewing live transport routes across wall monitors at CargoLine MENA"
                  width="1376"
                  height="768"
                  loading="lazy"
                />
              </div>
              <h3 class="founder-project__client">CargoLine MENA</h3>
              <p class="founder-project__outcome">
                Delivered a real-time fleet dispatch and route monitoring platform unifying regional logistics operations in one control tower.
              </p>
              <a href="{{ url('/work') }}" class="founder-text-link">Read the case study</a>
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
            <a href="{{ url('/founders/bishoy') }}" class="founder-others__link">
              <div class="founder-others__media">
                <img
                  class="founder-others__photo"
                  src="{{ asset('assets/img/team/bishoy-magdy.jpg') }}"
                  alt="Portrait of Bishoy Magdy, Co-founder and chief operating officer"
                  width="800"
                  height="1000"
                  loading="lazy"
                />
              </div>
              <h3 class="founder-others__name">Bishoy Magdy</h3>
              <p class="founder-others__role">Co-founder and chief operating officer</p>
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
