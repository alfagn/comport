@extends('layouts.public', ['activePage' => 'partners'])

@section('meta')
  <meta name="description" content="{{ sec('partners', 'copy_8f08704df899') }}"/>
  <title>{{ sec('partners', 'copy_2f39acc5afa8') }}</title>
  <!-- Open Graph / WhatsApp Preview -->
  <meta name="theme-color" content="#0A111C"/>
  <meta property="og:type" content="website"/>
  <meta property="og:site_name" content="Empetix Solution"/>
  <meta property="og:title" content="{{ sec('partners', 'copy_2f39acc5afa8') }}"/>
  <meta property="og:description" content="{{ sec('partners', 'copy_8f08704df899') }}"/>
  <meta property="og:url" content="{{ url()->current() }}"/>
  <meta property="og:image" content="{{ content_asset(sec('global', 'social_preview')) }}"/>
  <meta property="og:image:url" content="{{ content_asset(sec('global', 'social_preview')) }}"/>
  <meta property="og:image:secure_url" content="{{ content_asset(sec('global', 'social_preview')) }}"/>
  <meta property="og:image:type" content="image/jpeg"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="{{ sec('partners', 'copy_46054f40eb9f') }}"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="{{ sec('partners', 'copy_2f39acc5afa8') }}"/>
  <meta name="twitter:description" content="{{ sec('partners', 'copy_8f08704df899') }}"/>
  <meta name="twitter:image" content="{{ content_asset(sec('global', 'social_preview')) }}"/>
@endsection

@section('content')
<main id="main-content">


    <section class="page-hero">
      <div class="container">
        <ol class="page-hero__crumbs" aria-label="Breadcrumb">
          <li><a href="{{ url('/') }}">{{ sec('partners', 'copy_66b93f927675') }}</a></li>
          <li aria-current="page">{{ sec('partners', 'copy_91619d630bd7') }}</li>
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
        <h2 id="alliance-heading" class="sr-only">{{ sec('partners', 'copy_6a83c03a373c') }}</h2>
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
            <h2 id="models-heading" class="t-h2">{{ sec('partners', 'copy_a2ae320b545a') }}</h2>
          </div>
        </div>

        <div class="programme">
          <!-- LOOP: programme-item -->
          <div class="programme__row reveal">
            <span class="programme__num">01</span>
            <h3 class="programme__title">{{ sec('partners', 'copy_4cfbabb703b1') }}</h3>
            <p class="programme__text">
              {{ sec('partners', 'copy_b71f8f98085f') }}
            </p>
            <ul class="programme__list">
              <li>{{ sec('partners', 'copy_8d0e286535cb') }}</li>
              <li>{{ sec('partners', 'copy_6d00c23a58e6') }}</li>
              <li>{{ sec('partners', 'copy_f8e66b52cd2e') }}</li>
            </ul>
          </div>
          <!-- END LOOP: programme-item -->

          <!-- LOOP: programme-item -->
          <div class="programme__row reveal">
            <span class="programme__num">02</span>
            <h3 class="programme__title">{{ sec('partners', 'copy_a7c536725435') }}</h3>
            <p class="programme__text">
              {{ sec('partners', 'copy_1c39b3ea75b6') }}
            </p>
            <ul class="programme__list">
              <li>{{ sec('partners', 'copy_5a7ccc304898') }}</li>
              <li>{{ sec('partners', 'copy_e809233c6f40') }}</li>
              <li>{{ sec('partners', 'copy_8ec34dd420cb') }}</li>
            </ul>
          </div>
          <!-- END LOOP: programme-item -->

          <!-- LOOP: programme-item -->
          <div class="programme__row reveal">
            <span class="programme__num">03</span>
            <h3 class="programme__title">{{ sec('partners', 'copy_c29332f6ecd5') }}</h3>
            <p class="programme__text">
              {{ sec('partners', 'copy_bce7d6d08d31') }}
            </p>
            <ul class="programme__list">
              <li>{{ sec('partners', 'copy_b15f22e26281') }}</li>
              <li>{{ sec('partners', 'copy_ab7ea0bca7f1') }}</li>
              <li>{{ sec('partners', 'copy_834061aa009a') }}</li>
            </ul>
          </div>
          <!-- END LOOP: programme-item -->
        </div>
      </div>
    </section>

    <!-- Partner Testimonial -->
    <section class="section is-inverse" aria-label="Partner endorsement">
      <div class="container quote reveal">
        <span class="eyebrow eyebrow--inverse">{{ sec('partners', 'copy_ad9af5066e92') }}</span>
        <blockquote class="quote__text">
          {{ sec('partners', 'copy_d263eb31cbbf') }}
        </blockquote>
        <div class="quote__by">
          <div>
            <strong>{{ sec('partners', 'copy_ca4c3a1a78ed') }}</strong>
            <span>{{ sec('partners', 'copy_dd7e29f95bf4') }}</span>
          </div>
        </div>
      </div>
    </section>
@endsection
