@extends('layouts.public', ['activePage' => 'partners'])

@section('meta')
  <meta name="description" content="Strategic corporate partners, enterprise clients, and technology alliances partnering with Empetix."/>
  <title>Corporate Partners & Technology Alliances — Empetix</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="Corporate Partners & Technology Alliances — Empetix"/>
  <meta property="og:description" content="Strategic corporate partners, enterprise clients, and technology alliances partnering with Empetix."/>
  <meta property="og:url" content="https://empetix.vercel.app/partners.html"/>
  <meta property="og:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:secure_url" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="Empetix Solution Logo"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="Corporate Partners & Technology Alliances — Empetix"/>
  <meta name="twitter:description" content="Strategic corporate partners, enterprise clients, and technology alliances partnering with Empetix."/>
  <meta name="twitter:image" content="https://empetix.vercel.app/assets/img/og-empetix.jpg"/>
@endsection

@section('content')
<main id="main-content">


    <section class="page-hero">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ url('/') }}">Home</a></li>
          <li aria-current="page">Corporate Partners</li>
        </ol>
        <h1 class="page-hero__title">{{ sec('partners', 'hero_title', 'Corporate partners & technology alliances.') }}</h1>
        <div class="page-hero__row">
          <div class="page-hero__label">
            <span class="eyebrow">{{ sec('partners', 'hero_eyebrow', 'Ecosystem & Alliances') }}</span>
          </div>
          <p class="page-hero__lead">
            {{ sec('partners', 'hero_lead', 'We partner with ambitious enterprises, venture-backed category leaders, and global cloud and marketing platforms to deliver end-to-end digital transformation and commercial growth.') }}
          </p>
          <div class="page-hero__aside">
            <a href="{{ url('/contact') }}" class="btn btn--primary btn--sm">{{ sec('partners', 'cta_button', 'Become a Partner') }}</a>
          </div>
        </div>
      </div>
    </section>

    <!-- 12-Partner Logo Grid -->
    <section class="section section--flush-top" aria-labelledby="alliance-heading">
      <div class="container">
        <h2 id="alliance-heading" class="sr-only">Verified Corporate and Technology Partners</h2>
        <div class="logo-wall reveal">
          @foreach($partners as $partner)
          <div class="logo-wall__item">
            <span class="logo-wall__tier">{{ $partner->tier }}</span>
            <img src="{{ str_starts_with((string) $partner->logo, 'uploads/') ? asset('storage/'.$partner->logo) : asset($partner->logo) }}" alt="{{ $partner->name }}" width="220" height="56"/>
          </div>
@endforeach
        </div>
      </div>
    </section>

    <!-- 3 Partnership Models -->
    <section class="section section--alt section--ruled" aria-labelledby="models-heading">
      <div class="container">
        <div class="section-head reveal">
          <div class="section-head__label"></div>
          <div class="section-head__title">
            <h2 id="models-heading" class="t-h2">Three ways organizations partner with Empetix.</h2>
          </div>
        </div>

        <div class="programme">
          <!-- LOOP: programme-item -->
          <div class="programme__row reveal">
            <span class="programme__num">01</span>
            <h3 class="programme__title">Enterprise Product &amp; Growth Squads</h3>
            <p class="programme__text">
              Dedicated cross-functional squads (Laravel/Flutter engineers, product designers, and performance marketers) embedded directly alongside your internal leadership for 6 to 24 months.
            </p>
            <ul class="programme__list">
              <li>Shared weekly revenue &amp; velocity KPIs</li>
              <li>Full source-code &amp; ad-account ownership</li>
              <li>On-site workshops in our New Cairo studio</li>
            </ul>
          </div>
          <!-- END LOOP: programme-item -->

          <!-- LOOP: programme-item -->
          <div class="programme__row reveal">
            <span class="programme__num">02</span>
            <h3 class="programme__title">Technology &amp; Platform Co-Delivery</h3>
            <p class="programme__text">
              We collaborate with cloud providers, payment processors, and ERP vendors (AWS, Stripe, Shopify Plus, HubSpot) as the certified regional engineering and migration partner.
            </p>
            <ul class="programme__list">
              <li>Certified solutions architecture</li>
              <li>Custom middleware &amp; localized payment rails</li>
              <li>Joint enterprise go-to-market campaigns</li>
            </ul>
          </div>
          <!-- END LOOP: programme-item -->

          <!-- LOOP: programme-item -->
          <div class="programme__row reveal">
            <span class="programme__num">03</span>
            <h3 class="programme__title">Venture Studio &amp; Seed Co-Building</h3>
            <p class="programme__text">
              For funded founders and corporate spin-offs: we take new products from zero to v1.0 launch and initial customer acquisition in 12 weeks through structured fee-plus-milestone partnerships.
            </p>
            <ul class="programme__list">
              <li>5-Day architecture &amp; growth sprint</li>
              <li>Production MVP + brand system + launch film</li>
              <li>First 10,000 customers acquisition playbook</li>
            </ul>
          </div>
          <!-- END LOOP: programme-item -->
        </div>
      </div>
    </section>

    <!-- Partner Testimonial -->
    <section class="section is-inverse" aria-label="Partner endorsement">
      <div class="container quote reveal">
        <span class="eyebrow eyebrow--inverse">02 / Partner Voice</span>
        <blockquote class="quote__text">
          Empetix doesn&rsquo;t operate like an external vendor. Their engineers rebuilt our logistics control tower while their growth team rebuilt our B2B demand engine across the GCC.
        </blockquote>
        <div class="quote__by">
          <div>
            <strong>Khaled Al-Mansoor</strong>
            <span>Chief Operating Officer, CargoLine MENA (Riyadh)</span>
          </div>
        </div>
      </div>
    </section>
@endsection
