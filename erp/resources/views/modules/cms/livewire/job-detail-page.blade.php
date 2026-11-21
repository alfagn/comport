<div>
    <!-- Job Requisition Header -->
    <header class="job-header">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ route('home') }}">Home</a></li>
          <li><a href="{{ route('site.careers') }}">Careers</a></li>
          <li aria-current="page">{{ $job->title }}</li>
        </ol>

        <div class="job-header__grid">
          <div class="job-header__main">
            <div class="job-header__badges">
              <span class="hero__badge">{{ strtoupper($job->department) }}</span>
              <span class="hero__badge">{{ ucwords(str_replace('_', ' ', $job->employment_type)) }}</span>
              <span class="hero__badge">{{ $job->location }}</span>
            </div>
            <h1 class="job-header__title">{{ $job->title }}</h1>
          </div>

          <div class="job-header__cta">
            <a href="#apply-section" class="btn btn--accent btn--lg">
              <span>Apply for this Role &darr;</span>
            </a>
          </div>
        </div>

        <div class="job-header__highlights">
          <div>
            <p class="job-header__highlight-label">Department</p>
            <p class="job-header__highlight-val">{{ $job->department }}</p>
          </div>
          <div>
            <p class="job-header__highlight-label">Location</p>
            <p class="job-header__highlight-val">{{ $job->location }}</p>
          </div>
          <div>
            <p class="job-header__highlight-label">Type</p>
            <p class="job-header__highlight-val">{{ ucwords(str_replace('_', ' ', $job->employment_type)) }}</p>
          </div>
          <div>
            <p class="job-header__highlight-label">Compensation</p>
            <p class="job-header__highlight-val">{{ $job->salary_range ?: 'Competitive + Equity' }}</p>
          </div>
        </div>
      </div>
    </header>

    <!-- Job Description & Specs -->
    <section class="section section--flush-top">
      <div class="container" style="max-width: 880px;">
        <div style="font-size: 1.15rem; line-height: 1.8; color: #1C2E52; margin-bottom: 2.5rem;">
          <h2 class="t-h3" style="margin-bottom: 1rem;">Role Overview</h2>
          {!! nl2br(e($job->description)) !!}
        </div>

        @if($job->requirements)
          <div style="font-size: 1.1rem; line-height: 1.8; color: #1C2E52; margin-bottom: 3rem;">
            <h2 class="t-h3" style="margin-bottom: 1rem;">Key Requirements &amp; Qualifications</h2>
            {!! nl2br(e($job->requirements)) !!}
          </div>
        @endif

        <!-- Livewire Interactive Application Form -->
        <div id="apply-section" class="form-card" style="margin-top: 4rem;">
          <div class="form-card__head">
            <div>
              <span class="eyebrow">Application Form</span>
              <h2 class="t-h3">Apply for {{ $job->title }}</h2>
            </div>
            <span class="t-meta">48H RESPONSE GUARANTEE</span>
          </div>

          @if($submitted)
            <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem;">
              <h4 style="color: #15803d; font-weight: 700; margin-bottom: 0.5rem; font-size: 1.1rem;">&#10003; Application Submitted!</h4>
              <p style="color: #166534; font-size: 0.95rem; line-height: 1.6;">
                Thank you! Your profile and application have been submitted directly to the Empetix hiring squad. Our engineering and practice leads review applications daily and will get back to you within 48 business hours.
              </p>
            </div>
          @endif

          <form wire:submit="submitApplication" class="form">
            <div class="form__field">
              <label for="c-name" class="form__label">Full Legal Name <abbr title="Required">*</abbr></label>
              <input id="c-name" wire:model="candidate_name" type="text" class="form__input" placeholder="e.g. Mostafa El-Sayed" required/>
              @error('candidate_name') <span style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form__field">
              <label for="c-email" class="form__label">Email Address <abbr title="Required">*</abbr></label>
              <input id="c-email" wire:model="candidate_email" type="email" class="form__input" placeholder="you@example.com" required/>
              @error('candidate_email') <span style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form__field">
              <label for="c-phone" class="form__label">Phone / WhatsApp Number</label>
              <input id="c-phone" wire:model="candidate_phone" type="tel" class="form__input" placeholder="+20 10 ... or +966 5 ..."/>
              @error('candidate_phone') <span style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form__field">
              <label for="c-portfolio" class="form__label">GitHub / Portfolio / LinkedIn URL</label>
              <input id="c-portfolio" wire:model="portfolio_url" type="url" class="form__input" placeholder="https://github.com/..."/>
              @error('portfolio_url') <span style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form__field form__field--full">
              <label for="c-resume" class="form__label">Upload Resume / CV (PDF, DOC, DOCX up to 10MB)</label>
              <input id="c-resume" wire:model="resume" type="file" class="form__input" accept=".pdf,.doc,.docx"/>
              <div wire:loading wire:target="resume" style="color: #FF4D1C; font-size: 0.85rem; margin-top: 0.25rem;">Uploading CV...</div>
              @error('resume') <span style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form__field form__field--full">
              <label for="c-cover" class="form__label">Why Empetix? / Cover Note</label>
              <textarea id="c-cover" wire:model="cover_letter" class="form__textarea" rows="4" placeholder="Briefly share the most challenging system you built or campaign you launched..."></textarea>
              @error('cover_letter') <span style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form__actions">
              <button type="submit" class="btn btn--accent btn--lg">
                <span>Submit Job Application</span>
                <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                  <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
            </div>
          </form>
        </div>
      </div>
    </section>
</div>
