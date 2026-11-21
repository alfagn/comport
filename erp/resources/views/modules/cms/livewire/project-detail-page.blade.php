<div>
    <!-- Case Study Header -->
    <section class="article-head">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ route('home') }}">Home</a></li>
          <li><a href="{{ route('site.work') }}">Projects</a></li>
          <li aria-current="page">{{ $project->title }}</li>
        </ol>

        <div class="article-head__meta">
          <span class="article-head__cat">{{ strtoupper($project->category?->name ?: 'CASE STUDY') }}</span>
          <span>{{ strtoupper($project->client_name ?: 'CLIENT DOSSIER') }}</span>
          <span>{{ $project->completion_date?->format('Y') ?? date('Y') }}</span>
        </div>

        <h1 class="article-head__title">{{ $project->title }}</h1>
        <p class="article-head__dek">
          {{ $project->excerpt }}
        </p>

        <dl class="facts">
          <div>
            <dt>Client</dt>
            <dd>{{ $project->client_name ?: 'Confidential Client' }}</dd>
          </div>
          <div>
            <dt>Discipline</dt>
            <dd>{{ $project->category?->name ?: 'Software Engineering & Growth' }}</dd>
          </div>
          <div>
            <dt>Delivered</dt>
            <dd>{{ $project->completion_date?->format('F Y') ?? 'Recent' }}</dd>
          </div>
          <div>
            <dt>Project URL</dt>
            <dd>
              @if($project->project_url)
                <a href="{{ $project->project_url }}" target="_blank" rel="noopener" style="color: #FF4D1C; font-weight: 700;">Visit Platform &rarr;</a>
              @else
                <span>Enterprise Production</span>
              @endif
            </dd>
          </div>
        </dl>
      </div>
    </section>

    <!-- Featured Visual -->
    <div class="container" style="margin-bottom: 3rem;">
      <div class="frame frame--16x9 frame--zoom" style="border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
        <img src="{{ asset($project->featured_image ?: 'assets/img/work/nilepay.jpg') }}" alt="{{ $project->title }}" width="1920" height="1080"/>
      </div>
    </div>

    <!-- Case Study Narrative Body -->
    <section class="section section--flush-top">
      <div class="container" style="max-width: 900px;">
        <div style="font-size: 1.15rem; line-height: 1.8; color: #1C2E52;">
          {!! nl2br(e($project->description)) !!}
        </div>

        @if(!empty($project->gallery_images))
          <div style="margin-top: 3rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
            @foreach($project->gallery_images as $img)
              <div class="frame frame--3x2" style="border-radius: 6px; overflow: hidden;">
                <img src="{{ asset($img) }}" alt="Project screen" width="600" height="400"/>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </section>

    <!-- Related Projects / Next Case Studies -->
    @if($relatedProjects->isNotEmpty())
      <section class="section section--alt section--ruled" aria-labelledby="related-heading">
        <div class="container">
          <div class="section-head reveal">
            <div class="section-head__label"></div>
            <div class="section-head__title">
              <h2 id="related-heading" class="t-h3">More Selected Case Studies</h2>
            </div>
            <div class="section-head__action">
              <a href="{{ route('site.work') }}" class="arrow-link">
                <span>View All Projects</span>
                <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
              </a>
            </div>
          </div>

          <div class="work-grid">
            @foreach($relatedProjects as $rel)
              <article class="project-card project-card--wide reveal">
                <a href="{{ route('site.work.detail', $rel->slug) }}" class="project-card__link">
                  <div class="frame project-card__frame frame--zoom">
                    <img src="{{ asset($rel->featured_image ?: 'assets/img/work/nilepay.jpg') }}" alt="{{ $rel->title }}" width="1200" height="750"/>
                  </div>
                  <div class="project-card__body">
                    <span class="project-card__client">{{ strtoupper($rel->client_name ?: 'EMPETIX CLIENT') }}</span>
                    <h3 class="project-card__title">{{ $rel->title }}</h3>
                  </div>
                </a>
              </article>
            @endforeach
          </div>
        </div>
      </section>
    @endif
</div>
