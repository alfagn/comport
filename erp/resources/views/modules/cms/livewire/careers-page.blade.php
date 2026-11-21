<div>
    <!-- Careers Portal Hero (jobs.empetix.com) -->
    <section class="jobs-hero" aria-labelledby="jobs-hero-title">
      <div class="jobs-hero__bg" aria-hidden="true"></div>
      <div class="container">
        <div class="jobs-hero__grid">
          <div class="jobs-hero__main">
            <span class="eyebrow eyebrow--inverse">Empetix Talent Portal &middot; jobs.empetix.com</span>
            <h1 id="jobs-hero-title" class="jobs-hero__title">
              Build software that endures and campaigns that move numbers.
            </h1>
            <p class="jobs-hero__lead">
              Welcome to our dedicated careers portal. Explore open engineering, product design, and performance marketing roles across our New Cairo studios—or apply remotely across EMEA.
            </p>
          </div>

          <div class="jobs-hero__stats">
            <div class="jobs-hero__stat">
              <span class="jobs-hero__stat-num">{{ $jobs->count() }}</span>
              <span class="jobs-hero__stat-label">Open Positions</span>
            </div>
            <div class="jobs-hero__stat">
              <span class="jobs-hero__stat-num">48<span>h</span></span>
              <span class="jobs-hero__stat-label">Offer Turnaround</span>
            </div>
            <div class="jobs-hero__stat">
              <span class="jobs-hero__stat-num">28<span>d</span></span>
              <span class="jobs-hero__stat-label">Paid Annual Leave</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Open Roles Section -->
    <section id="open-roles" class="section">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 class="t-h2">Current Open Vacancies ({{ $jobs->count() }})</h2>
            <p class="t-lead">We review every portfolio and CV within 48 business hours. No automated algorithmic rejections.</p>
          </div>
        </div>

        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 2rem;">
          <button type="button" wire:click="setDepartment('all')" class="filter__btn" aria-pressed="{{ $department === 'all' ? 'true' : 'false' }}">
            All Teams
          </button>
          <button type="button" wire:click="setDepartment('Engineering')" class="filter__btn" aria-pressed="{{ $department === 'Engineering' ? 'true' : 'false' }}">
            Engineering
          </button>
          <button type="button" wire:click="setDepartment('Marketing')" class="filter__btn" aria-pressed="{{ $department === 'Marketing' ? 'true' : 'false' }}">
            Marketing
          </button>
          <button type="button" wire:click="setDepartment('Design')" class="filter__btn" aria-pressed="{{ $department === 'Design' ? 'true' : 'false' }}">
            Design
          </button>
        </div>

        <div style="display: grid; gap: 1.25rem;">
          @forelse($jobs as $job)
            <div style="background: #ffffff; border: 1px solid #DCDCD4; border-radius: 8px; padding: 1.75rem; display: flex; flex-direction: column; md:flex-row; justify-content: space-between; align-items: flex-start; gap: 1.5rem; transition: border-color 0.2s;" onmouseover="this.style.borderColor='#FF4D1C'" onmouseout="this.style.borderColor='#DCDCD4'">
              <div>
                <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem; align-items: center;">
                  <span class="hero__badge" style="font-size: 0.75rem;">{{ strtoupper($job->department) }}</span>
                  <span style="font-size: 0.8rem; color: #62718A;">&middot; {{ ucwords(str_replace('_', ' ', $job->employment_type)) }}</span>
                  <span style="font-size: 0.8rem; color: #62718A;">&middot; {{ $job->location }}</span>
                </div>
                <h3 style="font-size: 1.35rem; font-weight: 700; color: #08142C; margin-bottom: 0.5rem;">
                  <a href="{{ route('site.job.detail', $job->slug) }}" style="color: inherit; text-decoration: none;">
                    {{ $job->title }}
                  </a>
                </h3>
                <p style="color: #62718A; font-size: 0.95rem; line-height: 1.5; max-width: 800px;">
                  {{ Str::limit($job->description, 180) }}
                </p>
                @if($job->salary_range)
                  <span style="display: inline-block; margin-top: 0.5rem; font-size: 0.85rem; font-weight: 700; color: #059669;">
                    Compensation: {{ $job->salary_range }}
                  </span>
                @endif
              </div>

              <div>
                <a href="{{ route('site.job.detail', $job->slug) }}" class="btn btn--accent btn--sm" style="white-space: nowrap;">
                  <span>View Role &amp; Apply &rarr;</span>
                </a>
              </div>
            </div>
          @empty
            <div style="padding: 3rem; text-align: center; background: #ffffff; border: 1px dashed #DCDCD4; border-radius: 8px;">
              <h3 class="t-h3">No openings currently in this department</h3>
              <p style="color: #62718A; margin-top: 0.5rem;">Check back soon or send an open pitch to talent@empetix.com.</p>
            </div>
          @endforelse
        </div>
      </div>
    </section>

    <!-- Benefits & Culture -->
    <section id="benefits" class="section section--alt section--ruled">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 class="t-h2">Life &amp; Benefits at Empetix</h2>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem;">
          <div style="background: #ffffff; border: 1px solid #DCDCD4; border-radius: 8px; padding: 1.5rem;">
            <h4 style="font-weight: 700; color: #08142C; margin-bottom: 0.5rem;">Top-Tier Hardware</h4>
            <p style="color: #62718A; font-size: 0.9rem; line-height: 1.6;">Latest Apple M3 Max MacBook Pro or high-end Linux workstations with 4K displays.</p>
          </div>
          <div style="background: #ffffff; border: 1px solid #DCDCD4; border-radius: 8px; padding: 1.5rem;">
            <h4 style="font-weight: 700; color: #08142C; margin-bottom: 0.5rem;">Flexible Remote / Hybrid</h4>
            <p style="color: #62718A; font-size: 0.9rem; line-height: 1.6;">Work from our sunny New Cairo studio or anywhere across EMEA timezones.</p>
          </div>
          <div style="background: #ffffff; border: 1px solid #DCDCD4; border-radius: 8px; padding: 1.5rem;">
            <h4 style="font-weight: 700; color: #08142C; margin-bottom: 0.5rem;">Learning &amp; Growth</h4>
            <p style="color: #62718A; font-size: 0.9rem; line-height: 1.6;">Annual USD 2,000 personal budget for conferences, Laracasts, books, and courses.</p>
          </div>
          <div style="background: #ffffff; border: 1px solid #DCDCD4; border-radius: 8px; padding: 1.5rem;">
            <h4 style="font-weight: 700; color: #08142C; margin-bottom: 0.5rem;">Premium Health Cover</h4>
            <p style="color: #62718A; font-size: 0.9rem; line-height: 1.6;">100% employer-funded comprehensive health insurance for you and your dependents.</p>
          </div>
        </div>
      </div>
    </section>
</div>
