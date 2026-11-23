@extends('layouts.public', ['activePage' => 'contact'])

@section('meta')
  <meta name="description" content="Contact Empetix's co-founders and practice leads in New Cairo for software engineering, growth marketing, or corporate partnerships."/>
  <title>Contact Us & Start a Project — Empetix Studio (Cairo, Riyadh, London)</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="Contact Us & Start a Project — Empetix Studio (Cairo, Riyadh, London)"/>
  <meta property="og:description" content="Contact Empetix's co-founders and practice leads in New Cairo for software engineering, growth marketing, or corporate partnerships."/>
  <meta property="og:url" content="https://empetix.vercel.app/contact.html"/>
  <meta property="og:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:secure_url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution Logo"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="Contact Us & Start a Project — Empetix Studio (Cairo, Riyadh, London)"/>
  <meta name="twitter:description" content="Contact Empetix's co-founders and practice leads in New Cairo for software engineering, growth marketing, or corporate partnerships."/>
  <meta name="twitter:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
@endsection

@section('content')
<main id="main-content">

    <!-- 1. Dark Architectural Contact Header -->
    <section class="page-hero page-hero--dark">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ url('/') }}">Home</a></li>
          <li aria-current="page">Contact Us</li>
        </ol>
        <h1 class="page-hero__title">Start a project, request a technical audit, or partner with our studios.</h1>
        <div class="page-hero__row">
          <div class="page-hero__label">
            <span class="eyebrow eyebrow--inverse">Direct Executive Access</span>
          </div>
          <p class="page-hero__lead">
            At Empetix, you never speak with junior sales reps. Every inquiry goes directly to our four co-founders and practice heads in New Cairo.
          </p>
          <div class="page-hero__aside">
            <span class="hero__badge">AVG RESPONSE: &lt; 6 BUSINESS HOURS</span>
          </div>
        </div>
      </div>
    </section>

    <!-- 2. Four Direct Department Desks with One-Click Copy -->
    <section class="section">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 class="t-h2">Reach the right practice desk immediately.</h2>
          </div>
        </div>

        <div class="contact-channels">
          <!-- LOOP: channel-card -->
          <article class="channel-card reveal">
            <div>
              <div class="channel-card__top">
                <span class="channel-card__tag">NEW BUSINESS</span>
                <span class="channel-card__sla">SLA: &lt; 24H</span>
              </div>
              <h3 class="channel-card__title">Product &amp; Growth Projects</h3>
              <p class="channel-card__desc">
                For custom web/mobile software builds, FinTech &amp; e-commerce platforms, or full-funnel marketing retainers.
              </p>
            </div>
            <div class="channel-card__foot">
              <a href="mailto:hello&#64;empetix.com" class="channel-card__email">hello&#64;empetix.com</a>
              <button type="button" class="channel-card__copy" data-copy-email="hello&#64;empetix.com">Copy</button>
            </div>
          </article>
          <!-- END LOOP: channel-card -->

          <!-- LOOP: channel-card -->
          <article class="channel-card reveal">
            <div>
              <div class="channel-card__top">
                <span class="channel-card__tag">ENTERPRISE &amp; ALLIANCES</span>
                <span class="channel-card__sla">SLA: &lt; 24H</span>
              </div>
              <h3 class="channel-card__title">Corporate Partnerships</h3>
              <p class="channel-card__desc">
                For dedicated embedded engineering squads, AWS/Shopify Plus co-delivery, and formal enterprise RFPs.
              </p>
            </div>
            <div class="channel-card__foot">
              <a href="mailto:partners&#64;empetix.com" class="channel-card__email">partners&#64;empetix.com</a>
              <button type="button" class="channel-card__copy" data-copy-email="partners&#64;empetix.com">Copy</button>
            </div>
          </article>
          <!-- END LOOP: channel-card -->

          <!-- LOOP: channel-card -->
          <article class="channel-card reveal">
            <div>
              <div class="channel-card__top">
                <span class="channel-card__tag">TALENT &amp; CAREERS</span>
                <span class="channel-card__sla">6 OPEN ROLES</span>
              </div>
              <h3 class="channel-card__title">Engineering &amp; Creative Talent</h3>
              <p class="channel-card__desc">
                For senior engineers, product designers, filmmakers, and performance marketers joining our studios.
              </p>
            </div>
            <div class="channel-card__foot">
              <a href="mailto:talent&#64;empetix.com" class="channel-card__email">talent&#64;empetix.com</a>
              <button type="button" class="channel-card__copy" data-copy-email="talent&#64;empetix.com">Copy</button>
            </div>
          </article>
          <!-- END LOOP: channel-card -->

          <!-- LOOP: channel-card -->
          <article class="channel-card reveal">
            <div>
              <div class="channel-card__top">
                <span class="channel-card__tag">MEDIA &amp; SPEAKING</span>
                <span class="channel-card__sla">PRESS DESK</span>
              </div>
              <h3 class="channel-card__title">Press &amp; Keynotes</h3>
              <p class="channel-card__desc">
                For conference speaking invitations, podcast appearances, press kits, and industry research citations.
              </p>
            </div>
            <div class="channel-card__foot">
              <a href="mailto:press&#64;empetix.com" class="channel-card__email">press&#64;empetix.com</a>
              <button type="button" class="channel-card__copy" data-copy-email="press&#64;empetix.com">Copy</button>
            </div>
          </article>
          <!-- END LOOP: channel-card -->
        </div>

        <!-- 3. Interactive Project & Partnership Configurator + Executive Promise Sidebar -->
        <div class="contact">
          <div class="contact__form reveal">
            <div class="form-card">
              <div class="form-card__head">
                <div>
                  <span class="eyebrow">02 / Interactive Project Brief</span>
                  <h2 class="t-h3">Configure your project or partnership brief.</h2>
                </div>
                <span class="t-meta">PROTECTED BY MUTUAL NDA</span>
              </div>

              <form class="form" action="{{ route('contact.store') }}" method="post" data-interactive-form data-success-msg="Thank you. Your brief has been delivered directly to Co-Founders Ayman Ibrahim (CEO) and Bishoy Magdy (VP Growth). We will reply with a proposed architecture &amp; growth call time within 24 business hours.">
                @csrf
                @if(session('success'))
                  <p class="form__success" role="status" style="color:#1a7f4b;font-weight:600">{{ session('success') }}</p>
                @endif
                <!-- Step A: Capabilities -->
                <fieldset class="form__group">
                  <legend class="form__label">A. Which capabilities do you need? (Select one or more)</legend>
                  <div class="choices">
                    @foreach($services as $i => $service)
                    <label class="choice">
                      <input type="checkbox" name="service[]" value="{{ $service->title }}" {{ $i === 0 ? 'checked' : '' }}/>
                      <span>{{ $service->title }}</span>
                    </label>
@endforeach
                  </div>
                </fieldset>

                <!-- Step B: Budget Tier -->
                <fieldset class="form__group">
                  <legend class="form__label">B. Estimated Investment Range</legend>
                  <div class="choices">
                    <label class="choice">
                      <input type="radio" name="budget_tier" value="15-35k"/>
                      <span>USD 15k &ndash; 35k (5-Day Sprint / Audit)</span>
                    </label>
                    <label class="choice">
                      <input type="radio" name="budget_tier" value="35-80k" checked/>
                      <span>USD 35k &ndash; 80k (Full Product / Campaign)</span>
                    </label>
                    <label class="choice">
                      <input type="radio" name="budget_tier" value="80-150k"/>
                      <span>USD 80k &ndash; 150k (Platform + Growth Engine)</span>
                    </label>
                    <label class="choice">
                      <input type="radio" name="budget_tier" value="150k+"/>
                      <span>USD 150k+ (Enterprise Multi-Quarter)</span>
                    </label>
                  </div>
                </fieldset>

                <!-- Step C: Contact Details -->
                <div class="form__field">
                  <label for="c-name" class="form__label">Full Name <abbr title="Required">*</abbr></label>
                  <input id="c-name" name="name" type="text" class="form__input" required placeholder="e.g. Tariq Al-Faisal"/>
                </div>

                <div class="form__field">
                  <label for="c-email" class="form__label">Work Email <abbr title="Required">*</abbr></label>
                  <input id="c-email" name="email" type="email" class="form__input" required placeholder="name&#64;company.com"/>
                </div>

                <div class="form__field">
                  <label for="c-company" class="form__label">Company &amp; Website <abbr title="Required">*</abbr></label>
                  <input id="c-company" name="company" type="text" class="form__input" required placeholder="e.g. Acme FinTech (acme.com)"/>
                </div>

                <div class="form__field">
                  <label for="c-timeline" class="form__label">Target Kickoff Window</label>
                  <select id="c-timeline" name="timeline" class="form__select">
                    <option value="immediate">Immediate (Within 2 Weeks)</option>
                    <option value="month">Within 30 Days</option>
                    <option value="quarter">Next Quarter Planning</option>
                    <option value="exploring">Exploratory / RFP Stage</option>
                  </select>
                </div>

                <div class="form__field form__field--full">
                  <label for="c-brief" class="form__label">Project Context, Technical Stack &amp; Commercial Goals <abbr title="Required">*</abbr></label>
                  <textarea id="c-brief" name="brief" class="form__textarea" required placeholder="Tell us what you are building or scaling, what bottleneck you want to solve, and any key launch dates..."></textarea>
                </div>

                <div class="form__actions">
                  <p class="form__consent">
                    We sign mutual NDAs prior to reviewing private repositories or ad accounts. Zero spam, ever.
                  </p>
                  <button type="submit" class="btn btn--primary btn--lg">
                    <span>Submit Project Brief</span>
                    <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                      <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                  </button>
                </div>

                <div class="form__status" data-form-status role="status" aria-live="polite"></div>
              </form>
            </div>
          </div>

          <!-- Executive Promise Sidebar -->
          <aside class="contact__aside reveal">
            <div class="contact-promise">
              <span class="eyebrow eyebrow--inverse">What Happens Next</span>
              <h3 class="contact-promise__title">Direct founder review within 24 hours.</h3>
              <ul class="contact-promise__list">
                <li><strong>1. Technical &amp; Commercial Triage:</strong> Your brief is reviewed directly by our CEO, CTO, or VP of Growth.</li>
                <li><strong>2. Mutual NDA &amp; Discovery Call:</strong> A focused 30-minute architecture and growth call—no generic slide deck fluff.</li>
                <li><strong>3. Written Scope &amp; Roadmap:</strong> Within 4 days of our call, you receive a fixed-scope engineering &amp; media proposal with clear milestones.</li>
              </ul>
              <p class="t-small"><a href="{{ url('/team') }}" class="text-link">Meet the full Empetix team &rarr;</a></p>
            </div>

            <div class="contact-block">
              <span class="contact-block__title">Immediate Executive Hotline</span>
              <a href="tel:+20227928400" class="contact-block__big">+20 2 2792 8400</a>
              <p class="t-small">Sunday &ndash; Thursday, 09:00 &ndash; 18:00 Cairo / Riyadh Time.</p>
            </div>
          </aside>
        </div>
      </div>
    </section>

        <!-- 4. Single Headquarters Showcase -->
    <section class="section section--alt section--ruled" aria-labelledby="studios-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="studios-heading" class="t-h2">Visit our headquarters in New Cairo.</h2>
          </div>
        </div>

        <article class="hq-showcase reveal">
          <div class="hq-showcase__media">
            <img src="{{ asset('assets/img/home/hero.jpg') }}" alt="Empetix Solutions Headquarters in New Cairo" width="1200" height="675"/>
          </div>
          <div class="hq-showcase__body">
            <div>
              <div class="office__top">
                <span class="office__role">EMPETIX SOLUTIONS HEADQUARTERS</span>
                <span class="office__clock"><span data-tz-clock="Africa/Cairo">12:00</span> CAIRO</span>
              </div>
              <h3 class="t-h2">Cairo, Egypt</h3>
              <p class="t-small">
                All software engineering, product design, commercial production, and performance marketing squads operate together under one roof at our New Cairo campus.
              </p>
            </div>

            <div class="hq-showcase__list">
              <div class="hq-showcase__row">
                <span>Address</span>
                <span>Building 14, District 5 Campus, New Cairo</span>
              </div>
              <div class="hq-showcase__row">
                <span>Working Hours</span>
                <span>Sunday &ndash; Thursday &middot; 09:00 &ndash; 18:00</span>
              </div>
              <div class="hq-showcase__row">
                <span>Direct Telephone</span>
                <span><a href="tel:+20227928400" class="text-link">+20 2 2792 8400</a></span>
              </div>
              <div class="hq-showcase__row">
                <span>New Business Desk</span>
                <span><a href="mailto:hello&#64;empetix.com" class="text-link">hello&#64;empetix.com</a></span>
              </div>
            </div>

            <a href="#main-content" class="btn btn--primary">
              <span>Schedule an In-Person Studio Visit</span>
              <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </a>
          </div>
        </article>
      </div>
    </section>

    <!-- 5. Executive FAQ Accordion -->
    <section class="section" aria-labelledby="faq-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="faq-heading" class="t-h2">Common questions before we kick off.</h2>
          </div>
        </div>

        <div class="accordion reveal">
          <details class="accordion__item" open>
            <summary class="accordion__summary">
              <span>01. Can we hire Empetix for software engineering only, or growth marketing only?</span>
              <span class="accordion__icon" aria-hidden="true"></span>
            </summary>
            <div class="accordion__body">
              <p>
                Yes. Around 40% of our clients start with a single practice—such as rebuilding a Laravel/Flutter platform or overhauling paid acquisition and CRO—and expand into a dual-practice partnership once they see the velocity gains of having both teams under one roof.
              </p>
            </div>
          </details>

          <details class="accordion__item">
            <summary class="accordion__summary">
              <span>02. How quickly can an Empetix squad start on our project?</span>
              <span class="accordion__icon" aria-hidden="true"></span>
            </summary>
            <div class="accordion__body">
              <p>
                For a 5-Day Architecture &amp; Growth Sprint, we can typically mobilize within 7 to 10 business days. For full dedicated engineering and marketing squads, onboarding takes 2 weeks from signed agreement.
              </p>
            </div>
          </details>

          <details class="accordion__item">
            <summary class="accordion__summary">
              <span>03. Who owns the source code, design tokens, raw video footage, and ad accounts?</span>
              <span class="accordion__icon" aria-hidden="true"></span>
            </summary>
            <div class="accordion__body">
              <p>
                You do—100%. All GitHub repositories, Figma design systems, ad accounts (Meta, Google, TikTok), and commercial film footage are provisioned under your organization&rsquo;s ownership from day one.
              </p>
            </div>
          </details>

          <details class="accordion__item">
            <summary class="accordion__summary">
              <span>04. How do you structure pricing—fixed project fee or monthly retainer?</span>
              <span class="accordion__icon" aria-hidden="true"></span>
            </summary>
            <div class="accordion__body">
              <p>
                We offer both: fixed-milestone project pricing for clearly scoped v1.0 platform builds and brand launches, and transparent monthly squad retainers for continuous software delivery, CRO, and performance media scaling.
              </p>
            </div>
          </details>
        </div>
      </div>
    </section>
@endsection
