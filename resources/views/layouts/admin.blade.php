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
  <script>
    // Theme: persisted admin light/dark choice, applied before first paint.
    (function () {
      var saved = null;
      try { saved = localStorage.getItem('empetix-admin-theme'); } catch (e) {}
      document.documentElement.setAttribute('data-theme', saved === 'dark' ? 'dark' : 'light');
      window.empetixToggleTheme = function () {
        var next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        try { localStorage.setItem('empetix-admin-theme', next); } catch (e) {}
        document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
          btn.setAttribute('aria-pressed', next === 'dark' ? 'true' : 'false');
          btn.querySelector('.icon-moon').style.display = next === 'dark' ? 'none' : '';
          btn.querySelector('.icon-sun').style.display = next === 'dark' ? '' : 'none';
        });
      };
      document.addEventListener('DOMContentLoaded', function () {
        var dark = document.documentElement.getAttribute('data-theme') === 'dark';
        document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
          btn.setAttribute('aria-pressed', dark ? 'true' : 'false');
          btn.querySelector('.icon-moon').style.display = dark ? 'none' : '';
          btn.querySelector('.icon-sun').style.display = dark ? '' : 'none';
        });
      });
    })();
  </script>
</head>
<body class="admin">
  {{-- Decorative orange ribbon sweeps from the user's reference design.
       Pure background artwork, kept separate from real data charts. --}}
  <svg class="bg-ribbon bg-ribbon--top" viewBox="0 0 600 400" preserveAspectRatio="none" aria-hidden="true">
    <defs><linearGradient id="ribbonGradT" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#FF4D1C" stop-opacity="0.55"/><stop offset="1" stop-color="#FF9E7A" stop-opacity="0"/>
    </linearGradient></defs>
    <path d="M600 0 L600 120 C470 150 380 60 260 110 C180 142 140 210 60 230 C150 90 320 -40 600 0 Z" fill="url(#ribbonGradT)"/>
    <path d="M600 0 L600 60 C500 90 430 30 340 60 C420 -20 520 -30 600 0 Z" fill="#FF4D1C" fill-opacity="0.35"/>
  </svg>
  <svg class="bg-ribbon bg-ribbon--bottom" viewBox="0 0 600 400" preserveAspectRatio="none" aria-hidden="true">
    <defs><linearGradient id="ribbonGradB" x1="1" y1="1" x2="0" y2="0">
      <stop offset="0" stop-color="#FF4D1C" stop-opacity="0.45"/><stop offset="1" stop-color="#FF9E7A" stop-opacity="0"/>
    </linearGradient></defs>
    <path d="M600 400 L600 260 C470 230 380 330 250 290 C160 262 120 200 40 180 C140 320 330 440 600 400 Z" fill="url(#ribbonGradB)"/>
  </svg>
<div class="admin-shell">

  {{-- ============ Sidebar ============ --}}
  <aside class="sidebar" id="sidebar">
    <div class="sidebar__brand">
      <a href="{{ route('admin.dashboard') }}">
        <img src="{{ asset('assets/img/logo/empetix-logo.png') }}" alt="Empetix"/>
      </a>
      <div>
        <small>Solutions</small>
      </div>
    </div>

    @php
      // Current route name drives the active nav state.
      $r = request()->route()->getName();
      // Unread briefs feed both the sidebar badge and the topbar bell.
      $unread = \App\Models\ContactMessage::unread()->count();
      // New job applications badge on the Careers-adjacent Applications item.
      $newApplications = \App\Models\JobApplication::new()->count();
      $adminUser = auth()->user();
    @endphp

    {{-- Scrollable nav region: the brand and the profile footer stay pinned. --}}
    <nav class="sidebar__nav" aria-label="Dashboard sections">
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
    <a class="nav-item {{ str_starts_with($r, 'admin.applications') ? 'is-active' : '' }}" href="{{ route('admin.applications.index') }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M5 4h11l3 3v13H5z"/><path d="M9 12h6M9 16h6"/><circle cx="9" cy="8" r="1.6"/></svg>
      Applications
      @if($newApplications)<span class="badge">{{ $newApplications }}</span>@endif
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

    </nav>

    <div class="sidebar__foot">
      <div class="user-chip">
        <div class="user-chip__avatar">{{ strtoupper(substr($adminUser->name, 0, 1)) }}</div>
        <div>
          <div class="user-chip__name">{{ $adminUser->name }}</div>
          <div class="user-chip__role">{{ $adminUser->email }}</div>
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
      <button class="menu-btn" onclick="document.getElementById('sidebar').classList.toggle('is-open')" aria-label="Toggle menu">☰</button>
      <div class="topbar__titles">
        @if($__env->yieldContent('page_header') !== 'hide')
          <span class="crumb">@yield('crumb')</span>
          <h1>@yield('title', 'Dashboard')</h1>
        @endif
      </div>

      {{-- Quick-jump search: filters the admin sections client-side (opens with / or Cmd+K). --}}
      <button class="topbar-search" id="quicknav-open" type="button">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
        <span>Search sections…</span>
        <kbd>⌘K</kbd>
      </button>

      <div class="topbar__actions">
        @yield('actions')
        {{-- Unread-briefs bell: the badge is the real unread count. --}}
        <button class="icon-btn" type="button" data-theme-toggle onclick="empetixToggleTheme()" aria-label="Toggle dark mode" aria-pressed="false">
          <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" width="18" height="18"><path d="M20 13.5A8.5 8.5 0 0 1 10.5 4 8.5 8.5 0 1 0 20 13.5z"/></svg>
          <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" width="18" height="18" style="display:none"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
        </button>
        <a class="icon-btn" href="{{ route('admin.inbox.index') }}" aria-label="Inbox, {{ $unread }} unread">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M6 16v-5a6 6 0 1112 0v5l1.5 2.5h-15z"/><path d="M10 21a2.2 2.2 0 004 0"/></svg>
          @if($unread > 0)<span class="icon-btn__badge">{{ $unread }}</span>@endif
        </a>
        {{-- Account menu --}}
        <div class="avatar-menu" id="avatar-menu">
          <button class="avatar" type="button" aria-haspopup="true" aria-expanded="false"
                  onclick="document.getElementById('avatar-menu').classList.toggle('is-open')">{{ strtoupper(substr($adminUser->name, 0, 1)) }}</button>
          <div class="avatar-menu__drop">
            <div class="avatar-menu__head">
              <strong>{{ $adminUser->name }}</strong>
              <span>{{ $adminUser->email }}</span>
            </div>
            <a href="{{ url('/') }}" target="_blank">View website</a>
            <form method="post" action="{{ route('admin.logout') }}">@csrf<button type="submit">Sign out</button></form>
          </div>
        </div>
      </div>
    </div>

    {{-- Quick-nav overlay: real client-side filter over the sections below. --}}
    <div class="quicknav" id="quicknav" hidden>
      <div class="quicknav__panel">
        <input type="search" id="quicknav-input" placeholder="Jump to a section…" autocomplete="off"/>
        <ul id="quicknav-list">
          <li data-name="dashboard overview"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li data-name="inbox briefs messages contact"><a href="{{ route('admin.inbox.index') }}">Inbox</a></li>
          <li data-name="pages content sections"><a href="{{ route('admin.pages.index') }}">Pages &amp; Content</a></li>
          <li data-name="hero slides homepage"><a href="{{ route('admin.hero-slides.index') }}">Hero Slides</a></li>
          <li data-name="services"><a href="{{ route('admin.services.index') }}">Services</a></li>
          <li data-name="industries"><a href="{{ route('admin.industries.index') }}">Industries</a></li>
          <li data-name="projects work portfolio"><a href="{{ route('admin.projects.index') }}">Projects</a></li>
          <li data-name="team founders members"><a href="{{ route('admin.members.index') }}">Team &amp; Founders</a></li>
          <li data-name="careers jobs roles hiring"><a href="{{ route('admin.jobs.index') }}">Careers</a></li>
          <li data-name="applications candidates cvs resumes hiring"><a href="{{ route('admin.applications.index') }}">Applications</a></li>
          <li data-name="blog media posts news"><a href="{{ route('admin.posts.index') }}">Blog &amp; Media</a></li>
          <li data-name="partners clients logos"><a href="{{ route('admin.partners.index') }}">Partners</a></li>
          <li data-name="navigation menu mega"><a href="{{ route('admin.navigation.index') }}">Navigation</a></li>
          <li data-name="content lists about careers timeline"><a href="{{ route('admin.content-lists.index') }}">Content Lists</a></li>
          <li data-name="settings site social contact"><a href="{{ route('admin.settings.edit') }}">Settings</a></li>
        </ul>
      </div>
    </div>
    <script>
      // Quick-nav: open with Cmd/Ctrl+K or the search pill, filter as you type,
      // Enter follows the first visible section, Escape closes.
      (function () {
        var overlay = document.getElementById('quicknav');
        var input = document.getElementById('quicknav-input');
        var items = Array.prototype.slice.call(document.querySelectorAll('#quicknav-list li'));
        function open() { overlay.hidden = false; input.value = ''; filter(''); input.focus(); }
        function close() { overlay.hidden = true; }
        function filter(q) {
          q = q.trim().toLowerCase();
          items.forEach(function (li) {
            li.style.display = !q || li.dataset.name.indexOf(q) !== -1 ? '' : 'none';
          });
        }
        document.getElementById('quicknav-open').addEventListener('click', open);
        overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });
        input.addEventListener('input', function () { filter(input.value); });
        input.addEventListener('keydown', function (e) {
          if (e.key === 'Enter') {
            var first = items.find(function (li) { return li.style.display !== 'none'; });
            if (first) window.location = first.querySelector('a').href;
          }
        });
        document.addEventListener('keydown', function (e) {
          if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); open(); }
          if (e.key === 'Escape') close();
        });
      })();
    </script>

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

{{-- DataTables stack: jQuery + DataTables.net (MIT, vendored locally) load
     on every admin page; each listing pushes its init script here. --}}
<script src="{{ asset('assets/admin/vendor/jquery.min.js') }}"></script>
<script src="{{ asset('assets/admin/vendor/jquery.dataTables.min.js') }}"></script>
@stack('datatables')
</body>
</html>
