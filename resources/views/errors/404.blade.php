<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="robots" content="noindex, follow"/>
  <title>{{ sec('error', 'meta_title') }}</title>
  <link rel="icon" href="{{ brand_favicon() }}"/>
  <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}"/>
  <link rel="stylesheet" href="{{ asset('assets/css/pages/error.css') }}"/>
</head>
<body class="error-page">
  <header class="error-header">
    <a href="{{ route('home') }}" aria-label="Empetix Solution home"><img src="{{ brand_logo(dark: true) }}" alt="Empetix Solution" width="176" height="60"/></a>
    <span class="error-status"><span aria-hidden="true"></span>404 / {{ sec('error', 'status_label') }}</span>
  </header>
  <main class="error-main" id="main-content">
    <div class="error-copy">
      <p class="error-eyebrow">{{ sec('error', 'eyebrow') }}</p>
      <h1>{{ sec('error', 'title') }}</h1>
      <p class="error-description">{{ sec('error', 'description') }}</p>
      <div class="error-actions">
        <a class="error-primary" href="{{ route('home') }}">{{ sec('error', 'home_label') }} <span aria-hidden="true">↗</span></a>
        <a class="error-secondary" href="{{ route('work') }}">{{ sec('error', 'work_label') }} <span aria-hidden="true">→</span></a>
      </div>
    </div>
    <div class="error-art" aria-hidden="true">
      <span class="error-coordinate">EMPETIX / ROUTE NOT FOUND</span>
      <div class="error-digits">4<span>0</span>4</div>
      <img src="{{ asset('assets/images/3d/3d-magnifier.png') }}" alt="" width="256" height="256"/>
      <span class="error-art-line"></span>
      <span class="error-art-caption">404 &nbsp; / &nbsp; OFF THE GRID</span>
    </div>
  </main>
  <footer class="error-footer"><span>Empetix Solution</span><a href="{{ route('contact') }}">{{ sec('error', 'contact_label') }} <span aria-hidden="true">↗</span></a></footer>
</body>
</html>
