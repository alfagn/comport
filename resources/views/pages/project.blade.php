@extends('layouts.public', ['activePage' => 'work'])

@section('meta')
  <meta name="description" content="{{ $project->excerpt }}"/>
  <title>{{ $project->title }} — Empetix Solution Case Study</title>
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="article"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="{{ $project->title }} — Empetix Solution Case Study"/>
  <meta property="og:description" content="{{ $project->excerpt }}"/>
  <meta property="og:url" content="https://empetix.vercel.app/work/{{ $project->slug }}"/>
  <meta property="og:image" content="{{ content_asset(sec('global', 'social_preview')) }}"/>
@endsection

@section('content')
<main id="main-content">


    <section class="article-head">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ url('/') }}">{{ sec('project', 'copy_66b93f927675') }}</a></li>
          <li><a href="{{ url('/work') }}">{{ sec('project', 'copy_5629b6adbbae') }}</a></li>
          <li aria-current="page">{{ $project->client ?? $project->title }}</li>
        </ol>

        <div class="article-head__meta">
          @if($project->kicker)<span class="article-head__cat">{{ $project->kicker }}</span>@endif
          @if($project->location)<span>{{ $project->location }}</span>@endif
          @if($project->year)<span>{{ $project->year }}</span>@endif
        </div>

        <h1 class="article-head__title">{{ $project->title }}</h1>
        <p class="article-head__dek">{{ $project->excerpt }}</p>

        <dl class="facts">
          @foreach((array) $project->facts as $fact)
            <div>
              <dt>{{ $fact['label'] ?? '' }}</dt>
              <dd>{{ $fact['value'] ?? '' }}</dd>
            </div>
          @endforeach
        </dl>
      </div>
    </section>

    <!-- Full-Bleed Hero Image -->
    <section class="bleed">
      <div class="container">
        <div class="frame bleed__frame">
          <img src="{{ str_starts_with((string) $project->image, 'uploads/') ? asset('storage/'.$project->image) : asset($project->image) }}" alt="{{ $project->title }}" width="1600" height="900"/>
        </div>
        <div class="bleed__caption">
          <span>{{ strtoupper($project->client ?? 'EMPETIX') }} &mdash; {{ sec('project', 'case_study_suffix') }}</span>
          <span>{{ sec('project', 'copy_b3d3c7a19f0e') }}</span>
        </div>
      </div>
    </section>

    <!-- Audited Outcomes Strip -->
    <section class="section section--sm">
      <div class="container">
        <div class="stats">
          @foreach((array) $project->stats as $stat)
            <div class="stat">
              <div class="stat__value">
                <span data-counter data-counter-target="{{ $stat['target'] ?? 0 }}" data-counter-decimals="{{ $stat['decimals'] ?? 0 }}">{{ $stat['target'] ?? 0 }}</span>
                <span class="stat__suffix">{{ $stat['suffix'] ?? '' }}</span>
              </div>
              <p class="stat__label">{{ $stat['label'] ?? '' }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    @php($secNo = 0)
    @foreach((array) $project->sections as $section)
      @php($secNo++)
      <section class="section {{ $secNo % 2 ? 'section--ruled' : '' }}">
        <div class="container case-cols">
          <div class="case-cols__label">
            <span class="eyebrow">{{ str_pad((string) $secNo, 2, '0', STR_PAD_LEFT) }} / {{ $section['eyebrow'] ?? '' }}</span>
          </div>
          <div class="case-cols__body prose">
            <h2>{{ $section['heading'] ?? '' }}</h2>
            @foreach((array) ($section['items'] ?? []) as $paragraph)
              <p>{{ $paragraph }}</p>
            @endforeach
            @if($loop->last && $project->quote)
              <blockquote>
                &ldquo;{{ $project->quote }}&rdquo;
                @if($project->quote_cite)<cite>&mdash; {{ $project->quote_cite }}</cite>@endif
              </blockquote>
            @endif
          </div>
        </div>
      </section>
    @endforeach

    @if($project->body)
      <section class="section">
        <div class="container case-cols">
          <div class="case-cols__label">
            <span class="eyebrow">{{ sec('project', 'copy_7b4dd66edc0b') }}</span>
          </div>
          <div class="case-cols__body prose">{!! $project->body !!}</div>
        </div>
      </section>
    @endif

    @if($project->video_src)
      <section class="section section--alt">
        <div class="container">
          <div class="video" data-video-player="{{ $project->slug }}-reel">
            @if($project->video_poster)
              <img class="video__poster" src="{{ asset($project->video_poster) }}" alt="{{ $project->title }} video poster" width="1280" height="720"/>
            @endif
            <video class="video__el" preload="metadata" @if($project->video_poster) poster="{{ asset($project->video_poster) }}" @endif>
              <source src="{{ asset($project->video_src) }}" type="video/mp4"/>
            </video>
            <button type="button" class="video__btn" data-video-play aria-label="Play {{ $project->title }} case study video">
              <span class="video__btn-inner">
                <span class="video__btn-icon">
                  <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><polygon points="4,2 14,8 4,14"/></svg>
                </span>
                <span>{{ sec('project', 'copy_d5ebf21c3df6') }}</span>
              </span>
            </button>
          </div>
          <div class="video__meta">
            <span>{{ sec('project', 'copy_201a8b9bca1e') }}</span>
          </div>
        </div>
      </section>
    @endif

    <!-- Next Project Pager -->
    <div class="container">
      @if($nextProject)
      <a href="{{ route('work.show', $nextProject->slug) }}" class="pager">
        <span class="pager__label">{{ sec('project', 'copy_2d7f7e941759') }}</span>
        <span class="pager__title">
          <span>{{ $nextProject->title }}</span>
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="currentColor" stroke-width="2"/></svg>
        </span>
      </a>
    @endif
    </div>
@endsection
