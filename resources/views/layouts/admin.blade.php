<!DOCTYPE html>
{{-- Empetix Command — the CMS dashboard shell. --}}
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="robots" content="noindex, nofollow"/>
  <title>@yield('title', 'Dashboard') — Empetix Command</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('assets/img/favicon.svg') }}"/>
  <link rel="preload" as="font" type="font/woff2" href="{{ asset('assets/fonts/archivo-800.woff2') }}" crossorigin/>
  <link rel="preload" as="font" type="font/woff2" href="{{ asset('assets/fonts/public-sans-400.woff2') }}" crossorigin/>
  <link rel="stylesheet" href="{{ asset('assets/css/base/typography.css') }}"/>
  <link rel="stylesheet" href="{{ asset('assets/admin/admin.css') }}"/>
</head>
<body class="admin">
<div class="admin-shell">

  {{-- ============ Sidebar ============ --}}
  <aside class="sidebar" id="sidebar">
    <div class="sidebar__brand">
      <a href="{{ route('admin.dashboard') }}">
        <img src="{{ asset('assets/img/logo/empetix-logo.png') }}" alt="Empetix"/>
      </a>
      <div>
        <small>Command</small>
      </div>
    </div>

    @php
      // Current route name drives the active nav state.
      $r = request()->route()->getName();
      $unread = \App\Models\ContactMessage::unread()->count();
    @endphp

    <div class="sidebar__label">Overview</div>
    <a class="nav-item {{ $r === 'admin.dashboard' ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="3" width="8" height="10" rx="1.5"/><rect x="13" y="3" width="8" height="6" rx="1.5"/><rect x="13" y="11" width="8" height="10" rx="1.5"/><rect x="3" y="15" width="8" height="6" rx="1.5"/></svg>
      Dashboard
    </a>
    <a class="nav-item {{ str_starts_with($r, 'admin.inbox') ? 'is-active' : '' }}" href="{{ route('admin.inbox.index') }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 6h16v12H4z"/><path d="M4 7l8 6 8-6"/></svg>
      Inbox
      @if($unread > 0)<span class="badge">{{ $unread }}</span>@endif
    </a>
    <a class="nav-item {{ str_starts_with($r, 'admin.pages') ? 'is-active' : '' }}" href="{{ route('admin.pages.index') }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M5 4h14v16H5z"/><path d="M9 8h6M9 12h6M9 16h4"/></svg>
      Pages &amp; Content
    </a>

    <div class="sidebar__label">Content</div>
    <a class="nav-item {{ str_starts_with($r, 'admin.hero-slides') ? 'is-active' : '' }}" href="{{ route('admin.hero-slides.index') }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 15l5-5 4 4 3-3 6 6"/></svg>
      Hero Slides
    </a>
    <a class="nav-item {{ str_starts_with($r, 'admin.services') ? 'is-active' : '' }}" href="{{ route('admin.services.index') }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3l8 4.5v9L12 21l-8-4.5v-9z"/><path d="M12 12l8-4.5M12 12v9M12 12L4 7.5"/></svg>
      Services
    </a>
    <a class="nav-item {{ str_starts_with($r, 'admin.industries') ? 'is-active' : '' }}" href="{{ route('admin.industries.index') }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 21V8l6 4V8l6 4V5l6 3v13z"/></svg>
      Industries
    </a>
    <a class="nav-item {{ str_starts_with($r, 'admin.projects') ? 'is-active' : '' }}" href="{{ route('admin.projects.index') }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 7h16v13H4z"/><path d="M9 7V4h6v3M4 12h16"/></svg>
      Projects
    </a>
    <a class="nav-item {{ str_starts_with($r, 'admin.members') ? 'is-active' : '' }}" href="{{ route('admin.members.index') }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 20c.6-3.4 2.8-5 5.5-5s4.9 1.6 5.5 5M16 5.5a3 3 0 010 5.8M17.5 15.6c1.7.6 2.7 2 3 4.4"/></svg>
      Team &amp; Founders
    </a>
    <a class="nav-item {{ str_starts_with($r, 'admin.jobs') ? 'is-active' : '' }}" href="{{ route('admin.jobs.index') }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2"/></svg>
      Careers
    </a>
    <a class="nav-item {{ str_starts_with($r, 'admin.posts') ? 'is-active' : '' }}" href="{{ route('admin.posts.index') }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M5 4h11l3 3v13H5z"/><path d="M9 9h6M9 13h6M9 17h4"/></svg>
      Blog &amp; Media
    </a>
    <a class="nav-item {{ str_starts_with($r, 'admin.partners') ? 'is-active' : '' }}" href="{{ route('admin.partners.index') }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M8 12l3 3 5-6"/><circle cx="12" cy="12" r="9"/></svg>
      Partners
    </a>

    <a class="nav-item {{ str_starts_with($r, 'admin.navigation') ? 'is-active' : '' }}" href="{{ route('admin.navigation.index') }}">
      <svg viewBox="0 0 20 20" fill="none"><path d="M3 5h14M3 10h14M3 15h9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
      Navigation
    </a>
    <a class="nav-item {{ str_starts_with($r, 'admin.content-lists') ? 'is-active' : '' }}" href="{{ route('admin.content-lists.index') }}">
      <svg viewBox="0 0 20 20" fill="none"><rect x="3" y="3" width="14" height="6" rx="1.5" stroke="currentColor" stroke-width="1.6"/><rect x="3" y="11" width="14" height="6" rx="1.5" stroke="currentColor" stroke-width="1.6"/></svg>
      Content Lists
    </a>
    <div class="sidebar__label">Site</div>
    <a class="nav-item {{ str_starts_with($r, 'admin.settings') ? 'is-active' : '' }}" href="{{ route('admin.settings.edit') }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="3"/><path d="M19 12a7 7 0 00-.15-1.4l2-1.55-2-3.46-2.35.95a7 7 0 00-2.42-1.4L13.7 2.6h-3.4l-.38 2.54a7 7 0 00-2.42 1.4l-2.35-.95-2 3.46 2 1.55a7 7 0 000 2.8l-2 1.55 2 3.46 2.35-.95a7 7 0 002.42 1.4l.38 2.54h3.4l.38-2.54a7 7 0 002.42-1.4l2.35.95 2-3.46-2-1.55c.1-.45.15-.92.15-1.4z"/></svg>
      Settings
    </a>
    <a class="nav-item" href="{{ url('/') }}" target="_blank">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M14 4h6v6M20 4L10 14M9 5H5a1 1 0 00-1 1v13a1 1 0 001 1h13a1 1 0 001-1v-4"/></svg>
      View Website
    </a>

    <div class="sidebar__foot">
      <div class="user-chip">
        <div class="user-chip__avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div>
          <div class="user-chip__name">{{ auth()->user()->name }}</div>
          <div class="user-chip__role">{{ auth()->user()->email }}</div>
        </div>
      </div>
      <form method="post" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="btn btn--ghost btn--sm" style="width:100%;justify-content:center;margin-top:10px;">Sign out</button>
      </form>
    </div>
  </aside>

  {{-- ============ Main ============ --}}
  <div class="main">
    <div class="topbar">
      <button class="menu-btn" onclick="document.getElementById('sidebar').classList.toggle('is-open')">☰</button>
      <div class="topbar__titles">
        <span class="crumb">@yield('crumb')</span>
        <h1>@yield('title', 'Dashboard')</h1>
      </div>
      <div class="topbar__actions">@yield('actions')</div>
    </div>

    <div class="content">
      @if(session('success'))
        <div class="flash">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
          {{ session('success') }}
        </div>
      @endif

      @yield('content')
    </div>
  </div>
</div>
</body>
</html>
