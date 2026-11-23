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
          @foreach($projects as $i => $project)
          <article class="project-card {{ $i === 0 ? 'project-card--full' : ($i % 2 ? 'project-card--half' : 'project-card--wide') }} reveal">
            <a class="project-card__link" href="{{ route('work.show', $project->slug) }}">
              <div class="frame project-card__frame frame--zoom">
                <img alt="{{ $project->title }}" height="750" src="{{ str_starts_with((string) $project->image, 'uploads/') ? asset('storage/'.$project->image) : asset($project->image ?: 'assets/img/work/placeholder.jpg') }}" width="1200"/>
              </div>
              <div class="project-card__body">
                <span class="project-card__client">{{ $project->kicker }}</span>
                <span class="project-card__year">{{ $project->year }}</span>
                <h3 class="project-card__title">{{ $project->title }}</h3>
                <ul class="tag-list">
                  @foreach($project->tags as $tag)
                    <li class="tag">{{ $tag->name }}</li>
                  @endforeach
                </ul>
              </div>
            </a>
          </article>
@endforeach
          <!-- END LOOP: project-item -->
        </div>
      </div>
    </section>
@endsection
