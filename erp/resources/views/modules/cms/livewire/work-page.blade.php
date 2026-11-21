<div>
    <!-- Page Hero -->
    <section class="page-hero">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ route('home') }}">Home</a></li>
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
            <span class="t-meta">{{ $projects->count() }} CASE STUDIES</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Project Archive & Filter -->
    <section class="section section--flush-top" aria-label="Project archive">
      <div class="container">
        <!-- Livewire Interactive Filter Bar -->
        <div class="filter" role="toolbar" aria-label="Filter projects by discipline" style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 2.5rem;">
          <button type="button" wire:click="setFilter('all')" class="filter__btn" aria-pressed="{{ $filter === 'all' ? 'true' : 'false' }}">
            All Projects
          </button>
          <button type="button" wire:click="setFilter('software-engineering')" class="filter__btn" aria-pressed="{{ $filter === 'software-engineering' ? 'true' : 'false' }}">
            Software Engineering
          </button>
          <button type="button" wire:click="setFilter('fintech')" class="filter__btn" aria-pressed="{{ $filter === 'fintech' ? 'true' : 'false' }}">
            FinTech &amp; Wallets
          </button>
          <button type="button" wire:click="setFilter('growth-marketing')" class="filter__btn" aria-pressed="{{ $filter === 'growth-marketing' ? 'true' : 'false' }}">
            Growth &amp; Marketing
          </button>
        </div>

        <!-- Projects Grid -->
        <div class="work-grid">
          @forelse($projects as $project)
            <article class="project-card {{ $loop->iteration % 2 === 1 ? 'project-card--wide' : 'project-card--narrow' }} {{ $loop->iteration > 2 ? 'project-card--offset' : '' }} reveal">
              <a href="{{ route('site.work.detail', $project->slug) }}" class="project-card__link">
                <div class="frame project-card__frame frame--zoom">
                  <img src="{{ asset($project->featured_image ?: 'assets/img/work/nilepay.jpg') }}" alt="{{ $project->title }}" width="1200" height="750"/>
                </div>
                <div class="project-card__body">
                  <span class="project-card__client">{{ strtoupper($project->client_name ?: 'EMPETIX CLIENT') }} &middot; {{ strtoupper($project->category?->name ?: 'CASE STUDY') }}</span>
                  <span class="project-card__year">{{ $project->completion_date?->format('Y') ?? date('Y') }}</span>
                  <h3 class="project-card__title">{{ $project->title }}</h3>
                  <p class="t-body" style="font-size: 0.95rem; color: #62718A; margin-top: 0.4rem;">{{ $project->excerpt }}</p>
                </div>
              </a>
            </article>
          @empty
            <div style="grid-column: 1 / -1; padding: 4rem 2rem; text-align: center; background: #ffffff; border: 1px dashed #DCDCD4; border-radius: 8px;">
              <h3 class="t-h3">No projects found in this discipline</h3>
              <p style="color: #62718A; margin-top: 0.5rem;">Select another filter or reset to view all case studies.</p>
              <button type="button" wire:click="setFilter('all')" class="btn btn--accent btn--sm" style="margin-top: 1rem;">Reset Filter</button>
            </div>
          @endforelse
        </div>
      </div>
    </section>
</div>
