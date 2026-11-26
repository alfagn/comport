@extends('layouts.public', ['activePage' => 'contact'])

@section('meta')
  <meta name="description" content="{{ sec('contact', 'copy_d6d0b4b6f76b') }}"/>
  <title>{{ sec('contact', 'copy_b33a9603fc54') }}</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="{{ sec('contact', 'copy_b33a9603fc54') }}"/>
  <meta property="og:description" content="{{ sec('contact', 'copy_d6d0b4b6f76b') }}"/>
  <meta property="og:url" content="{{ seo_current_url() }}"/>
  <meta property="og:image" content="{{ seo_og_image() }}"/>
  <meta property="og:image:url" content="{{ seo_og_image() }}"/>
  <meta property="og:image:secure_url" content="{{ seo_og_image() }}"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="{{ sec('contact', 'copy_b33a9603fc54') }}"/>
  <meta name="twitter:description" content="{{ sec('contact', 'copy_d6d0b4b6f76b') }}"/>
  <meta name="twitter:image" content="{{ seo_og_image() }}"/>
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
        <h1 class="page-hero__title">{{ sec('contact', 'hero_title', 'Start a project, request a technical audit, or partner with our studios.') }}</h1>
        <div class="page-hero__row">
          <div class="page-hero__label">
            <span class="eyebrow eyebrow--inverse">{{ sec('contact', 'copy_591febde720a') }}</span>
          </div>
          <p class="page-hero__lead">
            {{ sec('contact', 'copy_7dd28e9d5493') }}
          </p>
          <div class="page-hero__aside">
            <span class="hero__badge">{{ sec('contact', 'copy_deda8e22bf26') }}</span>
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
            <h2 class="t-h2">{{ sec('contact', 'channels_heading', 'Reach the right practice desk immediately.') }}</h2>
          </div>
        </div>

        <div class="contact-channels">
          @foreach(content_list('contact.channels') as $channel)
            @php($channelEmail = setting($channel->url ?: 'email', 'hello@empetix.com'))
            <article class="channel-card reveal">
              <div>
                <div class="channel-card__top">
                  <span class="channel-card__tag">{{ $channel->meta }}</span>
                  <span class="channel-card__sla">{{ $channel->badge }}</span>
                </div>
                <h3 class="channel-card__title">{{ $channel->title }}</h3>
                <p class="channel-card__desc">{{ $channel->body }}</p>
              </div>
              <div class="channel-card__foot">
                <a href="mailto:{{ $channelEmail }}" class="channel-card__email">{{ $channelEmail }}</a>
                <button type="button" class="channel-card__copy" data-copy-email="{{ $channelEmail }}">Copy</button>
              </div>
            </article>
          @endforeach
        </div>

        <!-- 3. Interactive Project & Partnership Configurator + Executive Promise Sidebar -->
        <div class="contact">
          <div class="contact__form reveal">
            <div class="form-card">
              <div class="form-card__head">
                <div>
                  <span class="eyebrow">02 / Interactive Project Brief</span>
                  <h2 class="t-h3">{{ sec('contact', 'form_heading', 'Configure your project or partnership brief.') }}</h2>
                </div>
                <span class="t-meta">PROTECTED BY MUTUAL NDA</span>
              </div>

              <form class="form" action="{{ route('contact.store') }}" method="post">
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
                      <span>{{ sec('contact', 'form_budget_1', 'USD 15k – 35k (5-Day Sprint / Audit)') }}</span>
                    </label>
                    <label class="choice">
                      <input type="radio" name="budget_tier" value="35-80k" checked/>
                      <span>{{ sec('contact', 'form_budget_2', 'USD 35k – 80k (Full Product / Campaign)') }}</span>
                    </label>
                    <label class="choice">
                      <input type="radio" name="budget_tier" value="80-150k"/>
                      <span>{{ sec('contact', 'form_budget_3', 'USD 80k – 150k (Platform + Growth Engine)') }}</span>
                    </label>
                    <label class="choice">
                      <input type="radio" name="budget_tier" value="150k+"/>
                      <span>{{ sec('contact', 'form_budget_4', 'USD 150k+ (Enterprise Multi-Quarter)') }}</span>
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
                  <input id="c-email" name="email" type="email" class="form__input" required placeholder="name@company.com"/>
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
                  <textarea id="c-brief" name="description" class="form__textarea" required placeholder="Tell us what you are building or scaling, what bottleneck you want to solve, and any key launch dates..."></textarea>
                </div>

                <div class="form__actions">
                  <p class="form__consent">
                    {{ sec('contact', 'form_consent', 'We sign mutual NDAs prior to reviewing private repositories or ad accounts. Zero spam, ever.') }}
                  </p>
                  <button type="submit" class="btn btn--primary btn--lg">
                    <span>{{ sec('contact', 'form_button', 'Submit Project Brief') }}</span>
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
              <h3 class="contact-promise__title">{{ sec('contact', 'promise_title', 'Direct founder review within 24 hours.') }}</h3>
              <ul class="contact-promise__list">
                @foreach(content_list('contact.promise') as $promise)
                  <li><strong>{{ $promise->title }}</strong> {{ $promise->body }}</li>
                @endforeach
              </ul>
              <p class="t-small"><a href="{{ url('/team') }}" class="text-link">Meet the full Empetix team &rarr;</a></p>
            </div>

            <div class="contact-block">
              <span class="contact-block__title">{{ sec('contact', 'hotline_title', 'Immediate Executive Hotline') }}</span>
              <a href="tel:{{ setting('phone', '+201055209612') }}" class="contact-block__big">{{ setting('phone', '+20 105 520 9612') }}</a>
              <p class="t-small">{{ sec('contact', 'hotline_hours', 'Sunday – Thursday, 09:00 – 18:00 Cairo / Riyadh Time.') }}</p>
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
            <h2 id="studios-heading" class="t-h2">{{ sec('contact', 'studios_heading', 'Visit our headquarters in New Cairo.') }}</h2>
          </div>
        </div>

        <article class="hq-showcase reveal">
          <div class="hq-showcase__media">
            <img src="{{ content_asset(sec('contact', 'copy_fcccaf2eb501')) }}" alt="Empetix Solutions Headquarters in New Cairo" width="1200" height="675"/>
          </div>
          <div class="hq-showcase__body">
            <div>
              <div class="office__top">
                <span class="office__role">EMPETIX SOLUTIONS HEADQUARTERS</span>
                <span class="office__clock"><span data-tz-clock="Africa/Cairo">12:00</span> CAIRO</span>
              </div>
              <h3 class="t-h2">{{ sec('contact', 'hq_city', 'Cairo, Egypt') }}</h3>
              <p class="t-small">{{ sec('contact', 'hq_desc', 'All software engineering, product design, cloud infrastructure, and AI architecture squads operate together under one roof at our New Cairo campus.') }}</p>
            </div>

            <div class="hq-showcase__list">
              <div class="hq-showcase__row">
                <span>Address</span>
                <span>{{ setting('address', 'Building 14, District 5 Campus, New Cairo, Cairo, Egypt') }}</span>
              </div>
              <div class="hq-showcase__row">
                <span>Working Hours</span>
                <span>{{ sec('contact', 'hq_hours', 'Sunday – Thursday · 09:00 – 18:00') }}</span>
              </div>
              <div class="hq-showcase__row">
                <span>Direct Telephone</span>
                <span><a href="tel:{{ setting('phone', '+201055209612') }}" class="text-link">{{ setting('phone', '+20 105 520 9612') }}</a></span>
              </div>
              <div class="hq-showcase__row">
                <span>New Business Desk</span>
                <span><a href="mailto:{{ setting('email', 'hello@empetix.com') }}" class="text-link">{{ setting('email', 'hello@empetix.com') }}</a></span>
              </div>
            </div>

            <a href="#main-content" class="btn btn--primary">
              <span>{{ sec('contact', 'visit_cta', 'Schedule an In-Person Studio Visit') }}</span>
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
            <h2 id="faq-heading" class="t-h2">{{ sec('contact', 'faq_heading', 'Common questions before we kick off.') }}</h2>
          </div>
        </div>

        <div class="accordion reveal">
          @foreach(content_list('contact.faq') as $faq)
            <details class="accordion__item"@if($loop->first) open @endif>
              <summary class="accordion__summary">
                <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}. {{ $faq->title }}</span>
                <span class="accordion__icon" aria-hidden="true"></span>
              </summary>
              <div class="accordion__body">
                <p>{{ $faq->body }}</p>
              </div>
            </details>
          @endforeach
        </div>
      </div>
    </section>
</main>
@endsection
