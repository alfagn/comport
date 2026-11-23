@extends('layouts.public', ['activePage' => 'blog'])

@section('meta')
  <meta name="description" content="Read full engineering articles, watch studio video episodes, and explore behind-the-scenes photo essays from Empetix."/>
  <title>The Empetix Journal: Engineering Articles, Video Episodes & Photo Essays</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="The Empetix Journal: Engineering Articles, Video Episodes & Photo Essays"/>
  <meta property="og:description" content="Read full engineering articles, watch studio video episodes, and explore behind-the-scenes photo essays from Empetix."/>
  <meta property="og:url" content="https://empetix.vercel.app/blog.html"/>
  <meta property="og:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:secure_url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution Logo"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="The Empetix Journal: Engineering Articles, Video Episodes & Photo Essays"/>
  <meta name="twitter:description" content="Read full engineering articles, watch studio video episodes, and explore behind-the-scenes photo essays from Empetix."/>
  <meta name="twitter:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
@endsection

@section('content')
<main id="main-content">


    <section class="page-hero">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ url('/') }}">Home</a></li>
          <li aria-current="page">Blog &amp; Media</li>
        </ol>
        <h1 class="page-hero__title">The Empetix Journal: Engineering, Growth, Video &amp; Photo Essays.</h1>
        <div class="page-hero__row">
          <div class="page-hero__label">
            <span class="eyebrow">Studio Publications</span>
          </div>
          <p class="page-hero__lead">
            Long-form technical articles, full video episodes with chapter transcripts, and behind-the-scenes photography galleries from our software and marketing teams. Click any entry to open the full post.
          </p>
          <div class="page-hero__aside">
            <span class="t-meta">VIDEOS &middot; PHOTOS &middot; ESSAYS</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Video Post Banner -->
    <section class="section section--flush-top">
      <div class="container">
        <article class="post-card post-card--featured reveal">
          <a href="{{ url('/blog/video') }}" class="post-card__link">
            <div class="frame post-card__frame frame--zoom">
              <img src="{{ asset('assets/img/blog/design-sprint.jpg') }}" alt="Empetix product and growth team placing voting dots on interface sketches during a 5-day sprint" width="1200" height="750"/>
              <span class="post-card__play" aria-hidden="true">
                <svg viewBox="0 0 16 16" fill="currentColor"><polygon points="4,2 14,8 4,14"/></svg>
              </span>
              <span class="post-card__badge">FEATURED VIDEO EPISODE &middot; 14:20</span>
            </div>
            <div class="post-card__text">
              <div class="post-card__meta">
                <span class="post-card__cat">VIDEO DOCUMENTARY + FULL POST</span>
                <time datetime="2026-11-05">05 NOV 2026</time>
              </div>
              <h2 class="post-card__title">Inside an Empetix 5-Day Product &amp; Growth Sprint (Full Video + Transcript + Photos)</h2>
              <p class="post-card__excerpt">
                Watch how our engineers, product designers, and performance marketers take a FinTech checkout product from raw problem statement to user-tested prototype and live acquisition test in five days.
              </p>
              <span class="arrow-link">
                <span>Watch Video &amp; Read Full Post</span>
                <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                  <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </span>
            </div>
          </a>
        </article>
      </div>
    </section>

    <!-- Filterable Blog Archive -->
    <section class="section section--flush-top" aria-label="Blog posts archive">
      <div class="container">
        <div class="filter" data-filter-bar="blog-archive" role="toolbar" aria-label="Filter blog posts by format and topic">
          <span class="filter__label">Filter posts:</span>
          <button type="button" class="filter__btn" data-filter="all" aria-pressed="true">All Posts<sup>6</sup></button>
          <button type="button" class="filter__btn" data-filter="video" aria-pressed="false">Video Episodes<sup>3</sup></button>
          <button type="button" class="filter__btn" data-filter="photos" aria-pressed="false">Photo Essays<sup>3</sup></button>
          <button type="button" class="filter__btn" data-filter="engineering" aria-pressed="false">Engineering<sup>3</sup></button>
          <button type="button" class="filter__btn" data-filter="marketing" aria-pressed="false">Growth Marketing<sup>3</sup></button>
          <span class="filter__count" data-filter-count>6 entries</span>
        </div>

        <div id="blog-archive" class="post-grid">
          @foreach($posts as $post)
          <article class="post-card" data-category="{{ $post->type }} {{ \Illuminate\Support\Str::slug($post->category) }}">
            <a href="{{ route('blog.show', $post->slug) }}" class="post-card__link">
              <div class="frame post-card__frame frame--zoom">
                <img src="{{ str_starts_with((string) $post->cover_image, 'uploads/') ? asset('storage/'.$post->cover_image) : asset($post->cover_image ?: 'assets/img/blog/placeholder.jpg') }}" alt="{{ $post->title }}" width="800" height="533"/>
                <span class="post-card__badge">{{ $post->type === 'video' ? 'VIDEO EPISODE' : ($post->type === 'photos' ? 'PHOTO ESSAY' : 'ARTICLE') }}</span>
              </div>
              <div class="post-card__meta">
                <span class="post-card__cat">{{ $post->category }}</span>
                <time datetime="{{ $post->published_at?->format('Y-m-d') }}">{{ $post->published_at?->format('d M Y') }}</time>
                @if($post->meta['card_label'] ?? null)<span>{{ $post->meta['card_label'] }}</span>@endif
              </div>
              <h2 class="post-card__title">{{ $post->title }}</h2>
              <p class="post-card__excerpt">{{ $post->excerpt }}</p>
            </a>
          </article>
@endforeach
        </div>
      </div>
    </section>
@endsection
