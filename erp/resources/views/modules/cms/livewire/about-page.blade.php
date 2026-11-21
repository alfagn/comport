<div>
    <!-- Page Hero -->
    <section class="page-hero">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ route('home') }}">Home</a></li>
          <li aria-current="page">About &amp; Founders</li>
        </ol>
        <h1 class="page-hero__title">Built by engineers and storytellers who got tired of the hand-off gap.</h1>
        <div class="page-hero__row">
          <div class="page-hero__label">
            <span class="eyebrow">Established 2018 &middot; Cairo</span>
          </div>
          <p class="page-hero__lead">
            Empetix Solution was founded in New Cairo by our co-founders uniting executive strategy, operational excellence, and software engineering under one roof—helping ambitious businesses build technology, scale growth, and grow sustainably.
          </p>
          <div class="page-hero__aside">
            <span class="t-meta">48 SPECIALISTS &middot; 3 STUDIOS</span>
          </div>
        </div>
      </div>
    </section>

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

    <!-- Founders Section (أصحاب ومؤسسو الشركة) -->
    <section id="founders" class="founders founders--surface" aria-labelledby="founders-heading">
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
          @foreach($founders as $founder)
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
          @endforeach
        </ul>
      </div>
    </section>

    <!-- Operating Principles -->
    <section class="section is-inverse" aria-labelledby="principles-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="principles-heading" class="t-h2">How we work when the stakes are high.</h2>
          </div>
        </div>

        <div class="principles">
          <div class="principle reveal">
            <span class="principle__num">01</span>
            <h3 class="principle__title">Code and campaigns share one scoreboard.</h3>
            <p class="principle__text">
              We don&rsquo;t measure engineering in story points and marketing in impressions. Both teams are accountable for activation, retention, and net revenue.
            </p>
          </div>
          <div class="principle reveal">
            <span class="principle__num">02</span>
            <h3 class="principle__title">Senior principals on the tools.</h3>
            <p class="principle__text">
              We don&rsquo;t bait-and-switch with senior pitch decks and junior execution. Our founders and staff engineers write production code and direct campaigns daily.
            </p>
          </div>
          <div class="principle reveal">
            <span class="principle__num">03</span>
            <h3 class="principle__title">Arabic and English as equal first-class citizens.</h3>
            <p class="principle__text">
              We engineer bidirectional RTL/LTR interfaces and bilingual brand systems natively—never as a rushed translation layer at the end.
            </p>
          </div>
          <div class="principle reveal">
            <span class="principle__num">04</span>
            <h3 class="principle__title">Full IP ownership and clean handovers.</h3>
            <p class="principle__text">
              Every line of Laravel/React code, Figma token, ad account, and raw camera file belongs to our client, documented so your internal team can run with it.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Global Offices & Presence -->
    <section class="section section--alt section--ruled" aria-labelledby="offices-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="offices-heading" class="t-h2">Headquarters &amp; Regional Studio Network</h2>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-top: 2rem;">
          <div style="background: #ffffff; border: 1px solid #DCDCD4; border-radius: 8px; padding: 2rem;">
            <span class="eyebrow" style="color: #FF4D1C;">Headquarters</span>
            <h3 class="t-h3" style="margin: 0.5rem 0 1rem;">Cairo, Egypt</h3>
            <p style="color: #62718A; line-height: 1.6; font-size: 0.95rem;">{{ $cairoHq }}</p>
            <span style="display: block; margin-top: 1rem; font-size: 0.85rem; font-weight: 700; color: #08142C;">Full Studio &amp; Film Stage</span>
          </div>

          <div style="background: #ffffff; border: 1px solid #DCDCD4; border-radius: 8px; padding: 2rem;">
            <span class="eyebrow" style="color: #FF4D1C;">GCC Hub</span>
            <h3 class="t-h3" style="margin: 0.5rem 0 1rem;">Riyadh, KSA</h3>
            <p style="color: #62718A; line-height: 1.6; font-size: 0.95rem;">{{ $riyadhOffice }}</p>
            <span style="display: block; margin-top: 1rem; font-size: 0.85rem; font-weight: 700; color: #08142C;">Engineering &amp; Growth Retainers</span>
          </div>

          <div style="background: #ffffff; border: 1px solid #DCDCD4; border-radius: 8px; padding: 2rem;">
            <span class="eyebrow" style="color: #FF4D1C;">European Desk</span>
            <h3 class="t-h3" style="margin: 0.5rem 0 1rem;">London, UK</h3>
            <p style="color: #62718A; line-height: 1.6; font-size: 0.95rem;">{{ $londonOffice }}</p>
            <span style="display: block; margin-top: 1rem; font-size: 0.85rem; font-weight: 700; color: #08142C;">Enterprise Client Relations</span>
          </div>
        </div>
      </div>
    </section>
</div>
