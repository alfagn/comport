@extends('layouts.public', ['activePage' => 'careers'])

@section('meta')
  <meta name="description" content="{{ $job->summary }}"/>
  <title>{{ $job->title }} — Careers at Empetix Solution</title>
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="{{ $job->title }} — Careers at Empetix Solution"/>
  <meta property="og:description" content="{{ $job->summary }}"/>
  <meta property="og:url" content="{{ seo_current_url() }}"/>
  <meta property="og:image" content="{{ seo_og_image() }}"/>
@endsection

@section('content')
<main id="main-content">

    <!-- 1. JOB REQUISITION HEADER -->
    <header class="job-header">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ url('/careers') }}">{{ sec('job', 'copy_c0d554c08817') }}</a></li>
          <li><a href="{{ url('/careers') }}#open-roles">{{ sec('job', 'copy_f665170f831d') }}</a></li>
          <li aria-current="page">{{ $job->ref_code ?? $job->title }}</li>
        </ol>

        <div class="job-header__grid">
          <div class="job-header__main">
            <div class="job-header__badges">
              @if($job->ref_code)<span class="hero__badge">{{ $job->ref_code }}</span>@endif
              @if($job->department)<span class="hero__badge">{{ strtoupper($job->department) }}</span>@endif
              <span class="hero__badge">{{ sec('job', 'posted_prefix') }} {{ strtoupper($job->created_at->diffForHumans()) }}</span>
            </div>
            <h1 class="job-header__title">{{ $job->title }}</h1>
          </div>

          <div class="job-header__cta">
            <button type="button" class="btn btn--accent btn--lg" data-trigger-apply>
              <span>{{ sec('careers', 'apply_button_open', 'Apply for this Job') }}</span>
              <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="job-header__highlights">
          @foreach((array) $job->pills as $pill)
            @php([$hlLabel, $hlValue] = array_pad(array_map('trim', explode(':', (string) $pill, 2)), 2, ''))
            <div>
              <p class="job-header__highlight-label">{{ $hlLabel }}</p>
              <p class="job-header__highlight-val">{{ $hlValue }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </header>

    <!-- 2. FULL JOB SPECIFICATION (Overview, Responsibilities, Qualifications & Requirements) -->
    <section class="section">
      <div class="container">
        <div class="job-layout">
          <!-- Main 8-Column Specification -->
          <div class="job-layout__main">
            @foreach((array) $job->sections as $section)
              <div class="job-section">
                <h2 class="job-section__title">
                  <span class="job-section__num">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                  <span>{{ $section['heading'] ?? '' }}</span>
                </h2>
                <ul class="req-list">
                  @foreach((array) ($section['items'] ?? []) as $item)
                    <li>{{ $item }}</li>
                  @endforeach
                </ul>
              </div>
            @endforeach
            @if((array) $job->tags)
              <div class="job-section">
                <h2 class="job-section__title">
                  <span class="job-section__num">{{ str_pad((string) (count((array) $job->sections) + 1), 2, '0', STR_PAD_LEFT) }}</span>
                  <span>{{ sec('job', 'copy_c12c0b3fcd51') }}</span>
                </h2>
                <ul class="tag-list">
                  @foreach((array) $job->tags as $tag)
                    <li>{{ $tag }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
<h3 class="t-h4">{{ sec('job', 'copy_d99229d3eb2a') }}</h3>
              </div>
              <button type="button" class="btn btn--accent btn--lg" data-trigger-apply>
                <span>{{ sec('job', 'apply_now') }}{{ $job->ref_code ? ' for '.$job->ref_code : '' }}</span>
                <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                  <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Right 4-Column Sticky Job Summary Card -->
          <aside class="job-layout__aside" aria-label="Job summary and quick apply">
            <div class="job-summary-card">
              <span class="eyebrow">{{ sec('job', 'copy_3e44bd2a5936') }}</span>

              <div class="job-summary-card__list">
                @if($job->ref_code)
                  <div class="job-summary-card__row"><span>{{ sec('job', 'copy_98979abc826f') }}</span><span>{{ $job->ref_code }}</span></div>
                @endif
                @if($job->department)
                  <div class="job-summary-card__row"><span>{{ sec('job', 'copy_d6387b429263') }}</span><span>{{ $job->department }}</span></div>
                @endif
                @foreach((array) $job->pills as $pill)
                  @php([$rowLabel, $rowValue] = array_pad(array_map('trim', explode(':', (string) $pill, 2)), 2, ''))
                  <div class="job-summary-card__row"><span>{{ $rowLabel }}</span><span>{{ $rowValue }}</span></div>
                @endforeach
              </div>

              @if($job->hiringManager)
                <div>
                  <p class="t-meta">{{ sec('job', 'copy_fd6a2174fbff') }}</p>
                  <div class="hiring-manager">
                    @if($job->hiringManager->photo)
                      <img class="hiring-manager__img" src="{{ asset('storage/'.$job->hiringManager->photo) }}" alt="{{ $job->hiringManager->name }}" width="88" height="88"/>
                    @endif
                    <div>
                      <p class="hiring-manager__name">{{ $job->hiringManager->name }}</p>
                      <p class="hiring-manager__role">{{ $job->hiringManager->role }}</p>
                    </div>
                  </div>
                </div>
              @endif

              <button type="button" class="btn btn--primary btn--lg" data-trigger-apply>
                <span>{{ sec('careers', 'apply_button_open', 'Apply for this Job') }}</span>
                <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                  <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>

              <a href="{{ url('/careers') }}#open-roles" class="text-link t-small">&larr; Back to all {{ $openCount }} open jobs</a>
            </div>
          </aside>
        </div>
      </div>
    </section>

    <!-- 3. EXPANDABLE APPLICATION FORM (Hidden until candidate clicks "Apply for this Job") -->
    <section id="job-apply-drawer" class="apply-drawer"@if(!session('success') && !$errors->any()) hidden @endif aria-labelledby="apply-drawer-title">
      <div class="container">
        <div class="form-card">
          <div class="form-card__head">
            <div>
              <span class="eyebrow">{{ sec('careers', 'apply_eyebrow', 'Official Application · jobs.empetix.com') }}</span>
              <h2 id="apply-drawer-title" class="t-h2">{{ sec('job', 'apply_for') }} {{ $job->title }}</h2>
              <p class="t-small">@if($job->ref_code){{ sec('job', 'requisition_prefix') }} <strong>{{ $job->ref_code }}</strong>@endif @if($job->hiringManager) &middot; Reviewed directly by {{ $job->hiringManager->name }} ({{ $job->hiringManager->role }})@endif</p>
            </div>
            <span class="hero__badge">{{ sec('careers', 'apply_badge', 'STEP 1 OF 1 · ~3 MINS') }}</span>
          </div>

          @if(session('success'))
            <div class="form__status form__status--success" role="status">{{ session('success') }}</div>
          @endif
          @if($errors->any())
            <div class="form__status form__status--error" role="alert">
              <strong>{{ sec('job', 'copy_53c08c8da755') }}</strong>
              <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
          @endif
          <form id="job-application-form" class="form" action="{{ route('jobs.apply', $job->slug) }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form__field">
              <label for="app-name" class="form__label">{{ sec('careers', 'apply_label_name', 'Full Legal Name') }} <abbr title="Required">*</abbr></label>
              <input id="app-name" name="full_name" type="text" class="form__input" required placeholder="{{ sec('careers', 'apply_ph_name', 'e.g. Mostafa El-Sayed') }}"/>
            </div>

            <div class="form__field">
              <label for="app-email" class="form__label">{{ sec('careers', 'apply_label_email', 'Email Address') }} <abbr title="Required">*</abbr></label>
              <input id="app-email" name="email" type="email" class="form__input" required placeholder="{{ sec('careers', 'apply_ph_email', 'you@domain.com') }}"/>
            </div>

            <div class="form__field">
              <label for="app-phone" class="form__label">{{ sec('careers', 'apply_label_phone', 'Phone / WhatsApp Number') }} <abbr title="Required">*</abbr></label>
              <input id="app-phone" name="phone" type="tel" class="form__input" required placeholder="{{ sec('careers', 'apply_ph_phone', '+20 10 ... or +966 5 ...') }}"/>
            </div>

            <div class="form__field">
              <label for="app-city" class="form__label">{{ sec('careers', 'apply_label_city', 'Current City & Country') }} <abbr title="Required">*</abbr></label>
              <input id="app-city" name="location" type="text" class="form__input" required placeholder="{{ sec('careers', 'apply_ph_city', 'e.g. Cairo, Egypt') }}"/>
            </div>

            <div class="form__field">
              <label for="app-linkedin" class="form__label">{{ sec('careers', 'apply_label_linkedin', 'LinkedIn Profile URL') }} <abbr title="Required">*</abbr></label>
              <input id="app-linkedin" name="linkedin" type="url" class="form__input" required placeholder="{{ sec('careers', 'apply_ph_linkedin', 'https://linkedin.com/in/...') }}"/>
            </div>

            <div class="form__field">
              <label for="app-portfolio" class="form__label">{{ sec('careers', 'apply_label_portfolio', 'GitHub / Portfolio / Behance / Case Studies URL') }} <abbr title="Required">*</abbr></label>
              <input id="app-portfolio" name="portfolio" type="url" class="form__input" required placeholder="{{ sec('careers', 'apply_ph_portfolio', 'https://github.com/...') }}"/>
            </div>

            <!-- Interactive CV / Resume Upload Box -->
            <div class="form__field form__field--full">
              <label for="app-cv-file" class="form__label">{{ sec('careers', 'apply_label_cv', 'Upload CV / Resume (PDF or DOCX)') }} <abbr title="Required">*</abbr></label>
              <div id="cv-dropzone" class="cv-dropzone">
                <input id="app-cv-file" name="cv_file" type="file" accept=".pdf,.doc,.docx" required/>
                <p id="cv-file-label" class="cv-dropzone__title">{{ sec('careers', 'apply_cv_title', 'Click or drag your CV / Resume here (PDF, DOCX — Max 10MB)') }}</p>
                <p class="cv-dropzone__sub">{{ sec('careers', 'apply_cv_sub', "Your CV goes straight to our practice lead's inbox — no automated ATS keyword rejection.") }}</p>
              </div>
            </div>

            <div class="form__field">
              <label for="app-exp" class="form__label">{{ sec('careers', 'apply_label_exp', 'Years of Relevant Experience') }} <abbr title="Required">*</abbr></label>
              <select id="app-exp" name="experience_years" class="form__select" required>
                <option value="3-5">{{ sec('careers', 'apply_exp_1', '3 – 5 Years') }}</option>
                <option value="5-8" selected>{{ sec('careers', 'apply_exp_2', '5 – 8 Years') }}</option>
                <option value="8+">{{ sec('careers', 'apply_exp_3', '8+ Years (Staff / Principal)') }}</option>
              </select>
            </div>

            <div class="form__field">
              <label for="app-notice" class="form__label">{{ sec('careers', 'apply_label_notice', 'Notice Period / Availability') }} <abbr title="Required">*</abbr></label>
              <select id="app-notice" name="notice_period" class="form__select" required>
                <option value="immediate">{{ sec('careers', 'apply_notice_1', 'Available Immediately') }}</option>
                <option value="2-weeks">{{ sec('careers', 'apply_notice_2', '2 Weeks Notice') }}</option>
                <option value="30-days" selected>{{ sec('careers', 'apply_notice_3', '30 Days Notice') }}</option>
                <option value="60-days">{{ sec('careers', 'apply_notice_4', '60 Days Notice') }}</option>
              </select>
            </div>

            <div class="form__field form__field--full">
              <label for="app-achievement" class="form__label">{{ sec('careers', 'apply_label_achievement', 'Tell us about the most complex system, product, or campaign you have shipped') }} <abbr title="Required">*</abbr></label>
              <textarea id="app-achievement" name="achievement" class="form__textarea" required placeholder="{{ sec('careers', 'apply_ph_achievement', 'Share the technical or commercial challenge, your exact contribution, and the measurable result...') }}"></textarea>
            </div>

            <div class="form__actions">
              <p class="form__consent">
                {{ sec('careers', 'apply_consent', 'By submitting, you consent to Empetix storing your application for recruitment evaluation. We reply to 100% of applicants within 3 business days.') }}
              </p>
              <button type="submit" class="btn btn--accent btn--lg">
                <span>{{ sec('careers', 'apply_button', 'Submit Application') }}{{ $job->ref_code ? ' ('.$job->ref_code.')' : '' }}</span>
                <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                  <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
            </div>

            <div class="form__status" data-job-form-status role="status" aria-live="polite"></div>
          </form>
        </div>
      </div>
    </section>

    <!-- 4. OTHER OPEN POSITIONS ON JOBS.EMPETIX.COM -->
    <section class="section section--alt section--ruled" aria-labelledby="other-jobs-heading">
      <div class="container">
        <div class="section-head">
          <div class="section-head__label">
            <span class="eyebrow">{{ sec('job', 'copy_25270615a39a') }}</span>
          </div>
          <div class="section-head__title">
            <h2 id="other-jobs-heading" class="t-h3">{{ sec('careers', 'other_jobs_heading', 'Other open positions on jobs.empetix.com') }}</h2>
          </div>
          <div class="section-head__action">
            <a href="{{ url('/careers') }}#open-roles" class="arrow-link">View All {{ $openCount }} Jobs &rarr;</a>
          </div>
        </div>

        <div class="jobs-grid">
          @foreach($otherJobs as $other)
            <article class="job-card">
              <div>
                <div class="job-card__meta-top">
                  <span class="job-card__dept">{{ strtoupper($other->department) }}</span>
                  <span class="job-card__req">{{ $other->ref_code }}</span>
                </div>
                <h3 class="job-card__title"><a href="{{ route('jobs.show', $other->slug) }}">{{ $other->title }}</a></h3>
                <div class="job-card__pills">
                  @foreach((array) $other->pills as $pill)
                    @php([$pLabel, $pValue] = array_pad(array_map('trim', explode(':', (string) $pill, 2)), 2, ''))
                    <span class="job-card__pill"><strong>{{ $pLabel }}:</strong> {{ $pValue }}</span>
                  @endforeach
                </div>
              </div>
              <div class="job-card__actions">
                <a href="{{ route('jobs.show', $other->slug) }}" class="btn btn--primary btn--sm">{{ sec('job', 'copy_69ccfe9d9779') }}</a>
              </div>
            </article>
          @endforeach
        </div>
      </div>
    </section>
@endsection
