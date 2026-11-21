<div>
    <!-- Page Hero -->
    <section class="page-hero">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ route('home') }}">Home</a></li>
          <li aria-current="page">Corporate Partners</li>
        </ol>
        <h1 class="page-hero__title">Technology alliances and corporate partners.</h1>
        <div class="page-hero__row">
          <div class="page-hero__label">
            <span class="eyebrow">Enterprise Ecosystem</span>
          </div>
          <p class="page-hero__lead">
            Empetix works directly alongside leading global cloud providers, payment networks, and growth infrastructure platforms to architect resilient digital operations.
          </p>
          <div class="page-hero__aside">
            <span class="t-meta">{{ $partners->count() }} PARTNERS</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Partners Grid -->
    <section class="section section--flush-top">
      <div class="container">
        <div class="logo-wall" style="margin-top: 1rem;">
          @forelse($partners as $partner)
            <a href="{{ $partner->website_url ?: route('site.contact') }}" target="_blank" rel="noopener" class="logo-wall__item">
              <span class="logo-wall__tier">ALLIANCE</span>
              <img src="{{ asset($partner->logo ?: 'assets/img/partners/aws-partner.svg') }}" alt="{{ $partner->name }}" width="220" height="56"/>
            </a>
          @empty
            <div class="logo-wall__item">
              <span class="logo-wall__tier">CLOUD</span>
              <img src="{{ asset('assets/img/partners/aws-partner.svg') }}" alt="AWS" width="220" height="56"/>
            </div>
            <div class="logo-wall__item">
              <span class="logo-wall__tier">COMMERCE</span>
              <img src="{{ asset('assets/img/partners/shopify-plus.svg') }}" alt="Shopify Plus" width="220" height="56"/>
            </div>
            <div class="logo-wall__item">
              <span class="logo-wall__tier">GROWTH</span>
              <img src="{{ asset('assets/img/partners/meta-business.svg') }}" alt="Meta Business" width="220" height="56"/>
            </div>
            <div class="logo-wall__item">
              <span class="logo-wall__tier">FINTECH</span>
              <img src="{{ asset('assets/img/partners/stripe-partner.svg') }}" alt="Stripe" width="220" height="56"/>
            </div>
          @endforelse
        </div>
      </div>
    </section>

    <!-- Co-Delivery & Enterprise CTA -->
    <section class="section section--alt section--ruled">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 class="t-h2">Become an Alliance Partner</h2>
            <p class="t-lead">We collaborate with software vendors, venture studios, and cloud providers across EMEA.</p>
          </div>
          <div class="section-head__action">
            <a href="{{ route('site.contact') }}" class="btn btn--accent btn--md">
              <span>Start Alliance Discussion</span>
            </a>
          </div>
        </div>
      </div>
    </section>
</div>
