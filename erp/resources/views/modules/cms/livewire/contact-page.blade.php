<div>
    <!-- Page Hero -->
    <section class="page-hero page-hero--dark">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ route('home') }}">Home</a></li>
          <li aria-current="page">Contact Us</li>
        </ol>
        <h1 class="page-hero__title">Start a project, request a technical audit, or partner with our studios.</h1>
        <div class="page-hero__row">
          <div class="page-hero__label">
            <span class="eyebrow eyebrow--inverse">Direct Executive Access</span>
          </div>
          <p class="page-hero__lead">
            At Empetix, you never speak with junior sales reps. Every inquiry goes directly to our co-founders and practice heads in New Cairo.
          </p>
          <div class="page-hero__aside">
            <span class="hero__badge">AVG RESPONSE: &lt; 6 BUSINESS HOURS</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Direct Department Desks -->
    <section class="section">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 class="t-h2">Reach the right practice desk immediately.</h2>
          </div>
        </div>

        <div class="channels">
          <article class="channel-card reveal">
            <div>
              <div class="channel-card__top">
                <span class="channel-card__tag">SOFTWARE ENGINEERING</span>
                <span class="channel-card__sla">SLA: &lt; 12H</span>
              </div>
              <h3 class="channel-card__title">Software &amp; Platform Architecture</h3>
              <p class="channel-card__desc">
                For web platforms, mobile apps, multi-tenant SaaS architecture, cloud infrastructure, and technical code audits.
              </p>
            </div>
            <div class="channel-card__foot">
              <a href="mailto:{{ $contactEmail }}" class="channel-card__email">{{ $contactEmail }}</a>
            </div>
          </article>

          <article class="channel-card reveal">
            <div>
              <div class="channel-card__top">
                <span class="channel-card__tag">GROWTH &amp; PERFORMANCE</span>
                <span class="channel-card__sla">SLA: &lt; 12H</span>
              </div>
              <h3 class="channel-card__title">Performance Marketing &amp; Media</h3>
              <p class="channel-card__desc">
                For full-funnel media buying, server-side CAPI attribution, conversion rate optimization, and creative shoots.
              </p>
            </div>
            <div class="channel-card__foot">
              <a href="mailto:growth@empetix.com" class="channel-card__email">growth@empetix.com</a>
            </div>
          </article>

          <article class="channel-card reveal">
            <div>
              <div class="channel-card__top">
                <span class="channel-card__tag">TALENT &amp; CAREERS</span>
                <span class="channel-card__sla">OPEN VACANCIES</span>
              </div>
              <h3 class="channel-card__title">Engineering &amp; Creative Talent</h3>
              <p class="channel-card__desc">
                For senior engineers, product designers, filmmakers, and performance marketers joining our studios.
              </p>
            </div>
            <div class="channel-card__foot">
              <a href="{{ route('site.careers') }}" class="channel-card__email">View Careers Portal &rarr;</a>
            </div>
          </article>
        </div>

        <!-- Interactive Project Brief Form -->
        <div class="contact" style="margin-top: 3rem;">
          <div class="contact__form reveal">
            <div class="form-card">
              <div class="form-card__head">
                <div>
                  <span class="eyebrow">02 / Interactive Project Brief</span>
                  <h2 class="t-h3">Configure your project or partnership brief.</h2>
                </div>
                <span class="t-meta">PROTECTED BY MUTUAL NDA</span>
              </div>

              @if($submitted)
                <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem;">
                  <h4 style="color: #15803d; font-weight: 700; margin-bottom: 0.5rem; font-size: 1.1rem;">&#10003; Brief Delivered Successfully!</h4>
                  <p style="color: #166534; font-size: 0.95rem; line-height: 1.6;">
                    Thank you. Your brief has been delivered directly to our co-founders and engineering leads. We will review your requirements and reply with a proposed discovery call within 24 business hours.
                  </p>
                </div>
              @endif

              <form wire:submit="submitMessage" class="form">
                <div class="form__field">
                  <label for="c-name" class="form__label">Full Name <abbr title="Required">*</abbr></label>
                  <input id="c-name" wire:model="name" type="text" class="form__input" placeholder="e.g. Tariq Al-Faisal" required/>
                  @error('name') <span style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form__field">
                  <label for="c-email" class="form__label">Work Email <abbr title="Required">*</abbr></label>
                  <input id="c-email" wire:model="email" type="email" class="form__input" placeholder="name@company.com" required/>
                  @error('email') <span style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form__field">
                  <label for="c-phone" class="form__label">Phone / WhatsApp</label>
                  <input id="c-phone" wire:model="phone" type="text" class="form__input" placeholder="+20 100 000 0000"/>
                  @error('phone') <span style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form__field">
                  <label for="c-subject" class="form__label">Subject / Project Title <abbr title="Required">*</abbr></label>
                  <input id="c-subject" wire:model="subject" type="text" class="form__input" placeholder="e.g. Multi-Tenant SaaS Platform Development" required/>
                  @error('subject') <span style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form__field form__field--full">
                  <label for="c-brief" class="form__label">Project Context, Architecture &amp; Commercial Goals <abbr title="Required">*</abbr></label>
                  <textarea id="c-brief" wire:model="message" class="form__textarea" rows="5" placeholder="Tell us what you are building or scaling, what bottleneck you want to solve, and your target timeline..." required></textarea>
                  @error('message') <span style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</span> @enderror
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
              <p class="t-small"><a href="{{ route('site.team') }}" class="text-link">Meet the full Empetix team &rarr;</a></p>
            </div>

            <div class="contact-block">
              <span class="contact-block__title">Immediate Executive Hotline</span>
              <a href="tel:{{ $contactPhone }}" class="contact-block__big">{{ $contactPhone }}</a>
              <p class="t-small">Sunday &ndash; Thursday, 09:00 &ndash; 18:00 Cairo / Riyadh Time.</p>
            </div>
          </aside>
        </div>
      </div>
    </section>

    <!-- Global Studios Grid -->
    <section class="section section--alt section--ruled" aria-labelledby="studios-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="studios-heading" class="t-h2">Our Studio Locations</h2>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
          <div style="background: #ffffff; border: 1px solid #DCDCD4; border-radius: 8px; padding: 2rem;">
            <span class="eyebrow" style="color: #FF4D1C;">Headquarters</span>
            <h3 class="t-h3" style="margin: 0.5rem 0 1rem;">Cairo, Egypt</h3>
            <p style="color: #62718A; line-height: 1.6;">{{ $cairoHq }}</p>
          </div>

          <div style="background: #ffffff; border: 1px solid #DCDCD4; border-radius: 8px; padding: 2rem;">
            <span class="eyebrow" style="color: #FF4D1C;">GCC Hub</span>
            <h3 class="t-h3" style="margin: 0.5rem 0 1rem;">Riyadh, KSA</h3>
            <p style="color: #62718A; line-height: 1.6;">{{ $riyadhOffice }}</p>
          </div>

          <div style="background: #ffffff; border: 1px solid #DCDCD4; border-radius: 8px; padding: 2rem;">
            <span class="eyebrow" style="color: #FF4D1C;">Europe Hub</span>
            <h3 class="t-h3" style="margin: 0.5rem 0 1rem;">London, UK</h3>
            <p style="color: #62718A; line-height: 1.6;">{{ $londonOffice }}</p>
          </div>
        </div>
      </div>
    </section>
</div>
