<div>
    <!-- Post Header -->
    <section class="article-head">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ route('home') }}">Home</a></li>
          <li><a href="{{ route('site.blog') }}">Blog</a></li>
          <li aria-current="page">{{ $post->title }}</li>
        </ol>

        <div class="article-head__meta">
          <span class="article-head__cat">{{ strtoupper($post->type) }} &middot; {{ strtoupper($post->category?->name ?: 'INSIGHTS') }}</span>
          <span>{{ $post->published_at?->format('d F Y') ?? date('d F Y') }}</span>
          <span>{{ $post->views_count }} VIEWS</span>
        </div>

        <h1 class="article-head__title">{{ $post->title }}</h1>
        <p class="article-head__dek">{{ $post->excerpt }}</p>
      </div>
    </section>

    <!-- Media Stage: Video Player / Featured Hero / Photo Essay -->
    <div class="container" style="margin-bottom: 3rem;">
      @if($post->type === 'video' && $post->video_url)
        <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
          <iframe
            src="{{ $post->video_url }}"
            style="position: absolute; top:0; left: 0; width: 100%; height: 100%; border: 0;"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
          ></iframe>
        </div>
      @else
        <div class="frame frame--16x9 frame--zoom" style="border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
          <img src="{{ asset($post->featured_image ?: 'assets/img/blog/arabic-first.jpg') }}" alt="{{ $post->title }}" width="1920" height="1080"/>
        </div>
      @endif
    </div>

    <!-- Article Body -->
    <section class="section section--flush-top">
      <div class="container" style="max-width: 860px;">
        <div style="font-size: 1.15rem; line-height: 1.85; color: #1C2E52;">
          {!! nl2br(e($post->body)) !!}
        </div>

        <!-- If Photo Essay, render photo gallery -->
        @if($post->type === 'photo' && !empty($post->gallery_images))
          <div style="margin-top: 3rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
            @foreach($post->gallery_images as $photo)
              <div class="frame frame--3x2" style="border-radius: 6px; overflow: hidden;">
                <img src="{{ asset($photo) }}" alt="Photo essay frame" width="600" height="400"/>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </section>

    <!-- Related Posts -->
    @if($relatedPosts->isNotEmpty())
      <section class="section section--alt section--ruled">
        <div class="container">
          <div class="section-head reveal">
            <div class="section-head__label"></div>
            <div class="section-head__title">
              <h2 class="t-h3">More Articles &amp; Dispatches</h2>
            </div>
            <div class="section-head__action">
              <a href="{{ route('site.blog') }}" class="arrow-link">
                <span>View Full Archive</span>
                <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
              </a>
            </div>
          </div>

          <div class="post-grid">
            @foreach($relatedPosts as $rel)
              <article class="post-card reveal">
                <a href="{{ route('site.blog.detail', $rel->slug) }}" class="post-card__link">
                  <div class="frame post-card__frame frame--zoom">
                    <img src="{{ asset($rel->featured_image ?: 'assets/img/blog/arabic-first.jpg') }}" alt="{{ $rel->title }}" width="800" height="533"/>
                    <span class="post-card__badge">{{ strtoupper($rel->type) }}</span>
                  </div>
                  <div class="post-card__meta">
                    <span class="post-card__cat">{{ strtoupper($rel->category?->name ?: 'INSIGHTS') }}</span>
                    <span>{{ $rel->views_count }} VIEWS</span>
                  </div>
                  <h3 class="post-card__title">{{ $rel->title }}</h3>
                </a>
              </article>
            @endforeach
          </div>
        </div>
      </section>
    @endif
</div>
