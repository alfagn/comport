@extends('layouts.public', ['activePage' => 'team'])

@section('meta')
  <meta name="description" content="{{ $member->name }}, {{ $member->role }} at Empetix Solution."/>
  <title>{{ $member->name }} — Empetix Solution</title>
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="{{ $member->name }} — Empetix Solution"/>
  <meta property="og:description" content="{{ $member->name }}, {{ $member->role }} at Empetix Solution."/>
  <meta property="og:url" content="https://empetix.vercel.app/founders/{{ $member->slug }}"/>
  <meta property="og:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="{{ $member->name }} — Empetix Solution"/>
  <meta name="twitter:description" content="{{ $member->name }}, {{ $member->role }} at Empetix Solution."/>
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
              src="{{ str_starts_with((string) $member->photo, 'uploads/') ? asset('storage/'.$member->photo) : asset($member->photo) }}"
              alt="Portrait of Mohanad Ahmed Shehata, Co-founder and chief technology officer"
              width="800"
              height="1000"
            />
          </div>

          <div class="founder-hero__body">
            <h1 id="founder-name" class="founder-hero__name">{{ $member->name }}</h1>
            <p class="founder-hero__title">{{ $member->role }}</p>
            <p class="founder-hero__lead">{{ $member->lead }}</p>
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
              @foreach((array) $member->bio as $paragraph)
                <p class="founder-about__copy">{{ $paragraph }}</p>
              @endforeach
            </div>
        </div>
      </div>
    </section>

    <!-- Expertise -->
    <section class="founder-band founder-band--white" aria-labelledby="expertise-heading">
      <div class="founder-container">
        <h2 id="expertise-heading" class="founder-heading">Where Mohanad can help</h2>
        <ul class="founder-expertise">
              @foreach((array) $member->expertise as $expertise)
                <li class="founder-expertise__item">
                  <h3 class="founder-expertise__title">{{ $expertise['title'] ?? '' }}</h3>
                  <p class="founder-expertise__desc">{{ $expertise['desc'] ?? '' }}</p>
                </li>
              @endforeach
            </ul>
      </div>
    </section>

    <!-- Selected Work -->
    <section class="founder-band founder-band--surface" aria-labelledby="work-heading">
      <div class="founder-container">
        <h2 id="work-heading" class="founder-heading">Selected work</h2>
        <ul class="founder-projects">
              @foreach($projects as $project)
                <li>
                  <article class="founder-project">
                    <div class="founder-project__media">
                      <img class="founder-project__photo" src="{{ str_starts_with((string) $project->image, 'uploads/') ? asset('storage/'.$project->image) : asset($project->image ?: 'assets/img/work/placeholder.jpg') }}" alt="{{ $project->title }}" width="640" height="400" loading="lazy"/>
                    </div>
                    <h3 class="founder-project__client">{{ $project->kicker }}</h3>
                    <p class="founder-project__outcome">{{ $project->excerpt }}</p>
                    <a href="{{ route('work.show', $project->slug) }}" class="founder-text-link">Read the case study</a>
                  </article>
                </li>
              @endforeach
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
              @foreach($otherFounders as $other)
                <li>
                  <a href="{{ route('founders.show', $other->slug) }}" class="founder-others__link">
                    <div class="founder-others__media">
                      <img class="founder-others__photo" src="{{ str_starts_with((string) $other->photo, 'uploads/') ? asset('storage/'.$other->photo) : asset($other->photo) }}" alt="Portrait of {{ $other->name }}" width="480" height="600" loading="lazy"/>
                    </div>
                    <h3 class="founder-others__name">{{ $other->name }}</h3>
                    <p class="founder-others__role">{{ $other->role }}</p>
                  </a>
                </li>
              @endforeach
            </ul>
      </div>
    </section>
@endsection
