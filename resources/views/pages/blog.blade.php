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
  <meta property="og:url" content="{{ seo_current_url() }}"/>
  <meta property="og:image" content="{{ seo_og_image() }}"/>
  <meta property="og:image:url" content="{{ seo_og_image() }}"/>
  <meta property="og:image:secure_url" content="{{ seo_og_image() }}"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="{{ sec('blog', 'copy_fdc599e3df65') }}"/>
  <meta name="twitter:description" content="{{ sec('blog', 'copy_1256e49dd66b') }}"/>
  <meta name="twitter:image" content="{{ seo_og_image() }}"/>
@endsection

@section('content')
<main id="main-content">

    <section class="page-hero">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ url('/') }}">Home</a></li>
          <li aria-current="page">Blog &amp; Media</li>
        </ol>
        <h1 class="page-hero__title">{{ sec('blog', 'hero_title', 'The Empetix Journal: Engineering, Growth, Video & Photo Essays.') }}</h1>
        <div class="page-hero__row">
          <div class="page-hero__label">
            <span class="eyebrow">{{ sec('blog', 'hero_eyebrow', 'Studio Publications') }}</span>
          </div>
          <p class="page-hero__lead">
            {{ sec('blog', 'hero_lead', 'Long-form technical articles, system architecture deep-dives, and engineering video episodes from our software and cloud teams. Click any entry to open the full post.') }}
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
          <a href="{{ url(sec('blog', 'featured_url', '/blog/five-day-product-growth-sprint')) }}" class="post-card__link">
            <div class="frame post-card__frame frame--zoom">
              <img src="{{ content_asset(sec('blog', 'copy_26f63d8f8aa0')) }}" alt="Empetix product and growth team collaborating during a 5-day sprint" width="1200" height="750"/>
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
          <span class="filter__label">Filter posts:</span>
          <button type="button" class="filter__btn" data-filter="all" aria-pressed="true">All Posts<sup>{{ $posts->count() }}</sup></button>
          <button type="button" class="filter__btn" data-filter="video" aria-pressed="false">Video Episodes</button>
          <button type="button" class="filter__btn" data-filter="engineering" aria-pressed="false">Software Engineering</button>
          <button type="button" class="filter__btn" data-filter="architecture" aria-pressed="false">Cloud &amp; Architecture</button>
          <span class="filter__count" data-filter-count>{{ $posts->count() }} {{ \Illuminate\Support\Str::plural('entry', $posts->count()) }}</span>
        </div>

        <div id="blog-archive" class="post-grid">
          @forelse($posts as $post)
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
          @empty
          <div class="empty-state" style="grid-column: 1 / -1; text-align: center; padding: 64px 24px; background: rgba(255, 77, 28, 0.02); border: 1px dashed var(--line, #e2e8f0); border-radius: 16px;">
            <p style="font-size: 18px; font-weight: 700; color: var(--text-heading, #0f172a); margin-bottom: 8px;">
              No publications yet
            </p>
            <p style="font-size: 14px; color: var(--text-muted, #64748b); max-width: 440px; margin: 0 auto;">
              Our engineering monographs, studio video breakdowns, and technical essays are currently being drafted. Check back soon!
            </p>
          </div>
          @endforelse
        </div>
      </div>
    </section>
</main>
@endsection
