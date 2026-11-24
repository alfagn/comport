<!DOCTYPE html>
<html lang="{{ $locale }}" dir="ltr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>@yield('title')</title>
  <meta name="description" content="@yield('description')"/>
  @if(($preview ?? false) || setting('seo_allow_indexing', '1') === '0' || (isset($pages) && $pages->isEmpty()))
    <meta name="robots" content="noindex, nofollow"/>
  @else
    <meta name="robots" content="index, follow, max-image-preview:large"/>
  @endif
  <link rel="canonical" href="{{ seo_url(isset($page) ? $page->publicPath() : ('/services')) }}"/>
  <meta property="og:type" content="website"/>
  <meta property="og:title" content="@yield('title')"/>
  <meta property="og:description" content="@yield('description')"/>
  <meta property="og:url" content="{{ seo_url(isset($page) ? $page->publicPath() : ('/services')) }}"/>
  <meta property="og:image" content="{{ seo_og_image() }}"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <link rel="icon" href="{{ brand_favicon() }}"/>
  <link rel="stylesheet" href="{{ versioned_asset('assets/css/pages/service-pages.css') }}"/>
  @if(isset($page) && !($preview ?? false))
    <script type="application/ld+json">{!! json_encode(['@@context' => 'https://schema.org', '@type' => 'WebPage', 'name' => $page->title, 'description' => $page->summary, 'inLanguage' => $locale, 'url' => seo_url($page->publicPath())], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
  @endif
</head>
<body>
<a class="skip" href="#content">Skip to content</a>
<header class="service-nav wrap">
  <a href="{{ url('/') }}" aria-label="Empetix home"><img src="{{ brand_logo(dark: true) }}" width="175" height="60" alt="Empetix"/></a>
  <nav aria-label="Main navigation">
    <a href="{{ url('/services') }}">Services</a>
    <a class="nav-cta" href="{{ url('/contact') }}">Contact <span aria-hidden="true">↗</span></a>
  </nav>
</header>
@if($preview ?? false)<aside class="preview-banner" role="status">Private editorial preview. This page is not published. Review the service scope and copy before publishing.</aside>@endif
<main id="content">@yield('content')</main>
<footer class="service-footer wrap"><img src="{{ brand_logo() }}" width="175" height="60" alt="Empetix"/><p>Start with a conversation about your project.</p><a href="{{ url('/contact') }}">Contact Empetix ↗</a></footer>
</body>
</html>
