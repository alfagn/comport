@extends('layouts.public', ['activePage' => 'team'])

@section('meta')
  <meta name="description" content="Meet the leadership, engineering, product design, and marketing teams at Empetix Solution in New Cairo."/>
  <title>Team — Empetix Solution</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="Team — Empetix Solution"/>
  <meta property="og:description" content="Meet the leadership, engineering, product design, and marketing teams at Empetix Solution in New Cairo."/>
  <meta property="og:url" content="https://empetix.vercel.app/team.html"/>
  <meta property="og:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:secure_url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution Logo"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="Team — Empetix Solution"/>
  <meta name="twitter:description" content="Meet the leadership, engineering, product design, and marketing teams at Empetix Solution in New Cairo."/>
  <meta name="twitter:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
@endsection

@section('content')
<!-- 1. Breadcrumb & Editorial Hero -->
    <section class="team-band team-band--white team-hero-band" aria-labelledby="team-hero-heading">
      <div class="team-container">
        <nav class="team-crumb" aria-label="Breadcrumb">
          <ol class="team-crumb__list">
            <li class="team-crumb__item"><a href="{{ url('/') }}" class="team-crumb__link">Home</a></li>
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
            alt="Empetix engineers and product designers reviewing layouts together in our New Cairo studio"
            width="1600"
            height="900"
          />
        </div>
      </div>
    </section>

    <!-- 2. Founders & Leadership -->
    <section id="founders" class="team-band team-band--surface" aria-labelledby="founders-heading">
      <div class="team-container">
        <div class="team-section-head">
          <h2 id="founders-heading" class="team-heading">Founders</h2>
          <div class="team-section-head__body">
            <p class="team-section-head__lead">
              Three co-founders who stay directly involved across executive strategy, operations, and software engineering.
            </p>
          </div>
        </div>

        <ul class="team-founders">
          <!-- LOOP: founder -->
          <li class="team-founders__item">
            <a href="{{ url('/founders/ayman') }}" class="team-founders__link">
              <div class="team-founders__media">
                <img
                  class="team-founders__photo"
                  src="{{ asset('assets/img/team/ayman-ibrahim.jpg') }}"
                  alt="Portrait of Ayman Ibrahim, Co-founder and chief executive officer"
                  width="800"
                  height="1000"
                  loading="lazy"
                />
              </div>
              <h3 class="team-founders__name">Ayman Ibrahim</h3>
              <p class="team-founders__role">Co-founder and chief executive officer</p>
            </a>
          </li>
          <!-- END LOOP: founder -->

          <li class="team-founders__item">
            <a href="{{ url('/founders/bishoy') }}" class="team-founders__link">
              <div class="team-founders__media">
                <img
                  class="team-founders__photo"
                  src="{{ asset('assets/img/team/bishoy-magdy.jpg') }}"
                  alt="Portrait of Bishoy Magdy, Co-founder and chief operating officer"
                  width="800"
                  height="1000"
                  loading="lazy"
                />
              </div>
              <h3 class="team-founders__name">Bishoy Magdy</h3>
              <p class="team-founders__role">Co-founder and chief operating officer</p>
            </a>
          </li>

          <li class="team-founders__item">
            <a href="{{ url('/founders/mohanad') }}" class="team-founders__link">
              <div class="team-founders__media">
                <img
                  class="team-founders__photo"
                  src="{{ asset('assets/img/team/mohanad-shehata.jpg') }}"
                  alt="Portrait of Mohanad Ahmed Shehata, Co-founder and chief technology officer"
                  width="800"
                  height="1000"
                  loading="lazy"
                />
              </div>
              <h3 class="team-founders__name">Mohanad Ahmed Shehata</h3>
              <p class="team-founders__role">Co-founder and chief technology officer</p>
            </a>
          </li>
        </ul>
      </div>
    </section>

    <!-- 3. Practice Teams (Employee Portrait Cards + Department Filter) -->
    <section id="directory" class="team-band team-band--white" aria-labelledby="directory-heading">
      <div class="team-container">
        <div class="team-section-head">
          <h2 id="directory-heading" class="team-heading">Practice teams</h2>
          <div class="team-section-head__body">
            <p class="team-section-head__lead">
              Senior specialists across software engineering, product design, and performance marketing working together in dedicated client squads.
            </p>
            <div class="team-filters" role="group" aria-label="Filter team by department">
              <button type="button" class="team-filter" data-team-filter="all" aria-pressed="true">All teams (12)</button>
              <button type="button" class="team-filter" data-team-filter="engineering" aria-pressed="false">Software engineering (4)</button>
              <button type="button" class="team-filter" data-team-filter="design" aria-pressed="false">Product design (4)</button>
              <button type="button" class="team-filter" data-team-filter="marketing" aria-pressed="false">Marketing and production (4)</button>
            </div>
          </div>
        </div>

        <!-- Practice 1: Software Engineering -->
        <div id="practice-engineering" class="team-practice" data-team-group="engineering">
          <div class="team-practice__head">
            <h3 class="team-practice__title">Software engineering</h3>
            <p class="team-practice__desc">
              Backend, frontend, mobile, and infrastructure engineers responsible for scalable APIs, payment ledgers, and reliable cloud deployments.
            </p>
          </div>

          <ul class="team-cards">
            <!-- LOOP: team-member -->
            <li class="team-cards__item">
              <article class="team-card">
                <div class="team-card__media">
                  <img
                    class="team-card__photo"
                    src="{{ asset('assets/img/team/tarek-elshenawy.jpg') }}"
                    alt="Portrait of Tarek El-Shenawy, Principal backend engineer"
                    width="640"
                    height="800"
                    loading="lazy"
                  />
                </div>
                <div class="team-card__body">
                  <div class="team-card__identity">
                    <h4 class="team-card__name">Tarek El-Shenawy</h4>
                    <p class="team-card__role">Principal backend engineer</p>
                  </div>
                  <p class="team-card__focus">Laravel, PostgreSQL, payment ledgers</p>
                </div>
              </article>
            </li>
            <!-- END LOOP: team-member -->

            <li class="team-cards__item">
              <article class="team-card">
                <div class="team-card__media">
                  <img
                    class="team-card__photo"
                    src="{{ asset('assets/img/team/nourhan-ibrahim.jpg') }}"
                    alt="Portrait of Nourhan Ibrahim, Staff frontend engineer"
                    width="640"
                    height="800"
                    loading="lazy"
                  />
                </div>
                <div class="team-card__body">
                  <div class="team-card__identity">
                    <h4 class="team-card__name">Nourhan Ibrahim</h4>
                    <p class="team-card__role">Staff frontend engineer</p>
                  </div>
                  <p class="team-card__focus">Next.js, TypeScript, accessibility</p>
                </div>
              </article>
            </li>

            <li class="team-cards__item">
              <article class="team-card">
                <div class="team-card__media">
                  <img
                    class="team-card__photo"
                    src="{{ asset('assets/img/team/youssef-abdelrahman.jpg') }}"
                    alt="Portrait of Youssef Abdelrahman, Lead mobile engineer"
                    width="640"
                    height="800"
                    loading="lazy"
                  />
                </div>
                <div class="team-card__body">
                  <div class="team-card__identity">
                    <h4 class="team-card__name">Youssef Abdelrahman</h4>
                    <p class="team-card__role">Lead mobile engineer</p>
                  </div>
                  <p class="team-card__focus">Flutter, iOS and Android performance</p>
                </div>
              </article>
            </li>

            <li class="team-cards__item">
              <article class="team-card">
                <div class="team-card__media">
                  <img
                    class="team-card__photo"
                    src="{{ asset('assets/img/team/mostafa-fawzy.jpg') }}"
                    alt="Portrait of Mostafa Fawzy, Cloud and DevOps lead"
                    width="640"
                    height="800"
                    loading="lazy"
                  />
                </div>
                <div class="team-card__body">
                  <div class="team-card__identity">
                    <h4 class="team-card__name">Mostafa Fawzy</h4>
                    <p class="team-card__role">Cloud and DevOps lead</p>
                  </div>
                  <p class="team-card__focus">AWS infrastructure, CI/CD, observability</p>
                </div>
              </article>
            </li>
          </ul>
        </div>

        <!-- Practice 2: Product Design -->
        <div id="practice-design" class="team-practice" data-team-group="design">
          <div class="team-practice__head">
            <h3 class="team-practice__title">Product design</h3>
            <p class="team-practice__desc">
              Designers and researchers crafting bilingual Arabic and English interfaces, tokenized component systems, and brand identities.
            </p>
          </div>

          <ul class="team-cards">
            <li class="team-cards__item">
              <article class="team-card">
                <div class="team-card__media">
                  <img
                    class="team-card__photo"
                    src="{{ asset('assets/img/team/mariam-nabil.jpg') }}"
                    alt="Portrait of Mariam Nabil, Lead product designer"
                    width="640"
                    height="800"
                    loading="lazy"
                  />
                </div>
                <div class="team-card__body">
                  <div class="team-card__identity">
                    <h4 class="team-card__name">Mariam Nabil</h4>
                    <p class="team-card__role">Lead product designer</p>
                  </div>
                  <p class="team-card__focus">Bilingual RTL and LTR design systems</p>
                </div>
              </article>
            </li>

            <li class="team-cards__item">
              <article class="team-card">
                <div class="team-card__media">
                  <img
                    class="team-card__photo"
                    src="{{ asset('assets/img/team/hossam-reda.jpg') }}"
                    alt="Portrait of Hossam Reda, Senior UX researcher"
                    width="640"
                    height="800"
                    loading="lazy"
                  />
                </div>
                <div class="team-card__body">
                  <div class="team-card__identity">
                    <h4 class="team-card__name">Hossam Reda</h4>
                    <p class="team-card__role">Senior UX researcher</p>
                  </div>
                  <p class="team-card__focus">Usability testing, onboarding flows</p>
                </div>
              </article>
            </li>

            <li class="team-cards__item">
              <article class="team-card">
                <div class="team-card__media">
                  <img
                    class="team-card__photo"
                    src="{{ asset('assets/img/team/farida-zaki.jpg') }}"
                    alt="Portrait of Farida Zaki, Brand and visual designer"
                    width="640"
                    height="800"
                    loading="lazy"
                  />
                </div>
                <div class="team-card__body">
                  <div class="team-card__identity">
                    <h4 class="team-card__name">Farida Zaki</h4>
                    <p class="team-card__role">Brand and visual designer</p>
                  </div>
                  <p class="team-card__focus">Identity systems, editorial typography</p>
                </div>
              </article>
            </li>

            <li class="team-cards__item">
              <article class="team-card">
                <div class="team-card__media">
                  <img
                    class="team-card__photo"
                    src="{{ asset('assets/img/team/ziad-elbanna.jpg') }}"
                    alt="Portrait of Ziad El-Banna, Senior UI and RTL systems designer"
                    width="640"
                    height="800"
                    loading="lazy"
                  />
                </div>
                <div class="team-card__body">
                  <div class="team-card__identity">
                    <h4 class="team-card__name">Ziad El-Banna</h4>
                    <p class="team-card__role">Senior UI and RTL systems designer</p>
                  </div>
                  <p class="team-card__focus">Arabic typography, WCAG AA accessibility</p>
                </div>
              </article>
            </li>
          </ul>
        </div>

        <!-- Practice 3: Marketing & Production -->
        <div id="practice-marketing" class="team-practice" data-team-group="marketing">
          <div class="team-practice__head">
            <h3 class="team-practice__title">Marketing and production</h3>
            <p class="team-practice__desc">
              Media buyers, attribution specialists, and in-house commercial directors connecting launch campaigns directly to product revenue.
            </p>
          </div>

          <ul class="team-cards">
            <li class="team-cards__item">
              <article class="team-card">
                <div class="team-card__media">
                  <img
                    class="team-card__photo"
                    src="{{ asset('assets/img/team/khaled-mansour.jpg') }}"
                    alt="Portrait of Khaled Mansour, Performance marketing lead"
                    width="640"
                    height="800"
                    loading="lazy"
                  />
                </div>
                <div class="team-card__body">
                  <div class="team-card__identity">
                    <h4 class="team-card__name">Khaled Mansour</h4>
                    <p class="team-card__role">Performance marketing lead</p>
                  </div>
                  <p class="team-card__focus">Paid acquisition, multi-market scaling</p>
                </div>
              </article>
            </li>

            <li class="team-cards__item">
              <article class="team-card">
                <div class="team-card__media">
                  <img
                    class="team-card__photo"
                    src="{{ asset('assets/img/team/seif-hany.jpg') }}"
                    alt="Portrait of Seif El-Din Hany, Analytics and attribution lead"
                    width="640"
                    height="800"
                    loading="lazy"
                  />
                </div>
                <div class="team-card__body">
                  <div class="team-card__identity">
                    <h4 class="team-card__name">Seif El-Din Hany</h4>
                    <p class="team-card__role">Analytics and attribution lead</p>
                  </div>
                  <p class="team-card__focus">Server-side tracking, cohort retention</p>
                </div>
              </article>
            </li>

            <li class="team-cards__item">
              <article class="team-card">
                <div class="team-card__media">
                  <img
                    class="team-card__photo"
                    src="{{ asset('assets/img/team/ahmed-elgamal.jpg') }}"
                    alt="Portrait of Ahmed El-Gamal, Commercial film producer"
                    width="640"
                    height="800"
                    loading="lazy"
                  />
                </div>
                <div class="team-card__body">
                  <div class="team-card__identity">
                    <h4 class="team-card__name">Ahmed El-Gamal</h4>
                    <p class="team-card__role">Commercial film producer</p>
                  </div>
                  <p class="team-card__focus">Launch films, product photography</p>
                </div>
              </article>
            </li>

            <li class="team-cards__item">
              <article class="team-card">
                <div class="team-card__media">
                  <img
                    class="team-card__photo"
                    src="{{ asset('assets/img/team/ramy-tarek.jpg') }}"
                    alt="Portrait of Ramy Tarek, Technical SEO and content strategist"
                    width="640"
                    height="800"
                    loading="lazy"
                  />
                </div>
                <div class="team-card__body">
                  <div class="team-card__identity">
                    <h4 class="team-card__name">Ramy Tarek</h4>
                    <p class="team-card__role">Technical SEO and content strategist</p>
                  </div>
                  <p class="team-card__focus">Arabic and English search architecture</p>
                </div>
              </article>
            </li>
          </ul>
        </div>
      </div>
    </section>

    <!-- 4. How Our Teams Work -->
    <section class="team-band team-band--surface" aria-labelledby="culture-heading">
      <div class="team-container">
        <div class="team-culture">
          <div class="team-section-head">
            <h2 id="culture-heading" class="team-heading">How we work together</h2>
            <div class="team-section-head__body">
              <p class="team-section-head__lead">
                Every client engagement pairs engineering, design, and growth under one shared roadmap so software quality and commercial performance move together.
              </p>
            </div>
          </div>

          <ul class="team-culture__grid">
            <li class="team-culture__item">
              <h3 class="team-culture__title">Small senior squads</h3>
              <p class="team-culture__text">
                Projects are staffed with experienced engineers, designers, and marketers who work directly in the codebase and campaigns every day.
              </p>
            </li>
            <li class="team-culture__item">
              <h3 class="team-culture__title">Shared accountability</h3>
              <p class="team-culture__text">
                Product teams and growth specialists review the same activation, reliability, and retention metrics each week.
              </p>
            </li>
            <li class="team-culture__item">
              <h3 class="team-culture__title">One studio in New Cairo</h3>
              <p class="team-culture__text">
                Working side by side in our New Cairo headquarters keeps architecture reviews, design critiques, and launch shoots fast and direct.
              </p>
            </li>
          </ul>

          <div class="team-culture__media">
            <img
              class="team-culture__photo"
              src="{{ asset('assets/img/careers/culture.jpg') }}"
              alt="Empetix team members collaborating around a studio workshop table in New Cairo"
              width="1600"
              height="900"
              loading="lazy"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- 5. Join the Team / General Company Contact -->
    <section class="team-band team-band--white" aria-labelledby="join-heading">
      <div class="team-container">
        <div class="team-join">
          <div class="team-join__main">
            <h2 id="join-heading" class="team-heading">Join Empetix Solution</h2>
            <p class="team-join__copy">
              We hire engineers, designers, and marketers who care deeply about craft and measurable work. Explore open positions on our careers portal or reach out to our company desk.
            </p>
          </div>
          <div class="team-join__actions">
            <a href="{{ url('/careers') }}" class="team-btn team-btn--primary">View open roles</a>
            <a href="{{ url('/contact') }}" class="team-text-link">Start a project</a>
          </div>
        </div>
      </div>
    </section>
@endsection
