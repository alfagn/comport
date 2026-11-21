<div>
    <!-- 1. HERO SLIDER -->
    <section class="hero-slider" data-hero-slider aria-label="Featured studio highlights">
      <div class="hero-slider__viewport">

        <!-- SLIDE 01: Digital Product Engineering & Growth -->
        <article class="hero-slide is-active" data-hero-slide aria-hidden="false">
          <div class="hero-slide__bg">
            <img src="{{ asset('assets/img/home/hero.jpg') }}" alt="Empetix studio team" width="1920" height="1080"/>
          </div>
          <div class="container">
            <div class="hero-slide__content">
              <div class="hero-slide__meta">
                <span class="hero-slide__tag">SOFTWARE &amp; GROWTH PARTNER</span>
                <span class="hero-slide__kicker">NEW CAIRO, EGYPT</span>
              </div>
              <h1 class="hero-slide__title">
                {{ $heroHeadline }}
              </h1>
              <p class="hero-slide__desc">
                {{ $heroSubheadline }}
              </p>
              <div class="hero-slide__actions">
                <a href="{{ route('site.contact') }}" class="btn btn--accent btn--lg">
                  <span>Start a Project</span>
                  <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                  </svg>
                </a>
                <a href="#who-we-are" class="btn btn--ghost btn--lg">
                  <span>Watch Company Overview</span>
                </a>
              </div>
            </div>
          </div>
        </article>

        <!-- SLIDE 02: Performance Marketing, CRO & Commercial Production -->
        <article class="hero-slide" data-hero-slide aria-hidden="true">
          <div class="hero-slide__bg">
            <img src="{{ asset('assets/img/blog/performance-playbook.jpg') }}" alt="Performance marketing telemetry" width="1920" height="1080"/>
          </div>
          <div class="container">
            <div class="hero-slide__content">
              <div class="hero-slide__meta">
                <span class="hero-slide__tag">PERFORMANCE MARKETING &amp; CRO</span>
                <span class="hero-slide__kicker">3.8X AUDITED MEDIAN ROAS</span>
              </div>
              <h2 class="hero-slide__title">
                Paid media, video production, and conversion work for growing brands.
              </h2>
              <p class="hero-slide__desc">
                We replace guesswork with server-side Conversions API (CAPI) engineering, in-house commercial film shoots, and bilingual conversion optimization across MENA and Europe.
              </p>
              <div class="hero-slide__actions">
                <a href="{{ route('site.work') }}" class="btn btn--accent btn--lg">
                  <span>View Growth Case Studies</span>
                  <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                  </svg>
                </a>
                <a href="{{ route('site.blog') }}" class="btn btn--ghost btn--lg">
                  <span>Inside Our Production Lab</span>
                </a>
              </div>
            </div>
          </div>
        </article>

        <!-- SLIDE 03: Flagship Case Study — NilePay FinTech -->
        <article class="hero-slide" data-hero-slide aria-hidden="true">
          <div class="hero-slide__bg">
            <img src="{{ asset('assets/img/work/nilepay.jpg') }}" alt="Customer tapping smartphone to pay" width="1920" height="1080"/>
          </div>
          <div class="container">
            <div class="hero-slide__content">
              <div class="hero-slide__meta">
                <span class="hero-slide__tag">SUCCESS STORY: NILEPAY</span>
                <span class="hero-slide__kicker">FINTECH &middot; 1.4M ACTIVE WALLETS</span>
              </div>
              <h2 class="hero-slide__title">
                How NilePay reached 1.4 million active mobile wallet users.
              </h2>
              <p class="hero-slide__desc">
                Pairing a 60fps Flutter mobile wallet and event-sourced Laravel 11 settlement core with a nationwide paid acquisition engine that lowered CAC by 41%.
              </p>
              <div class="hero-slide__actions">
                <a href="{{ route('site.work.detail', 'nilepay') }}" class="btn btn--accent btn--lg">
                  <span>Read Full Case Dossier</span>
                  <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                  </svg>
                </a>
                <a href="{{ route('site.work') }}" class="btn btn--ghost btn--lg">
                  <span>All Projects</span>
                </a>
              </div>
            </div>
          </div>
        </article>

        <!-- SLIDE 04: Video Episode — 5-Day Product & Growth Sprint -->
        <article class="hero-slide" data-hero-slide aria-hidden="true">
          <div class="hero-slide__bg">
            <img src="{{ asset('assets/img/blog/design-sprint.jpg') }}" alt="Empetix team sprint" width="1920" height="1080"/>
          </div>
          <div class="container">
            <div class="hero-slide__content">
              <div class="hero-slide__meta">
                <span class="hero-slide__tag">STUDIO VIDEO DOCUMENTARY</span>
                <span class="hero-slide__kicker">RUNTIME 14:20 &middot; FULL EPISODE</span>
              </div>
              <h2 class="hero-slide__title">
                From product problem to tested prototype in five days.
              </h2>
              <p class="hero-slide__desc">
                Watch how our software architects, product designers, and media buyers take a product from commercial bottleneck to user-tested prototype in one week.
              </p>
              <div class="hero-slide__actions">
                <a href="{{ route('site.blog') }}" class="btn btn--accent btn--lg">
                  <span>Watch Full Video Episode</span>
                  <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
                  </svg>
                </a>
                <a href="{{ route('site.blog') }}" class="btn btn--ghost btn--lg">
                  <span>Explore All Blogs</span>
                </a>
              </div>
            </div>
          </div>
        </article>

      </div>

      <!-- Hero Slider Navigation Tabs -->
      <div class="hero-slider__dock">
        <div class="container">
          <div class="hero-slider__tabs" role="tablist" aria-label="Hero slides">
            <button type="button" class="hero-tab" role="tab" data-hero-tab="0" aria-selected="true">
              <span class="hero-tab__progress"><span class="hero-tab__progress-bar"></span></span>
              <span class="hero-tab__num">OVERVIEW</span>
              <span class="hero-tab__title">Software &amp; Growth Studio</span>
              <span class="hero-tab__sub">End-to-end digital transformation</span>
            </button>

            <button type="button" class="hero-tab" role="tab" data-hero-tab="1" aria-selected="false">
              <span class="hero-tab__progress"><span class="hero-tab__progress-bar"></span></span>
              <span class="hero-tab__num">MARKETING</span>
              <span class="hero-tab__title">Performance &amp; CRO Engine</span>
              <span class="hero-tab__sub">3.8x median ROAS &amp; server-side CAPI</span>
            </button>

            <button type="button" class="hero-tab" role="tab" data-hero-tab="2" aria-selected="false">
              <span class="hero-tab__progress"><span class="hero-tab__progress-bar"></span></span>
              <span class="hero-tab__num">CASE STUDY</span>
              <span class="hero-tab__title">NilePay FinTech Platform</span>
              <span class="hero-tab__sub">1.4M wallets &middot; +310% activation</span>
            </button>

            <button type="button" class="hero-tab" role="tab" data-hero-tab="3" aria-selected="false">
              <span class="hero-tab__progress"><span class="hero-tab__progress-bar"></span></span>
              <span class="hero-tab__num">VIDEO EPISODE</span>
              <span class="hero-tab__title">Inside a 5-Day Sprint</span>
              <span class="hero-tab__sub">Watch 14:20 studio documentary</span>
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Continuous Capabilities Ticker -->
    <div class="ticker" aria-label="Core studio capabilities">
      <div class="ticker__track">
        <span class="ticker__item"><span class="ticker__dot"></span>{{ $tickerText }}</span>
        <span class="ticker__item"><span class="ticker__dot"></span>LARAVEL &amp; MULTI-TENANT ARCHITECTURE</span>
        <span class="ticker__item"><span class="ticker__dot"></span>FLUTTER 60FPS MOBILE ENGINEERING</span>
        <span class="ticker__item"><span class="ticker__dot"></span>SERVER-SIDE CONVERSIONS API (CAPI)</span>
        <span class="ticker__item"><span class="ticker__dot"></span>FINTECH PAYMENT CORE &amp; HIGH-CONCURRENCY LEDGERS</span>
        <span class="ticker__item" aria-hidden="true"><span class="ticker__dot"></span>{{ $tickerText }}</span>
        <span class="ticker__item" aria-hidden="true"><span class="ticker__dot"></span>LARAVEL &amp; MULTI-TENANT ARCHITECTURE</span>
        <span class="ticker__item" aria-hidden="true"><span class="ticker__dot"></span>FLUTTER 60FPS MOBILE ENGINEERING</span>
        <span class="ticker__item" aria-hidden="true"><span class="ticker__dot"></span>SERVER-SIDE CONVERSIONS API (CAPI)</span>
      </div>
    </div>

    <!-- Company Overview Film -->
    <section id="who-we-are" class="overview-film" aria-labelledby="who-we-are-heading">
      <div class="container">
        <header class="overview-film__header">
          <h2 id="who-we-are-heading" class="overview-film__title">Who we are</h2>
          <p class="overview-film__lead">
            Empetix Solution brings software engineering, growth marketing, and strategic investment together under one roof in New Cairo. Watch our 40-second overview to see how we build and scale digital businesses.
          </p>
        </header>

        <div class="overview-film__stage">
          <video
            class="overview-film__video"
            controls
            playsinline
            preload="metadata"
            poster="{{ asset('assets/img/home/overview-poster.jpg') }}"
            width="1920"
            height="1080"
          >
            <source src="{{ asset('assets/video/empetix-overview.mp4') }}" type="video/mp4"/>
            Your browser does not support embedded video playback.
          </video>
        </div>

        <ul class="overview-film__pillars" aria-label="Empetix Solution core pillars">
          <li class="overview-film__pillar">
            <h3 class="overview-film__pillar-title">Software engineering</h3>
            <p class="overview-film__pillar-desc">
              Full-stack web and mobile platforms built for reliability, speed, and long-term scale.
            </p>
          </li>
          <li class="overview-film__pillar">
            <h3 class="overview-film__pillar-title">Growth marketing</h3>
            <p class="overview-film__pillar-desc">
              Engineers and marketers working as one unified team to turn clean data into customer acquisition.
            </p>
          </li>
          <li class="overview-film__pillar">
            <h3 class="overview-film__pillar-title">Strategic investment</h3>
            <p class="overview-film__pillar-desc">
              Backing bold founders with capital, senior technical leadership, and go-to-market execution.
            </p>
          </li>
        </ul>
      </div>
    </section>

    <!-- Capabilities & Solutions Hub -->
    <section id="solutions-hub" class="section section--alt" aria-labelledby="solutions-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="solutions-heading" class="t-h2">End-to-end engineering and growth capabilities.</h2>
            <p class="t-lead">
              Select a practice pillar below to inspect our architecture, deliverables, and verified client outcomes.
            </p>
          </div>
          <div class="section-head__action">
            <a href="{{ route('site.contact') }}" class="arrow-link">
              <span>Request Custom Scope</span>
              <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </a>
          </div>
        </div>

        <div class="solutions-hub reveal">
          <!-- Pillar Selector Tabs -->
          <div class="solutions-hub__nav" role="tablist" aria-label="Solution pillars">
            <button type="button" class="solution-tab" role="tab" data-solution-tab="sol-software" aria-selected="true">
              <span class="solution-tab__num">01</span>
              <span class="solution-tab__title">Custom Web, SaaS &amp; Cloud APIs</span>
              <span class="solution-tab__tag">Engineering</span>
            </button>

            <button type="button" class="solution-tab" role="tab" data-solution-tab="sol-mobile" aria-selected="false">
              <span class="solution-tab__num">02</span>
              <span class="solution-tab__title">Mobile Apps &amp; FinTech Ledgers</span>
              <span class="solution-tab__tag">Flutter / Core</span>
            </button>

            <button type="button" class="solution-tab" role="tab" data-solution-tab="sol-marketing" aria-selected="false">
              <span class="solution-tab__num">03</span>
              <span class="solution-tab__title">Performance Marketing &amp; Paid Media</span>
              <span class="solution-tab__tag">Growth</span>
            </button>

            <button type="button" class="solution-tab" role="tab" data-solution-tab="sol-production" aria-selected="false">
              <span class="solution-tab__num">04</span>
              <span class="solution-tab__title">Brand Systems, Film &amp; Photography</span>
              <span class="solution-tab__tag">Creative</span>
            </button>

            <button type="button" class="solution-tab" role="tab" data-solution-tab="sol-cro" aria-selected="false">
              <span class="solution-tab__num">05</span>
              <span class="solution-tab__title">Conversion Optimization (CRO) &amp; AI</span>
              <span class="solution-tab__tag">Data / AI</span>
            </button>
          </div>

          <!-- Interactive Stage -->
          <div class="solutions-hub__stage">
            <!-- Panel 1: Software & Cloud -->
            <div class="solution-panel is-active" data-solution-panel="sol-software">
              <div class="solution-panel__body">
                <div>
                  <span class="solution-panel__kicker">PILLAR SOFTWARE ARCHITECTURE</span>
                  <h3 class="solution-panel__title">Enterprise Web Platforms, Laravel APIs &amp; Cloud Modernization</h3>
                  <p class="solution-panel__desc">
                    We build modular web platforms, B2B portals, and multi-tenant SaaS products in Laravel 11, Next.js, and AWS—engineered for 99.96% uptime and clean ERP/CRM integration.
                  </p>
                  <ul class="solution-panel__deliverables">
                    <li>Domain-Driven Laravel &amp; Node.js Microservices</li>
                    <li>Next.js / TypeScript SSR &amp; Headless Storefronts</li>
                    <li>AWS ECS, RDS &amp; Zero-Downtime CI/CD Pipelines</li>
                    <li>Automated Pest / PHPUnit &amp; Security Audits</li>
                  </ul>
                </div>
                <a href="{{ route('site.work.detail', 'nilepay') }}" class="btn btn--primary">
                  <span>Explore Software Architecture</span>
                  <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
                </a>
              </div>
              <div class="solution-panel__media">
                <img src="{{ asset('assets/img/home/studio.jpg') }}" alt="Developers coding" width="800" height="600"/>
                <div class="solution-panel__metric-badge">
                  <span class="solution-panel__metric-num">{{ $statUptime }}</span>
                  <span class="solution-panel__metric-text">Audited production availability across deployed enterprise platforms</span>
                </div>
              </div>
            </div>

            <!-- Panel 2: Mobile & FinTech -->
            <div class="solution-panel" data-solution-panel="sol-mobile">
              <div class="solution-panel__body">
                <div>
                  <span class="solution-panel__kicker">PILLAR MOBILE &amp; FINTECH</span>
                  <h3 class="solution-panel__title">60fps Consumer Mobile Apps &amp; Event-Sourced Payment Ledgers</h3>
                  <p class="solution-panel__desc">
                    From biometric KYC and NFC merchant checkouts to offline-first fleet logistics tablets, our Flutter and native iOS/Android squads build apps used by 1.4M+ monthly active users.
                  </p>
                  <ul class="solution-panel__deliverables">
                    <li>Flutter &amp; Native Swift / Kotlin Mobile Clients</li>
                    <li>Double-Entry Payment Wallets &amp; Regional Gateways</li>
                    <li>90-Second Biometric &amp; OCR Onboarding Flows</li>
                    <li>Offline-First SQLite Sync for Field Operations</li>
                  </ul>
                </div>
                <a href="{{ route('site.work.detail', 'nilepay') }}" class="btn btn--primary">
                  <span>See NilePay Mobile Case Study</span>
                  <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
                </a>
              </div>
              <div class="solution-panel__media">
                <img src="{{ asset('assets/img/work/nilepay.jpg') }}" alt="Customer paying with phone" width="800" height="600"/>
                <div class="solution-panel__metric-badge">
                  <span class="solution-panel__metric-num">+310%</span>
                  <span class="solution-panel__metric-text">First-transaction wallet activation after onboarding rebuild</span>
                </div>
              </div>
            </div>

            <!-- Panel 3: Performance Marketing -->
            <div class="solution-panel" data-solution-panel="sol-marketing">
              <div class="solution-panel__body">
                <div>
                  <span class="solution-panel__kicker">PILLAR GROWTH &amp; ACQUISITION</span>
                  <h3 class="solution-panel__title">Full-Funnel Performance Media &amp; Server-Side Attribution</h3>
                  <p class="solution-panel__desc">
                    We wire server-side Conversions API (CAPI) events straight from your backend database into Meta, Google, TikTok, and LinkedIn—bidding on real net revenue instead of vanity clicks.
                  </p>
                  <ul class="solution-panel__deliverables">
                    <li>Meta, Google PMax, TikTok &amp; Snapchat Media Buying</li>
                    <li>First-Party Server-Side CAPI &amp; CRM Attribution</li>
                    <li>Weekly Creative Hook &amp; Angle Testing Matrices</li>
                    <li>Cohort LTV:CAC &amp; Payback Period Dashboards</li>
                  </ul>
                </div>
                <a href="{{ route('site.work') }}" class="btn btn--primary">
                  <span>Examine Growth Outcomes</span>
                  <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
                </a>
              </div>
              <div class="solution-panel__media">
                <img src="{{ asset('assets/img/blog/performance-playbook.jpg') }}" alt="Marketing dashboards" width="800" height="600"/>
                <div class="solution-panel__metric-badge">
                  <span class="solution-panel__metric-num">3.8x</span>
                  <span class="solution-panel__metric-text">Audited median Return on Ad Spend across active growth retainers</span>
                </div>
              </div>
            </div>

            <!-- Panel 4: Brand Production -->
            <div class="solution-panel" data-solution-panel="sol-production">
              <div class="solution-panel__body">
                <div>
                  <span class="solution-panel__kicker">PILLAR BRAND &amp; PRODUCTION</span>
                  <h3 class="solution-panel__title">Bilingual Brand Systems, Commercial Film &amp; 35mm Photography</h3>
                  <p class="solution-panel__desc">
                    Our in-house cinema and design unit crafts authoritative Arabic/English brand identities, product launch films, and high-converting paid-social video assets.
                  </p>
                  <ul class="solution-panel__deliverables">
                    <li>Arabic &amp; Latin Brand Identity &amp; Design Guidelines</li>
                    <li>In-House Commercial Film &amp; Documentary Crew</li>
                    <li>Product Tabletop &amp; Editorial Lifestyle Photography</li>
                    <li>9:16 Paid Social Video Hook Variations</li>
                  </ul>
                </div>
                <a href="{{ route('site.blog') }}" class="btn btn--primary">
                  <span>View Production Essays</span>
                  <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
                </a>
              </div>
              <div class="solution-panel__media">
                <img src="{{ asset('assets/img/blog/behind-the-lens.jpg') }}" alt="Commercial photography" width="800" height="600"/>
                <div class="solution-panel__metric-badge">
                  <span class="solution-panel__metric-num">+190%</span>
                  <span class="solution-panel__metric-text">Lift in recurring D2C subscription orders post-campaign launch</span>
                </div>
              </div>
            </div>

            <!-- Panel 5: CRO & AI -->
            <div class="solution-panel" data-solution-panel="sol-cro">
              <div class="solution-panel__body">
                <div>
                  <span class="solution-panel__kicker">PILLAR CRO &amp; AI AUTOMATION</span>
                  <h3 class="solution-panel__title">Arabic-First Conversion Optimization &amp; AI Workflow Agents</h3>
                  <p class="solution-panel__desc">
                    We eliminate checkout friction through native RTL/LTR design systems, multivariate A/B testing, and domain-trained AI assistants that automate customer triage and internal operations.
                  </p>
                  <ul class="solution-panel__deliverables">
                    <li>Bilingual Checkout &amp; Onboarding CRO Experiments</li>
                    <li>100% CSS Logical Property Design Systems</li>
                    <li>Domain-Trained RAG AI Copilots &amp; Support Triage</li>
                    <li>Executive BigQuery &amp; Looker Telemetry Pipelines</li>
                  </ul>
                </div>
                <a href="{{ route('site.blog') }}" class="btn btn--primary">
                  <span>Read CRO Analysis</span>
                  <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
                </a>
              </div>
              <div class="solution-panel__media">
                <img src="{{ asset('assets/img/blog/arabic-first.jpg') }}" alt="Arabic wireframing" width="800" height="600"/>
                <div class="solution-panel__metric-badge">
                  <span class="solution-panel__metric-num">+38.4%</span>
                  <span class="solution-panel__metric-text">Mobile checkout completion lift on Arabic-language sessions</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Selected Success Stories & Case Studies -->
    <section class="section" aria-labelledby="work-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="work-heading" class="t-h2">Platforms built to perform. Campaigns built to convert.</h2>
          </div>
          <div class="section-head__action">
            <a href="{{ route('site.work') }}" class="arrow-link">
              <span>All Case Studies ({{ $featuredProjects->count() }})</span>
              <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
            </a>
          </div>
        </div>

        <div class="work-grid">
          @forelse($featuredProjects as $project)
            <article class="project-card {{ $loop->iteration % 2 === 1 ? 'project-card--wide' : 'project-card--narrow' }} {{ $loop->iteration > 2 ? 'project-card--offset' : '' }} reveal">
              <a href="{{ route('site.work.detail', $project->slug) }}" class="project-card__link">
                <div class="frame project-card__frame frame--zoom">
                  <img src="{{ asset($project->featured_image ?: 'assets/img/work/nilepay.jpg') }}" alt="{{ $project->title }}" width="1200" height="750"/>
                </div>
                <div class="project-card__body">
                  <span class="project-card__client">{{ strtoupper($project->client_name ?: 'EMPETIX CLIENT') }} &middot; {{ strtoupper($project->category?->name ?: 'SOFTWARE & GROWTH') }}</span>
                  <span class="project-card__year">{{ $project->completion_date?->format('Y') ?? date('Y') }}</span>
                  <h3 class="project-card__title">{{ $project->title }}</h3>
                  <p class="t-body" style="font-size: 0.95rem; color: #62718A; margin-top: 0.4rem;">{{ $project->excerpt }}</p>
                </div>
              </a>
            </article>
          @empty
            <article class="project-card project-card--wide reveal">
              <a href="{{ route('site.work.detail', 'nilepay') }}" class="project-card__link">
                <div class="frame project-card__frame frame--zoom">
                  <img src="{{ asset('assets/img/work/nilepay.jpg') }}" alt="NilePay" width="1200" height="750"/>
                </div>
                <div class="project-card__body">
                  <span class="project-card__client">NILEPAY FINTECH &middot; SOFTWARE + GROWTH LAUNCH</span>
                  <span class="project-card__year">2026</span>
                  <h3 class="project-card__title">Rebuilding mobile merchant checkout and scaling active wallets to 1.4M users across Egypt &amp; KSA.</h3>
                </div>
              </a>
            </article>
          @endforelse
        </div>
      </div>
    </section>

    <!-- Measurable Impact Stats -->
    <section class="section curtain-window is-inverse" aria-labelledby="impact-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="impact-heading" class="t-h2">Measurable engineering velocity and commercial return.</h2>
          </div>
        </div>

        <div class="stats reveal">
          <div class="stat">
            <div class="stat__value">
              <span data-counter data-counter-target="{{ (int) filter_var($statProjects, FILTER_SANITIZE_NUMBER_INT) }}">{{ $statProjects }}</span>
            </div>
            <p class="stat__label">Software platforms &amp; growth engines shipped</p>
          </div>

          <div class="stat">
            <div class="stat__value">
              <span data-counter data-counter-target="3.8" data-counter-decimals="1">{{ $statRevenue }}</span>
            </div>
            <p class="stat__label">Audited median return on ad spend across active retainers</p>
          </div>

          <div class="stat">
            <div class="stat__value">
              <span data-counter data-counter-target="99.96" data-counter-decimals="2">{{ $statUptime }}</span>
            </div>
            <p class="stat__label">Production uptime across client SaaS and FinTech applications</p>
          </div>

          <div class="stat">
            <div class="stat__value">
              <span>{{ $statRating }}</span>
            </div>
            <p class="stat__label">Verified client satisfaction across multi-year engagements</p>
          </div>
        </div>
      </div>
    </section>

    <!-- The Founders Section (أصحاب ومؤسسو الشركة) -->
    <section id="founders" class="founders" aria-labelledby="founders-heading">
      <div class="founders__inner">
        <header class="founders__header">
          <h2 id="founders-heading" class="founders__title">The founders</h2>
          <div class="founders__intro">
            <p class="founders__lead">
              Every engagement at Empetix Solution is led directly by our co-founders across executive strategy, operations, and software engineering.
            </p>
            <a href="{{ route('site.team') }}" class="founders__story-link">Meet the full team</a>
          </div>
        </header>

        <ul class="founders__list">
          @forelse($founders as $founder)
            <li class="founders__item">
              <a href="{{ route('site.founder', $founder->slug) }}" class="founders__link">
                <div class="founders__media">
                  <img
                    class="founders__photo"
                    src="{{ asset($founder->avatar ?: 'assets/img/team/' . $founder->slug . '.jpg') }}"
                    alt="Portrait of {{ $founder->name }}"
                    width="800"
                    height="1000"
                    loading="lazy"
                  />
                </div>
                <h3 class="founders__name">{{ $founder->name }}</h3>
                <p class="founders__role">{{ $founder->role_title }}</p>
              </a>
            </li>
          @empty
            <li class="founders__item">
              <a href="{{ route('site.founder', 'ayman') }}" class="founders__link">
                <div class="founders__media"><img class="founders__photo" src="{{ asset('assets/img/team/ayman-ibrahim.jpg') }}" alt="Ayman Ibrahim" width="800" height="1000"/></div>
                <h3 class="founders__name">Ayman Ibrahim</h3>
                <p class="founders__role">Co-founder and chief executive officer</p>
              </a>
            </li>
            <li class="founders__item">
              <a href="{{ route('site.founder', 'bishoy') }}" class="founders__link">
                <div class="founders__media"><img class="founders__photo" src="{{ asset('assets/img/team/bishoy-magdy.jpg') }}" alt="Bishoy Magdy" width="800" height="1000"/></div>
                <h3 class="founders__name">Bishoy Magdy</h3>
                <p class="founders__role">Co-founder and chief operating officer</p>
              </a>
            </li>
            <li class="founders__item">
              <a href="{{ route('site.founder', 'mohanad') }}" class="founders__link">
                <div class="founders__media"><img class="founders__photo" src="{{ asset('assets/img/team/mohanad-shehata.jpg') }}" alt="Mohanad Shehata" width="800" height="1000"/></div>
                <h3 class="founders__name">Mohanad Ahmed Shehata</h3>
                <p class="founders__role">Co-founder and chief technology officer</p>
              </a>
            </li>
          @endforelse
        </ul>
      </div>
    </section>

    <!-- Strategic Corporate Partners -->
    <section class="section section--alt section--ruled" aria-labelledby="partners-home-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="partners-home-heading" class="t-h2">Trusted by category leaders and global technology alliances.</h2>
          </div>
          <div class="section-head__action">
            <a href="{{ route('site.partners') }}" class="arrow-link">
              <span>Partner Directory</span>
              <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
            </a>
          </div>
        </div>

        <div class="logo-wall reveal">
          @forelse($partners as $partner)
            <a href="{{ route('site.partners') }}" class="logo-wall__item">
              <span class="logo-wall__tier">ALLIANCE</span>
              <img src="{{ asset($partner->logo ?: 'assets/img/partners/aws-partner.svg') }}" alt="{{ $partner->name }}" width="220" height="56"/>
            </a>
          @empty
            <a href="{{ route('site.partners') }}" class="logo-wall__item">
              <span class="logo-wall__tier">FINTECH</span>
              <img src="{{ asset('assets/img/partners/nilepay.svg') }}" alt="NilePay" width="220" height="56"/>
            </a>
            <a href="{{ route('site.partners') }}" class="logo-wall__item">
              <span class="logo-wall__tier">CLOUD</span>
              <img src="{{ asset('assets/img/partners/aws-partner.svg') }}" alt="AWS" width="220" height="56"/>
            </a>
            <a href="{{ route('site.partners') }}" class="logo-wall__item">
              <span class="logo-wall__tier">COMMERCE</span>
              <img src="{{ asset('assets/img/partners/shopify-plus.svg') }}" alt="Shopify Plus" width="220" height="56"/>
            </a>
            <a href="{{ route('site.partners') }}" class="logo-wall__item">
              <span class="logo-wall__tier">GROWTH</span>
              <img src="{{ asset('assets/img/partners/meta-business.svg') }}" alt="Meta Business" width="220" height="56"/>
            </a>
          @endforelse
        </div>
      </div>
    </section>

    <!-- Latest Journal Posts -->
    <section class="section" aria-labelledby="blog-home-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="blog-home-heading" class="t-h2">Engineering essays, video episodes &amp; behind-the-scenes dispatches.</h2>
          </div>
          <div class="section-head__action">
            <a href="{{ route('site.blog') }}" class="arrow-link">
              <span>View All Posts</span>
              <svg class="arrow-link__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
            </a>
          </div>
        </div>

        <div class="post-grid">
          @forelse($latestPosts as $post)
            <article class="post-card reveal">
              <a href="{{ route('site.blog.detail', $post->slug) }}" class="post-card__link">
                <div class="frame post-card__frame frame--zoom">
                  <img src="{{ asset($post->featured_image ?: 'assets/img/blog/arabic-first.jpg') }}" alt="{{ $post->title }}" width="800" height="533"/>
                  <span class="post-card__badge">{{ strtoupper($post->type) }}</span>
                </div>
                <div class="post-card__meta">
                  <span class="post-card__cat">{{ strtoupper($post->category?->name ?: 'ENGINEERING') }}</span>
                  <time datetime="{{ $post->published_at?->format('Y-m-d') }}">{{ $post->published_at?->format('d M Y') ?? 'LATEST' }}</time>
                  <span>{{ $post->views_count }} VIEWS</span>
                </div>
                <h3 class="post-card__title">{{ $post->title }}</h3>
                <p class="post-card__excerpt">{{ $post->excerpt }}</p>
              </a>
            </article>
          @empty
            <article class="post-card reveal">
              <a href="{{ route('site.blog') }}" class="post-card__link">
                <div class="frame post-card__frame frame--zoom">
                  <img src="{{ asset('assets/img/blog/arabic-first.jpg') }}" alt="Arabic First" width="800" height="533"/>
                  <span class="post-card__badge">ARTICLE</span>
                </div>
                <div class="post-card__meta">
                  <span class="post-card__cat">DESIGN &amp; CRO</span>
                  <time>14 NOV 2026</time>
                  <span>8 MIN READ</span>
                </div>
                <h3 class="post-card__title">Engineering Arabic-First Design Systems &amp; High-Conversion Funnels</h3>
                <p class="post-card__excerpt">Why optical typography and localized checkout flows lifted conversion by 38%.</p>
              </a>
            </article>
          @endforelse
        </div>
      </div>
    </section>

    <!-- Careers Banner -->
    <section class="cta-band" aria-labelledby="careers-band-title">
      <div class="cta-band__media">
        <img src="{{ asset('assets/img/careers/culture.jpg') }}" alt="Empetix studio culture" width="1600" height="900"/>
      </div>
      <div class="container cta-band__inner reveal">
        <div class="cta-band__main">
          <span class="eyebrow eyebrow--inverse">07 / Careers Portal &middot; jobs.empetix.com</span>
          <h2 id="careers-band-title" class="cta-band__title">Do the sharpest work of your career without the agency theatre.</h2>
        </div>
        <div class="cta-band__side">
          <p>
            Explore open roles in Laravel, Next.js, Flutter, Product Design, and Performance Marketing on our dedicated talent portal.
          </p>
          <a href="{{ route('site.careers') }}" class="btn btn--accent btn--lg">
            <span>Explore Careers (Open Roles)</span>
            <svg class="btn__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5"/></svg>
          </a>
        </div>
      </div>
    </section>
</div>