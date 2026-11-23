@extends('layouts.public', ['activePage' => 'team'])

@section('meta')
  <meta name="description" content="{{ sec('team', 'copy_dca495ea9079') }}"/>
  <title>{{ sec('team', 'copy_1bd5cb61a600') }}</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="{{ sec('team', 'copy_1bd5cb61a600') }}"/>
  <meta property="og:description" content="{{ sec('team', 'copy_dca495ea9079') }}"/>
  <meta property="og:url" content="https://empetix.vercel.app/team.html"/>
  <meta property="og:image" content="{{ content_asset(sec('global', 'social_preview')) }}"/>
  <meta property="og:image:url" content="{{ content_asset(sec('global', 'social_preview')) }}"/>
  <meta property="og:image:secure_url" content="{{ content_asset(sec('global', 'social_preview')) }}"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="{{ sec('team', 'copy_46054f40eb9f') }}"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="{{ sec('team', 'copy_1bd5cb61a600') }}"/>
  <meta name="twitter:description" content="{{ sec('team', 'copy_dca495ea9079') }}"/>
  <meta name="twitter:image" content="{{ content_asset(sec('global', 'social_preview')) }}"/>
@endsection

@section('content')
<!-- 1. Breadcrumb & Editorial Hero -->
    <section class="team-band team-band--white team-hero-band" aria-labelledby="team-hero-heading">
      <div class="team-container">
        <nav class="team-crumb" aria-label="Breadcrumb">
          <ol class="team-crumb__list">
            <li class="team-crumb__item"><a href="{{ url('/') }}" class="team-crumb__link">{{ sec('team', 'copy_66b93f927675') }}</a></li>
            <li class="team-crumb__item" aria-current="page">{{ sec('team', 'copy_cdbd990d3384') }}</li>
          </ol>
        </nav>

        <div class="team-hero">
          <h1 id="team-hero-heading" class="team-hero__title">{{ sec('team', 'hero_title', 'The people behind Empetix') }}</h1>
          <p class="team-hero__lead">
            {{ sec('team', 'copy_be3f40c5f9fe') }}
          </p>
        </div>

        <div class="team-hero__media">
          <img
            class="team-hero__photo"
            src="{{ content_asset(sec('team', 'copy_38a9b4c47c1d')) }}"
            alt="{{ sec('team', 'copy_548db1eb20f4') }}"
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
              {{ sec('team', 'copy_42a38ba194b4') }}
            </p>
            <div class="team-filters" role="group" aria-label="Filter team by department">
              <button type="button" class="team-filter" data-team-filter="all" aria-pressed="true">{{ sec('team', 'copy_e111c2849bae') }}</button>
              <button type="button" class="team-filter" data-team-filter="engineering" aria-pressed="false">{{ sec('team', 'copy_bba41e68c87e') }}</button>
              <button type="button" class="team-filter" data-team-filter="design" aria-pressed="false">{{ sec('team', 'copy_9f3ba2584498') }}</button>
              <button type="button" class="team-filter" data-team-filter="marketing" aria-pressed="false">{{ sec('team', 'copy_81990933b449') }}</button>
            </div>
          </div>
        </div>

        <!-- Practice 1: Software Engineering -->
        <div id="practice-engineering" class="team-practice" data-team-group="engineering">
          <div class="team-practice__head">
            <h3 class="team-practice__title">{{ sec('team', 'copy_865f5a4280b3') }}</h3>
            <p class="team-practice__desc">
              {{ sec('team', 'copy_a588d0d2cd5f') }}
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
                {{ sec('team', 'copy_67418b5acff7') }}
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
              src="{{ content_asset(sec('team', 'copy_b221f03ea2bf')) }}"
              alt="{{ sec('team', 'copy_8a668411ecaf') }}"
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
              {{ sec('team', 'copy_0c8127443473') }}
            </p>
          </div>
          <div class="team-join__actions">
            <a href="{{ url('/careers') }}" class="team-btn team-btn--primary">{{ sec('team', 'copy_96490c6dd6ac') }}</a>
            <a href="{{ url('/contact') }}" class="team-text-link">{{ sec('team', 'copy_83e7789a3687') }}</a>
          </div>
        </div>
      </div>
    </section>
@endsection
