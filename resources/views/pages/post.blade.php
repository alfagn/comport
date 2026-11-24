@extends('layouts.public', ['activePage' => 'blog'])

@section('meta')
  <meta name="description" content="{{ $post->excerpt }}"/>
  <title>{{ $post->title }} — Empetix Journal</title>
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="article"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="{{ $post->title }} — Empetix Journal"/>
  <meta property="og:description" content="{{ $post->excerpt }}"/>
  <meta property="og:url" content="{{ seo_current_url() }}"/>
  <meta property="og:image" content="{{ seo_og_image() }}"/>
@endsection

@section('content')
<main id="main-content">

    <div class="read-progress" aria-hidden="true">
      <div id="read-progress-bar" class="read-progress__bar"></div>
    </div>

    <header class="article-head">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ url('/') }}">Home</a></li>
          <li><a href="{{ url('/blog') }}">Blog &amp; Media</a></li>
          <li aria-current="page">{{ $post->category ?? $post->title }}</li>
        </ol>

        <div class="article-head__meta">
          @if($post->category)<span class="article-head__cat">{{ strtoupper($post->category) }}</span>@endif
          @if($post->published_at)<time datetime="{{ $post->published_at->toDateString() }}">{{ strtoupper($post->published_at->format('d F Y')) }}</time>@endif
          @if($post->meta['read_time'] ?? null)<span>{{ $post->meta['read_time'] }}</span>@endif
        </div>

        <h1 class="article-head__title">{{ $post->title }}</h1>
        <p class="article-head__dek">{{ $post->excerpt }}</p>
      </div>
    </header>

    <!-- Cover Photo -->
    <div class="container article-media">
      <div class="frame frame--21x9">
        <img src="{{ str_starts_with((string) $post->cover_image, 'uploads/') ? asset('storage/'.$post->cover_image) : asset($post->cover_image) }}" alt="{{ $post->title }}" width="1600" height="685"/>
      </div>
      <div class="bleed__caption">
        <span>{{ strtoupper($post->category ?? 'EMPETIX JOURNAL') }}</span>
        <span>EMPETIX SOLUTION</span>
      </div>
    </div>

    <!-- Article Body + Sticky Sidebar -->
    @php
      $bylineName = $post->meta['author_name'] ?? 'Empetix Editorial';
      $bylineRole = $post->meta['author_role'] ?? 'Software Engineering & Growth Studio';
      $bylineAvatar = $post->meta['author_avatar'] ?? null;
    @endphp
    <section id="article-content" class="section section--flush-top">
      <div class="container article-layout">
        <!-- Sticky Author & TOC Sidebar -->
        <aside class="article-layout__aside" aria-label="Article metadata and table of contents">
          <div class="byline">
            @if($bylineAvatar)
              <div class="byline__avatar">
                <img src="{{ content_asset($bylineAvatar) }}" alt="{{ $bylineName }}" width="96" height="96"/>
              </div>
            @endif
            <div>
              <p class="byline__name">{{ $bylineName }}</p>
              <p class="byline__role">{{ $bylineRole }}</p>
            </div>
          </div>

          @if(!empty($post->meta['toc']))
            <div class="aside-block">
              <p class="aside-block__title">In This Post</p>
              <ol class="toc">
                @foreach((array) ($post->meta['toc'] ?? []) as $toc)
                  <li><a href="#{{ $toc['id'] ?? '' }}">{{ $toc['label'] ?? '' }}</a></li>
                @endforeach
              </ol>
            </div>
          @endif

          <div class="aside-block">
            <p class="aside-block__title">Share Article</p>
            <div class="share">
              <button type="button" data-copy-url>Copy Link</button>
              <a href="{{ setting('linkedin', 'https://www.linkedin.com') }}" rel="noopener noreferrer">LinkedIn</a>
            </div>
          </div>
        </aside>

        <!-- Full Article Prose -->
        <article class="article-layout__body prose">
          {!! $post->body !!}

          @if($post->meta['author_name'] ?? null)
            <div class="author-card">
              <div class="author-card__img">
                <img src="{{ asset($post->meta['author_avatar'] ?? 'assets/img/og-empetix.jpg') }}" alt="{{ $post->meta['author_name'] }}" width="160" height="160"/>
              </div>
              <div>
                <p class="t-meta">WRITTEN BY</p>
                <h3 class="author-card__name">{{ $post->meta['author_name'] }}@if($post->meta['author_role'] ?? null) &mdash; {{ $post->meta['author_role'] }}@endif</h3>
                @if($post->meta['author_bio'] ?? null)
                  <p class="author-card__bio">
                    {{ $post->meta['author_bio'] }}
                    @if($post->meta['author_profile'] ?? null) <a href="{{ url($post->meta['author_profile']) }}">View profile</a>.@endif
                  </p>
                @endif
              </div>
            </div>
          @endif
        </article>
      </div>
    </section>

    <!-- Related Posts -->
    <section class="section section--alt section--ruled" aria-labelledby="related-heading">
      <div class="container">
        <div class="section-head">
          <div class="section-head__label">
            <span class="eyebrow">Continue Reading</span>
          </div>
          <div class="section-head__title">
            <h2 id="related-heading" class="t-h3">More from the Empetix Journal</h2>
          </div>
        </div>

        <div class="post-rows">
          @foreach($relatedPosts as $related)
            <article class="post-row">
              <a href="{{ route('blog.show', $related->slug) }}" class="post-row__link">
                <div class="post-row__meta">
                  <span class="post-card__cat">{{ $related->meta['card_label'] ?? strtoupper($related->type) }}</span>
                  @if($related->published_at)<time datetime="{{ $related->published_at->toDateString() }}">{{ strtoupper($related->published_at->format('d M Y')) }}</time>@endif
                </div>
                <h3 class="post-row__title">{{ $related->title }}</h3>
                @if($related->cover_image)
                  <div class="frame post-row__thumb">
                    <img src="{{ str_starts_with((string) $related->cover_image, 'uploads/') ? asset('storage/'.$related->cover_image) : asset($related->cover_image) }}" alt="{{ $related->title }}" width="240" height="160"/>
                  </div>
                @endif
              </a>
            </article>
          @endforeach
        </div>
      </div>
    </section>

    <!-- Full-Screen Photo Lightbox Dialog -->
    <dialog id="photo-lightbox" class="lightbox" aria-label="Full-screen photo viewer">
      <div class="lightbox__bar">
        <span class="lightbox__counter" data-lightbox-counter>01 / 04</span>
        <button type="button" class="lightbox__btn" data-lightbox-close>Close (Esc)</button>
      </div>
      <div class="lightbox__stage">
        <img class="lightbox__img" data-lightbox-img src="" alt=""/>
      </div>
      <div class="lightbox__foot">
        <p class="lightbox__caption" data-lightbox-caption></p>
        <div class="lightbox__nav">
          <button type="button" class="lightbox__btn" data-lightbox-prev aria-label="Previous photo">&larr; Prev</button>
          <button type="button" class="lightbox__btn" data-lightbox-next aria-label="Next photo">Next &rarr;</button>
        </div>
      </div>
    </dialog>
</main>
@endsection
