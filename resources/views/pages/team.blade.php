@extends('layouts.public', ['activePage' => 'team'])

@section('meta')
  <meta name="description" content="Meet the leadership, engineering, product design, and marketing teams at Empetix Solution in New Cairo."/>
  <title>Team — Empetix Solution</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="Team — Empetix Solution"/>
  <meta property="og:description" content="Meet the leadership, engineering, product design, and marketing teams at Empetix Solution in New Cairo."/>
  <meta property="og:url" content="https://empetix.vercel.app/team.html"/>
  <meta property="og:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:secure_url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution Logo"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="Team — Empetix Solution"/>
  <meta name="twitter:description" content="Meet the leadership, engineering, product design, and marketing teams at Empetix Solution in New Cairo."/>
  <meta name="twitter:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
@endsection

@section('content')
<!-- 1. Breadcrumb & Editorial Hero -->
    <section class="team-band team-band--white team-hero-band" aria-labelledby="team-hero-heading">
      <div class="team-container">
        <nav class="team-crumb" aria-label="Breadcrumb">
          <ol class="team-crumb__list">
            <li class="team-crumb__item"><a href="{{ url('/') }}" class="team-crumb__link">Home</a></li>
            <li class="team-crumb__item" aria-current="page">Team</li>
          </ol>
        </nav>

        <div class="team-hero">
          <h1 id="team-hero-heading" class="team-hero__title">{{ sec('team', 'hero_title', 'The people behind Empetix') }}</h1>
          <p class="team-hero__lead">
            Engineers, product designers, and performance marketers working as one multidisciplinary team from New Cairo—without agency layers or external hand-offs.
          </p>
        </div>

        <div class="team-hero__media">
          <img
            class="team-hero__photo"
            src="{{ asset('assets/img/about/office.jpg') }}"
            alt="Empetix engineers and product designers reviewing layouts together in our New Cairo studio"
            width="1600"
            height="900"
          />
        </div>
      </div>
    </section>

    <!-- 2. Founders & Leadership -->
    @include('partials.founders')

    <!-- 3. Practice Teams (Employee Portrait Cards + Department Filter) -->
    <section id="directory" class="team-band team-band--white" aria-labelledby="directory-heading">
      <div class="team-container">
        <div class="team-section-head">
          <h2 id="directory-heading" class="team-heading">{{ sec('team', 'practices_heading', 'Practice teams') }}</h2>
          <div class="team-section-head__body">
            <p class="team-section-head__lead">
              Senior specialists across software engineering, product design, and performance marketing working together in dedicated client squads.
            </p>
            <div class="team-filters" role="group" aria-label="Filter team by department">
              <button type="button" class="team-filter" data-team-filter="all" aria-pressed="true">All teams (12)</button>
              <button type="button" class="team-filter" data-team-filter="engineering" aria-pressed="false">Software engineering (4)</button>
              <button type="button" class="team-filter" data-team-filter="design" aria-pressed="false">Product design (4)</button>
              <button type="button" class="team-filter" data-team-filter="marketing" aria-pressed="false">Marketing and production (4)</button>
            </div>
          </div>
        </div>

        <!-- Practice 1: Software Engineering -->
        <div id="practice-engineering" class="team-practice" data-team-group="engineering">
          <div class="team-practice__head">
            <h3 class="team-practice__title">Software engineering</h3>
            <p class="team-practice__desc">
              Backend, frontend, mobile, and infrastructure engineers responsible for scalable APIs, payment ledgers, and reliable cloud deployments.
            </p>
          </div>

          <ul class="team-cards">
            <!-- LOOP: team-member -->
            @foreach($members as $member)
          <li class="team-cards__item">
            <article class="team-card">
              <div class="team-card__media">
                <img
                  class="team-card__photo"
                  src="{{ str_starts_with((string) $member->photo, 'uploads/') ? asset('storage/'.$member->photo) : asset($member->photo ?: 'assets/img/team/placeholder.jpg') }}"
                  alt="Portrait of {{ $member->name }}, {{ $member->role }}"
                  width="640"
                  height="800"
                  loading="lazy"
                />
              </div>
              <div class="team-card__body">
                <div class="team-card__identity">
                  <h4 class="team-card__name">{{ $member->name }}</h4>
                  <p class="team-card__role">{{ $member->role }}</p>
                </div>
                <p class="team-card__focus">{{ $member->focus }}</p>
              </div>
            </article>
          </li>
@endforeach
          </ul>
        </div>
      </div>
    </section>

    <!-- 4. How Our Teams Work -->
    <section class="team-band team-band--surface" aria-labelledby="culture-heading">
      <div class="team-container">
        <div class="team-culture">
          <div class="team-section-head">
            <h2 id="culture-heading" class="team-heading">{{ sec('team', 'culture_heading', 'How we work together') }}</h2>
            <div class="team-section-head__body">
              <p class="team-section-head__lead">
                Every client engagement pairs engineering, design, and growth under one shared roadmap so software quality and commercial performance move together.
              </p>
            </div>
          </div>

          <ul class="team-culture__grid">
            @foreach(content_list('team.culture') as $card)
              <li class="team-culture__item">
                <h3 class="team-culture__title">{{ $card->title }}</h3>
                <p class="team-culture__text">{{ $card->body }}</p>
              </li>
            @endforeach
          </ul>

          <div class="team-culture__media">
            <img
              class="team-culture__photo"
              src="{{ asset('assets/img/careers/culture.jpg') }}"
              alt="Empetix team members collaborating around a studio workshop table in New Cairo"
              width="1600"
              height="900"
              loading="lazy"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- 5. Join the Team / General Company Contact -->
    <section class="team-band team-band--white" aria-labelledby="join-heading">
      <div class="team-container">
        <div class="team-join">
          <div class="team-join__main">
            <h2 id="join-heading" class="team-heading">{{ sec('team', 'join_heading', 'Join Empetix Solution') }}</h2>
            <p class="team-join__copy">
              We hire engineers, designers, and marketers who care deeply about craft and measurable work. Explore open positions on our careers portal or reach out to our company desk.
            </p>
          </div>
          <div class="team-join__actions">
            <a href="{{ url('/careers') }}" class="team-btn team-btn--primary">View open roles</a>
            <a href="{{ url('/contact') }}" class="team-text-link">Start a project</a>
          </div>
        </div>
      </div>
    </section>
@endsection
