@extends('layouts.public', ['activePage' => 'blog'])

@section('meta')
  <meta name="description" content="{{ sec('blog', 'copy_1256e49dd66b') }}"/>
  <title>{{ sec('blog', 'copy_fdc599e3df65') }}</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="{{ sec('blog', 'copy_fdc599e3df65') }}"/>
  <meta property="og:description" content="{{ sec('blog', 'copy_1256e49dd66b') }}"/>
  <meta property="og:url" content="https://empetix.vercel.app/blog.html"/>
  <meta property="og:image" content="{{ content_asset(sec('global', 'social_preview')) }}"/>
  <meta property="og:image:url" content="{{ content_asset(sec('global', 'social_preview')) }}"/>
  <meta property="og:image:secure_url" content="{{ content_asset(sec('global', 'social_preview')) }}"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="{{ sec('blog', 'copy_46054f40eb9f') }}"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="{{ sec('blog', 'copy_fdc599e3df65') }}"/>
  <meta name="twitter:description" content="{{ sec('blog', 'copy_1256e49dd66b') }}"/>
  <meta name="twitter:image" content="{{ content_asset(sec('global', 'social_preview')) }}"/>
@endsection

@section('content')
<main id="main-content">


    <section class="page-hero">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ url('/') }}">{{ sec('blog', 'copy_66b93f927675') }}</a></li>
          <li aria-current="page">{{ sec('blog', 'copy_16c89765a20a') }}</li>
        </ol>
        <h1 class="page-hero__title">{{ sec('blog', 'hero_title', 'The Empetix Journal: Engineering, Growth, Video & Photo Essays.') }}</h1>
        <div class="page-hero__row">
          <div class="page-hero__label">
            <span class="eyebrow">{{ sec('blog', 'hero_eyebrow', 'Studio Publications') }}</span>
          </div>
          <p class="page-hero__lead">
            {{ sec('blog', 'hero_lead', 'Long-form technical articles, full video episodes with chapter transcripts, and behind-the-scenes photography galleries from our software and marketing teams. Click any entry to open the full post.') }}
          </p>
          <div class="page-hero__aside">
            <span class="t-meta">{{ sec('blog', 'hero_meta', 'VIDEOS · PHOTOS · ESSAYS') }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Video Post Banner -->
    <section class="section section--flush-top">
      <div class="container">
        <article class="post-card post-card--featured reveal">
          <a href="{{ url(sec('blog', 'featured_url', '/blog/video')) }}" class="post-card__link">
            <div class="frame post-card__frame frame--zoom">
              <img src="{{ content_asset(sec('blog', 'copy_26f63d8f8aa0')) }}" alt="{{ sec('blog', 'copy_a1f7648a3b74') }}" width="1200" height="750"/>
              <span class="post-card__play" aria-hidden="true">
                <svg viewBox="0 0 16 16" fill="currentColor"><polygon points="4,2 14,8 4,14"/></svg>
              </span>
              <span class="post-card__badge">{{ sec('blog', 'copy_855145b5639a') }}</span>
            </div>
            <div class="post-card__text">
              <div class="post-card__meta">
                <span class="post-card__cat">{{ sec('blog', 'copy_82eec5c0a198') }}</span>
                <time datetime="2026-11-05">{{ sec('blog', 'copy_3ee3c06a73ee') }}</time>
              </div>
              <h2 class="post-card__title">{{ sec('blog', 'copy_d70de1ef3361') }}</h2>
              <p class="post-card__excerpt">
                {{ sec('blog', 'copy_be2a3ae8d09f') }}
              </p>
              <span class="arrow-link">
                <span>{{ sec('blog', 'copy_9448bd6ae08a') }}</span>
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
          <span class="filter__label">{{ sec('blog', 'copy_7571d773cf73') }}</span>
          <button type="button" class="filter__btn" data-filter="all" aria-pressed="true">{{ sec('blog', 'copy_00f415594506') }}<sup>6</sup></button>
          <button type="button" class="filter__btn" data-filter="video" aria-pressed="false">{{ sec('blog', 'copy_a2f559e2cf39') }}<sup>3</sup></button>
          <button type="button" class="filter__btn" data-filter="photos" aria-pressed="false">{{ sec('blog', 'copy_ea939700e2f3') }}<sup>3</sup></button>
          <button type="button" class="filter__btn" data-filter="engineering" aria-pressed="false">{{ sec('blog', 'copy_2c9920d0787c') }}<sup>3</sup></button>
          <button type="button" class="filter__btn" data-filter="marketing" aria-pressed="false">{{ sec('blog', 'copy_aecfa0b21ab2') }}<sup>3</sup></button>
          <span class="filter__count" data-filter-count>{{ sec('blog', 'copy_941ff037160e') }}</span>
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
