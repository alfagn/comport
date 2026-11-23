@extends('layouts.public', ['activePage' => 'team'])

@section('meta')
  <meta name="description" content="Mohanad Ahmed Shehata, Co-founder and chief technology officer at Empetix Solution."/>
  <title>Mohanad Ahmed Shehata — Empetix Solution</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="Mohanad Ahmed Shehata — Empetix Solution"/>
  <meta property="og:description" content="Mohanad Ahmed Shehata, Co-founder and chief technology officer at Empetix Solution."/>
  <meta property="og:url" content="https://empetix.vercel.app/founder-mohanad.html"/>
  <meta property="og:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:secure_url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution Logo"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="Mohanad Ahmed Shehata — Empetix Solution"/>
  <meta name="twitter:description" content="Mohanad Ahmed Shehata, Co-founder and chief technology officer at Empetix Solution."/>
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
            <li class="founder-crumb__item" aria-current="page">Mohanad Ahmed Shehata</li>
          </ol>
        </nav>

        <div class="founder-hero">
          <div class="founder-hero__media">
            <img
              class="founder-hero__photo"
              src="{{ asset('assets/img/team/mohanad-shehata.jpg') }}"
              alt="Portrait of Mohanad Ahmed Shehata, Co-founder and chief technology officer"
              width="800"
              height="1000"
            />
          </div>

          <div class="founder-hero__body">
            <h1 id="founder-name" class="founder-hero__name">Mohanad Ahmed Shehata</h1>
            <p class="founder-hero__title">Co-founder and chief technology officer</p>
            <p class="founder-hero__lead">
              Mohanad leads software architecture, engineering standards, and technical infrastructure at Empetix Solution. He guides web, mobile, and backend teams to build fast, secure, and maintainable platforms engineered for scale.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- About -->
    <section class="founder-band founder-band--surface" aria-labelledby="about-heading">
      <div class="founder-container">
        <div class="founder-about">
          <h2 id="about-heading" class="founder-heading">About Mohanad</h2>
          <div class="founder-about__body">
            <p class="founder-about__copy">
              As chief technology officer, Mohanad directs Empetix Solution&rsquo;s engineering practice across custom web platforms, mobile applications, backend APIs, and cloud infrastructure from New Cairo.
            </p>
            <p class="founder-about__copy">
              He reviews core system blueprints, database designs, and deployment pipelines hands-on so production software stays resilient under real user load and straightforward for teams to evolve.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Expertise -->
    <section class="founder-band founder-band--white" aria-labelledby="expertise-heading">
      <div class="founder-container">
        <h2 id="expertise-heading" class="founder-heading">Where Mohanad can help</h2>
        <ul class="founder-expertise">
          <!-- LOOP: expertise-item -->
          <li class="founder-expertise__item">
            <h3 class="founder-expertise__title">Web and mobile platform architecture</h3>
            <p class="founder-expertise__desc">
              Designing full-stack systems across Laravel, modern web frameworks, and native-grade mobile applications.
            </p>
          </li>
          <!-- END LOOP: expertise-item -->

          <li class="founder-expertise__item">
            <h3 class="founder-expertise__title">Scalable APIs and backend systems</h3>
            <p class="founder-expertise__desc">
              Engineering high-reliability transactional cores, payment integrations, and clean modular service boundaries.
            </p>
          </li>

          <li class="founder-expertise__item">
            <h3 class="founder-expertise__title">Cloud infrastructure and DevOps</h3>
            <p class="founder-expertise__desc">
              Establishing secure cloud environments, automated CI/CD pipelines, monitoring, and low-risk release workflows.
            </p>
          </li>

          <li class="founder-expertise__item">
            <h3 class="founder-expertise__title">Technical reviews and code quality</h3>
            <p class="founder-expertise__desc">
              Mentoring engineering squads through architecture reviews, performance optimization, and security standards.
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
                Engineered the high-reliability backend settlement architecture and mobile platform powering everyday wallet transactions.
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
                Architected the live fleet telemetry and dispatch backend coordinating regional freight operations across MENA.
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
        </ul>
      </div>
    </section>
@endsection
