<div>
    <!-- Page Hero -->
    <section class="page-hero">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ route('home') }}">Home</a></li>
          <li aria-current="page">Blog &amp; Media</li>
        </ol>
        <h1 class="page-hero__title">The Empetix Journal: Engineering, Growth, Video &amp; Photo Essays.</h1>
        <div class="page-hero__row">
          <div class="page-hero__label">
            <span class="eyebrow">Studio Publications</span>
          </div>
          <p class="page-hero__lead">
            Long-form technical articles, full video episodes with chapter transcripts, and behind-the-scenes photography galleries from our software and marketing teams.
          </p>
          <div class="page-hero__aside">
            <span class="t-meta">{{ $posts->count() }} PUBLISHED ENTRIES</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Search & Filter Controls -->
    <section class="section section--flush-top">
      <div class="container">
        <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
          <!-- Filter Buttons -->
          <div class="filter" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <button type="button" wire:click="setType('all')" class="filter__btn" aria-pressed="{{ $type === 'all' ? 'true' : 'false' }}">
              All Media
            </button>
            <button type="button" wire:click="setType('article')" class="filter__btn" aria-pressed="{{ $type === 'article' ? 'true' : 'false' }}">
              Articles
            </button>
            <button type="button" wire:click="setType('video')" class="filter__btn" aria-pressed="{{ $type === 'video' ? 'true' : 'false' }}">
              Video Episodes
            </button>
            <button type="button" wire:click="setType('photo')" class="filter__btn" aria-pressed="{{ $type === 'photo' ? 'true' : 'false' }}">
              Photo Essays
            </button>
          </div>

          <!-- Livewire Real-time Search Box -->
          <div style="position: relative; min-width: 280px;">
            <input
              type="search"
              wire:model.live.debounce.300ms="q"
              placeholder="Search articles &amp; episodes..."
              class="form__input"
              style="padding-inline-start: 2.25rem; font-size: 0.9rem;"
            />
            <svg style="position: absolute; inset-inline-start: 0.75rem; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #62718A;" viewBox="0 0 20 20" fill="none" aria-hidden="true">
              <circle cx="8.5" cy="8.5" r="6" stroke="currentColor" stroke-width="2"/>
              <path d="M13 13L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </div>
        </div>

        <!-- Post Grid -->
        <div class="post-grid">
          @forelse($posts as $post)
            <article class="post-card reveal">
              <a href="{{ route('site.blog.detail', $post->slug) }}" class="post-card__link">
                <div class="frame post-card__frame frame--zoom">
                  <img src="{{ asset($post->featured_image ?: 'assets/img/blog/arabic-first.jpg') }}" alt="{{ $post->title }}" width="800" height="533"/>
                  @if($post->type === 'video')
                    <span class="post-card__play" aria-hidden="true">
                      <svg viewBox="0 0 16 16" fill="currentColor"><polygon points="4,2 14,8 4,14"/></svg>
                    </span>
                  @endif
                  <span class="post-card__badge">{{ strtoupper($post->type) }}</span>
                </div>
                <div class="post-card__meta">
                  <span class="post-card__cat">{{ strtoupper($post->category?->name ?: 'INSIGHTS') }}</span>
                  <time datetime="{{ $post->published_at?->format('Y-m-d') }}">{{ $post->published_at?->format('d M Y') ?? 'LATEST' }}</time>
                  <span>{{ $post->views_count }} VIEWS</span>
                </div>
                <h3 class="post-card__title">{{ $post->title }}</h3>
                <p class="post-card__excerpt">{{ $post->excerpt }}</p>
              </a>
            </article>
          @empty
            <div style="grid-column: 1 / -1; padding: 4rem 2rem; text-align: center; background: #ffffff; border: 1px dashed #DCDCD4; border-radius: 8px;">
              <h3 class="t-h3">No articles or episodes found</h3>
              <p style="color: #62718A; margin-top: 0.5rem;">Try adjusting your search terms or filter selection.</p>
              <button type="button" wire:click="setType('all')" class="btn btn--accent btn--sm" style="margin-top: 1rem;">View All Entries</button>
            </div>
          @endforelse
        </div>
      </div>
    </section>
</div>
