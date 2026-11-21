<div class="founder-page">
    <!-- Breadcrumb & Hero -->
    <section class="founder-band founder-band--white founder-hero-band" aria-labelledby="founder-name">
      <div class="founder-container">
        <nav class="founder-crumb" aria-label="Breadcrumb">
          <ol class="founder-crumb__list">
            <li class="founder-crumb__item"><a href="{{ route('home') }}" class="founder-crumb__link">Home</a></li>
            <li class="founder-crumb__item"><a href="{{ route('site.team') }}" class="founder-crumb__link">Team</a></li>
            <li class="founder-crumb__item" aria-current="page">{{ $founder->name }}</li>
          </ol>
        </nav>

        <div class="founder-hero">
          <div class="founder-hero__media">
            <img
              class="founder-hero__photo"
              src="{{ asset($founder->avatar ?: 'assets/img/team/' . $founder->slug . '.jpg') }}"
              alt="Portrait of {{ $founder->name }}, {{ $founder->role_title }}"
              width="800"
              height="1000"
            />
          </div>

          <div class="founder-hero__body">
            <h1 id="founder-name" class="founder-hero__name">{{ $founder->name }}</h1>
            <p class="founder-hero__title">{{ $founder->role_title }}</p>
            <p class="founder-hero__lead">
              {{ $founder->bio }}
            </p>

            @if(!empty($founder->social_links))
              <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                @foreach($founder->social_links as $network => $url)
                  <a href="{{ $url }}" target="_blank" rel="noopener" class="btn btn--ghost btn--sm">
                    {{ ucfirst($network) }} &rarr;
                  </a>
                @endforeach
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>

    <!-- Leadership Philosophy & Track Record -->
    <section class="founder-band founder-band--surface" aria-labelledby="about-heading">
      <div class="founder-container">
        <div class="founder-about">
          <h2 id="about-heading" class="founder-heading">Executive Biography &amp; Practice Focus</h2>
          <div class="founder-about__body" style="font-size: 1.1rem; line-height: 1.8; color: #1C2E52;">
            <p>
              As a core co-founder of Empetix Solution, {{ $founder->name }} leads client initiatives with an unyielding commitment to precision, production reliability, and tangible business acceleration.
            </p>
            <p style="margin-top: 1rem;">
              With years of enterprise experience across high-growth MENA startups, GCC financial technology groups, and European commerce brands, {{ $founder->name }} bridges technical complexity and executive clarity.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Co-Founders Navigation -->
    @if($coFounders->isNotEmpty())
      <section class="founder-band founder-band--white" aria-labelledby="cofounders-heading">
        <div class="founder-container">
          <header class="team-sec-head">
            <p class="team-sec-head__eyebrow">Executive Partners</p>
            <h2 id="cofounders-heading" class="team-sec-head__title">Meet the Other Co-Founders</h2>
          </header>

          <div class="team-founders-grid" style="margin-top: 2rem;">
            @foreach($coFounders as $co)
              <article class="team-founder-card">
                <a href="{{ route('site.founder', $co->slug) }}" class="team-founder-card__link">
                  <div class="team-founder-card__media">
                    <img
                      class="team-founder-card__photo"
                      src="{{ asset($co->avatar ?: 'assets/img/team/' . $co->slug . '.jpg') }}"
                      alt="Portrait of {{ $co->name }}"
                      width="800"
                      height="1000"
                      loading="lazy"
                    />
                  </div>
                  <div class="team-founder-card__body">
                    <h3 class="team-founder-card__name">{{ $co->name }}</h3>
                    <p class="team-founder-card__role">{{ $co->role_title }}</p>
                    <span style="display: inline-block; margin-top: 0.5rem; color: #FF4D1C; font-weight: 700; font-size: 0.85rem;">
                      View Profile &rarr;
                    </span>
                  </div>
                </a>
              </article>
            @endforeach
          </div>
        </div>
      </section>
    @endif
</div>
