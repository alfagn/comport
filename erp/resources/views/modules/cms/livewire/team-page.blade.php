<div class="team-page">
    <!-- Breadcrumb & Editorial Hero -->
    <section class="team-band team-band--white team-hero-band" aria-labelledby="team-hero-heading">
      <div class="team-container">
        <nav class="team-crumb" aria-label="Breadcrumb">
          <ol class="team-crumb__list">
            <li class="team-crumb__item"><a href="{{ route('home') }}" class="team-crumb__link">Home</a></li>
            <li class="team-crumb__item" aria-current="page">Team</li>
          </ol>
        </nav>

        <div class="team-hero">
          <h1 id="team-hero-heading" class="team-hero__title">The people behind Empetix</h1>
          <p class="team-hero__lead">
            Engineers, product designers, and performance marketers working as one multidisciplinary team from New Cairo—without agency layers or external hand-offs.
          </p>
        </div>

        <div class="team-hero__media">
          <img
            class="team-hero__photo"
            src="{{ asset('assets/img/about/office.jpg') }}"
            alt="Empetix studio"
            width="1600"
            height="900"
          />
        </div>
      </div>
    </section>

    <!-- Founders Grid (المؤسسون وأصحاب الشركه) -->
    <section class="team-band team-band--founders" aria-labelledby="team-founders-heading">
      <div class="team-container">
        <header class="team-sec-head">
          <p class="team-sec-head__eyebrow">Executive Leadership</p>
          <h2 id="team-founders-heading" class="team-sec-head__title">The Founders</h2>
          <p class="team-sec-head__lead">
            Our co-founders lead every client partnership directly across commercial strategy, operational delivery, and software engineering.
          </p>
        </header>

        <div class="team-founders-grid">
          @foreach($founders as $founder)
            <article class="team-founder-card">
              <a href="{{ route('site.founder', $founder->slug) }}" class="team-founder-card__link">
                <div class="team-founder-card__media">
                  <img
                    class="team-founder-card__photo"
                    src="{{ asset($founder->avatar ?: 'assets/img/team/' . $founder->slug . '.jpg') }}"
                    alt="Portrait of {{ $founder->name }}"
                    width="800"
                    height="1000"
                    loading="lazy"
                  />
                </div>
                <div class="team-founder-card__body">
                  <h3 class="team-founder-card__name">{{ $founder->name }}</h3>
                  <p class="team-founder-card__role">{{ $founder->role_title }}</p>
                  <p class="team-founder-card__bio" style="font-size: 0.9rem; color: #62718A; margin-top: 0.5rem;">
                    {{ $founder->bio }}
                  </p>
                  <span class="team-founder-card__cta" style="display: inline-block; margin-top: 0.75rem; color: #FF4D1C; font-weight: 700; font-size: 0.85rem;">
                    View Executive Profile &rarr;
                  </span>
                </div>
              </a>
            </article>
          @endforeach
        </div>
      </div>
    </section>

    <!-- Core Team & Engineering Leads -->
    <section class="team-band team-band--grid" aria-labelledby="team-leads-heading">
      <div class="team-container">
        <header class="team-sec-head">
          <p class="team-sec-head__eyebrow">Practice Leads &amp; Principals</p>
          <h2 id="team-leads-heading" class="team-sec-head__title">Engineering &amp; Growth Directory</h2>
        </header>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
          @forelse($teamMembers as $member)
            <div style="background: #ffffff; border: 1px solid #DCDCD4; border-radius: 8px; padding: 1.5rem;">
              <h4 style="font-size: 1.1rem; font-weight: 700; color: #08142C; margin-bottom: 0.25rem;">{{ $member->name }}</h4>
              <p style="color: #FF4D1C; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">{{ $member->role_title }}</p>
              <p style="color: #62718A; font-size: 0.85rem; line-height: 1.5;">{{ $member->bio }}</p>
            </div>
          @empty
            <div style="background: #ffffff; border: 1px solid #DCDCD4; border-radius: 8px; padding: 1.5rem;">
              <h4 style="font-size: 1.1rem; font-weight: 700; color: #08142C;">Youssef Nabil</h4>
              <p style="color: #FF4D1C; font-size: 0.85rem; font-weight: 600;">Principal Laravel Architect</p>
            </div>
            <div style="background: #ffffff; border: 1px solid #DCDCD4; border-radius: 8px; padding: 1.5rem;">
              <h4 style="font-size: 1.1rem; font-weight: 700; color: #08142C;">Nourhan Shawky</h4>
              <p style="color: #FF4D1C; font-size: 0.85rem; font-weight: 600;">Head of Product Design Systems</p>
            </div>
            <div style="background: #ffffff; border: 1px solid #DCDCD4; border-radius: 8px; padding: 1.5rem;">
              <h4 style="font-size: 1.1rem; font-weight: 700; color: #08142C;">Hassan El-Kholy</h4>
              <p style="color: #FF4D1C; font-size: 0.85rem; font-weight: 600;">Staff Mobile Engineer (Flutter)</p>
            </div>
            <div style="background: #ffffff; border: 1px solid #DCDCD4; border-radius: 8px; padding: 1.5rem;">
              <h4 style="font-size: 1.1rem; font-weight: 700; color: #08142C;">Mariam Farouk</h4>
              <p style="color: #FF4D1C; font-size: 0.85rem; font-weight: 600;">Director of Paid Acquisition</p>
            </div>
          @endforelse
        </div>
      </div>
    </section>
</div>
