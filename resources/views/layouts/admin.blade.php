<!DOCTYPE html>
{{-- Empetix Command — the CMS dashboard shell. --}}
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="robots" content="noindex, nofollow"/>
  <title>@yield('title', 'Dashboard') — Empetix Command</title>
  <link rel="icon" href="{{ brand_favicon() }}"/>
  <link rel="preload" as="font" type="font/woff2" href="{{ asset('assets/fonts/archivo-800.woff2') }}" crossorigin/>
  <link rel="preload" as="font" type="font/woff2" href="{{ asset('assets/fonts/public-sans-400.woff2') }}" crossorigin/>
  <link rel="stylesheet" href="{{ asset('assets/css/base/typography.css') }}"/>
  <link rel="stylesheet" href="{{ asset('assets/admin/admin.css') }}?v={{ file_exists(public_path('assets/admin/admin.css')) ? filemtime(public_path('assets/admin/admin.css')) : time() }}"/>
  @livewireStyles
  <script>
    // Theme: persisted admin light/dark choice, applied before first paint.
    (function () {
      var saved = null;
      try { saved = localStorage.getItem('empetix-admin-theme'); } catch (e) {}
      document.documentElement.setAttribute('data-theme', saved === 'dark' ? 'dark' : 'light');
      window.empetixToggleSidebar = function () {
      var sidebar = document.getElementById('sidebar');
      var btn = document.getElementById('sidebar-toggle');
      if (window.matchMedia('(max-width: 980px)').matches) {
        var open = sidebar.classList.toggle('is-open');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      } else {
        var collapsed = document.body.classList.toggle('sidebar-collapsed');
        try { localStorage.setItem('empetix-admin-sidebar', collapsed ? 'collapsed' : 'expanded'); } catch (e) {}
        btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
      }
    };
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
      window.empetixSyncThemeButtons = function () {
        var dark = document.documentElement.getAttribute('data-theme') === 'dark';
        document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
          btn.setAttribute('aria-pressed', dark ? 'true' : 'false');
          btn.querySelector('.icon-moon').style.display = dark ? 'none' : '';
          btn.querySelector('.icon-sun').style.display = dark ? '' : 'none';
        });
      };
      document.addEventListener('DOMContentLoaded', window.empetixSyncThemeButtons);
      // Livewire SPA navigation swaps the body: re-sync the freshly rendered buttons.
      document.addEventListener('livewire:navigated', window.empetixSyncThemeButtons);
      // One-time SPA lifecycle hooks (head survives body swaps):
      if (!window.__empetixNavHooks) {
        window.__empetixNavHooks = true;
        document.addEventListener('livewire:navigating', function () {
          if (window.jQuery && jQuery.fn.dataTable) {
            jQuery.fn.dataTable.tables({ api: true }).destroy();
          }
        });
        document.addEventListener('livewire:navigated', function () {
          // The swap replaces <html> attributes: re-apply the persisted theme.
          try {
            var saved = localStorage.getItem('empetix-admin-theme');
            document.documentElement.setAttribute('data-theme', saved === 'dark' ? 'dark' : 'light');
          } catch (e) {}
          if (window.empetixSyncThemeButtons) window.empetixSyncThemeButtons();
          // Keep the selected destination visible in the independently scrolling rail.
          document.querySelectorAll('.sidebar__nav a.nav-item').forEach(function (link) {
            var active = link.classList.contains('is-active');
            if (active) {
              link.setAttribute('aria-current', 'page');
              link.scrollIntoView({ block: 'nearest' });
            } else {
              link.removeAttribute('aria-current');
            }
          });
          // Tables use their page-scoped Livewire lifecycle initializer.
        });
      }
    })();
  </script>
</head>
<body class="admin {{ request()->routeIs('admin.seo.*', 'admin.sitemap.*', 'admin.service-pages.*') ? 'discovery-admin' : '' }}">
  <script>
    // Sidebar: persisted desktop collapsed state, applied before first paint.
    (function () {
      try {
        if (window.matchMedia('(min-width: 981px)').matches && localStorage.getItem('empetix-admin-sidebar') === 'collapsed') {
          document.body.classList.add('sidebar-collapsed');
        }
      } catch (e) {}
    })();
  </script>
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
      <a class="sidebar__brand-link" href="{{ route('admin.dashboard') }}" wire:navigate onclick="if (document.body.classList.contains('sidebar-collapsed')) { event.preventDefault(); empetixToggleSidebar(); }" title="Empetix Dashboard">
        <img class="sidebar__brand-logo" src="{{ brand_logo() }}" alt="Empetix"/>
        <span class="sidebar__brand-mark" aria-hidden="true">E</span>
      </a>
      <button class="sidebar__collapse-btn" type="button" onclick="empetixToggleSidebar()" aria-label="Toggle sidebar" title="Toggle sidebar">
        <svg class="sidebar__collapse-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="18" height="18" rx="5" ry="5"/>
          <path d="M9 3v18"/>
          <path class="sidebar__collapse-arrow" d="m14 9-3 3 3 3"/>
        </svg>
      </button>
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
    <a class="nav-item {{ $r === 'admin.dashboard' ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-grid.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Dashboard</span>
    </a>
    @can('manage inbox')
    <a class="nav-item {{ str_starts_with($r, 'admin.inbox') ? 'is-active' : '' }}" href="{{ route('admin.inbox.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-inbox.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Inbox</span>
      @if($unread > 0)<span class="badge">{{ $unread }}</span>@endif
    </a>
    @endcan
    @can('manage pages')
    <a class="nav-item {{ str_starts_with($r, 'admin.pages') ? 'is-active' : '' }}" href="{{ route('admin.pages.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-document.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Pages &amp; Content</span>
    </a>
    @endcan

    <div class="sidebar__label">Content</div>
    @can('manage hero slides')
    <a class="nav-item {{ str_starts_with($r, 'admin.hero-slides') ? 'is-active' : '' }}" href="{{ route('admin.hero-slides.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-image.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Hero Slides</span>
    </a>
    @endcan
    @can('manage services')
    <a class="nav-item {{ str_starts_with($r, 'admin.services') ? 'is-active' : '' }}" href="{{ route('admin.services.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-headphones.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Services</span>
    </a>
    <a class="nav-item {{ str_starts_with($r, 'admin.service-pages') ? 'is-active' : '' }}" href="{{ route('admin.service-pages.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-document.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Service Pages</span>
    </a>
    @endcan
    @can('manage industries')
    <a class="nav-item {{ str_starts_with($r, 'admin.industries') ? 'is-active' : '' }}" href="{{ route('admin.industries.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-pie-chart.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Industries</span>
    </a>
    @endcan
    @can('manage projects')
    <a class="nav-item {{ str_starts_with($r, 'admin.projects') ? 'is-active' : '' }}" href="{{ route('admin.projects.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-folder.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Projects</span>
    </a>
    @endcan
    @can('manage members')
    <a class="nav-item {{ str_starts_with($r, 'admin.members') ? 'is-active' : '' }}" href="{{ route('admin.members.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-puzzle-partners.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Team &amp; Founders</span>
    </a>
    @endcan
    @can('manage jobs')
    <a class="nav-item {{ str_starts_with($r, 'admin.jobs') ? 'is-active' : '' }}" href="{{ route('admin.jobs.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-briefcase.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Careers</span>
    </a>
    @endcan
    @can('manage applications')
    <a class="nav-item {{ str_starts_with($r, 'admin.applications') ? 'is-active' : '' }}" href="{{ route('admin.applications.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-mailbox.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Applications</span>
      @if($newApplications)<span class="badge">{{ $newApplications }}</span>@endif
    </a>
    @endcan
    @can('manage posts')
    <a class="nav-item {{ str_starts_with($r, 'admin.posts') ? 'is-active' : '' }}" href="{{ route('admin.posts.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-megaphone.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Blog &amp; Media</span>
    </a>
    @endcan
    @can('manage partners')
    <a class="nav-item {{ str_starts_with($r, 'admin.partners') ? 'is-active' : '' }}" href="{{ route('admin.partners.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-medal.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Partners</span>
    </a>
    @endcan

    @can('manage navigation')
    <a class="nav-item {{ str_starts_with($r, 'admin.navigation') ? 'is-active' : '' }}" href="{{ route('admin.navigation.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-list.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Navigation</span>
    </a>
    @endcan
    @can('manage content lists')
    <a class="nav-item {{ str_starts_with($r, 'admin.content-lists') ? 'is-active' : '' }}" href="{{ route('admin.content-lists.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-clipboard.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Content Lists</span>
    </a>
    @endcan
    <div class="sidebar__label">Site</div>
    @can('manage settings')
    <a class="nav-item {{ str_starts_with($r, 'admin.settings') ? 'is-active' : '' }}" href="{{ route('admin.settings.edit') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-gear.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Settings</span>
    </a>
    <a class="nav-item {{ str_starts_with($r, 'admin.seo') ? 'is-active' : '' }}" href="{{ route('admin.seo.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-magnifier.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">SEO &amp; AI Discovery</span>
    </a>
    <a class="nav-item {{ str_starts_with($r, 'admin.sitemap') ? 'is-active' : '' }}" href="{{ route('admin.sitemap.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-target.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Sitemap</span>
    </a>
    @endcan
    @role('owner')
    <div class="sidebar__label">System</div>
    <a class="nav-item {{ str_starts_with($r, 'admin.admins') ? 'is-active' : '' }}" href="{{ route('admin.admins.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-shield.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Admins</span>
    </a>
    <a class="nav-item {{ str_starts_with($r, 'admin.roles') ? 'is-active' : '' }}" href="{{ route('admin.roles.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-sliders.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">Roles</span>
    </a>
    @endrole

    <a class="nav-item" href="{{ url('/') }}" target="_blank">
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-eye.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">View Website</span>
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
        <button type="submit" class="btn btn--ghost btn--sm sidebar__signout" style="width:100%;justify-content:center;margin-top:10px;" title="Sign out">
          <img src="{{ asset('assets/images/3d/3d-power.png') }}" alt="" width="16" height="16"/>
          <span class="nav-item__text">Sign out</span>
        </button>
      </form>
    </div>
  </aside>

  {{-- ============ Main ============ --}}
  <div class="main">
    <div class="topbar">
      <button class="menu-btn" type="button" onclick="empetixToggleSidebar()" aria-label="Toggle sidebar" aria-expanded="true" aria-controls="sidebar" id="sidebar-toggle">
        <img src="{{ asset('assets/images/3d/3d-menu.png') }}" alt="" width="20" height="20"/>
      </button>
      <div class="topbar__titles">
        @if($__env->yieldContent('page_header') !== 'hide')
          <span class="crumb">@yield('crumb')</span>
          <h1>@yield('title', 'Dashboard')</h1>
        @endif
      </div>

      {{-- Quick-jump search: filters the admin sections client-side (opens with / or Cmd+K). --}}
      <button class="topbar-search" id="quicknav-open" type="button">
        <img src="{{ asset('assets/images/3d/3d-magnifier.png') }}" alt="" width="16" height="16"/>
        <span>Search sections…</span>
        <kbd>⌘K</kbd>
      </button>

      <div class="topbar__actions">
        @yield('actions')
        {{-- Unread-briefs bell: the badge is the real unread count. --}}
        <button class="icon-btn" type="button" data-theme-toggle onclick="empetixToggleTheme()" aria-label="Toggle dark mode" aria-pressed="false">
          <img class="icon-moon" src="{{ asset('assets/images/3d/3d-moon.png') }}" alt="" width="20" height="20"/>
          <img class="icon-sun" src="{{ asset('assets/images/3d/3d-sun.png') }}" alt="" width="20" height="20" style="display:none"/>
        </button>
        <a class="icon-btn" href="{{ route('admin.inbox.index') }}" aria-label="Inbox, {{ $unread }} unread">
          <img src="{{ asset('assets/images/3d/3d-bell.png') }}" alt="" width="20" height="20"/>
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
          <li data-name="dashboard overview"><a href="{{ route('admin.dashboard') }}" wire:navigate>Dashboard</a></li>
          <li data-name="inbox briefs messages contact"><a href="{{ route('admin.inbox.index') }}" wire:navigate>Inbox</a></li>
          <li data-name="pages content sections"><a href="{{ route('admin.pages.index') }}" wire:navigate>Pages &amp; Content</a></li>
          <li data-name="hero slides homepage"><a href="{{ route('admin.hero-slides.index') }}" wire:navigate>Hero Slides</a></li>
          <li data-name="services"><a href="{{ route('admin.services.index') }}" wire:navigate>Services</a></li>
          <li data-name="service pages editions landing"><a href="{{ route('admin.service-pages.index') }}" wire:navigate>Service Pages</a></li>
          <li data-name="industries"><a href="{{ route('admin.industries.index') }}" wire:navigate>Industries</a></li>
          <li data-name="projects work portfolio"><a href="{{ route('admin.projects.index') }}" wire:navigate>Projects</a></li>
          <li data-name="team founders members"><a href="{{ route('admin.members.index') }}" wire:navigate>Team &amp; Founders</a></li>
          <li data-name="careers jobs roles hiring"><a href="{{ route('admin.jobs.index') }}" wire:navigate>Careers</a></li>
          <li data-name="applications candidates cvs resumes hiring"><a href="{{ route('admin.applications.index') }}" wire:navigate>Applications</a></li>
          <li data-name="blog media posts news"><a href="{{ route('admin.posts.index') }}" wire:navigate>Blog &amp; Media</a></li>
          <li data-name="partners clients logos"><a href="{{ route('admin.partners.index') }}" wire:navigate>Partners</a></li>
          <li data-name="navigation menu mega"><a href="{{ route('admin.navigation.index') }}" wire:navigate>Navigation</a></li>
          <li data-name="content lists about careers timeline"><a href="{{ route('admin.content-lists.index') }}" wire:navigate>Content Lists</a></li>
          <li data-name="settings site social contact"><a href="{{ route('admin.settings.edit') }}" wire:navigate>Settings</a></li>
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
        if (!window.__empetixQuicknavKeys) {
          window.__empetixQuicknavKeys = true;
          document.addEventListener('keydown', function (e) {
            var liveOverlay = document.getElementById('quicknav');
            if (!liveOverlay) return;
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
              e.preventDefault();
              liveOverlay.hidden = false;
              var liveInput = document.getElementById('quicknav-input');
              liveInput.value = '';
              liveInput.dispatchEvent(new Event('input'));
              liveInput.focus();
            }
            if (e.key === 'Escape') liveOverlay.hidden = true;
          });
        }
      })();
    </script>

    <div class="content">
      @if(session('success'))
        <div class="flash">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
          {{ session('success') }}
        </div>
        <script>
          (function() {
            var fireToast = function() {
              if (window.empetixToast) {
                window.empetixToast(@json(session('success')), 'success');
              }
            };
            if (document.readyState === 'loading') {
              window.addEventListener('DOMContentLoaded', fireToast);
            } else {
              fireToast();
            }
          })();
        </script>
      @endif

      @yield('content')
    </div>
  </div>
</div>

{{-- DataTables stack: jQuery + DataTables.net (MIT, vendored locally) load
     on every admin page; each listing pushes its init script here. --}}
<script src="{{ asset('assets/admin/vendor/jquery.min.js') }}" data-navigate-once></script>
<script src="{{ asset('assets/admin/vendor/jquery.dataTables.min.js') }}" data-navigate-once></script>
<script src="{{ asset('assets/admin/vendor/sweetalert2.all.min.js') }}" data-navigate-once></script>
<script data-navigate-once>
  (function() {
    // 1. VIP Glassmorphic SweetAlert Confirm Dialog
    window.empetixConfirm = function(options) {
      options = options || {};
      var title = options.title || 'Delete Confirmation';
      var text = options.text || 'Are you sure you want to delete this? This action cannot be undone.';
      var confirmText = options.confirmText || 'Yes, Delete';
      var cancelText = options.cancelText || 'Cancel';
      var icon = options.icon || "{{ asset('assets/images/3d/3d-notification.png') }}";

      return Swal.fire({
        html: `
          <div class="empetix-swal-hero">
            <div class="empetix-swal-icon-halo">
              <img src="${icon}" class="empetix-swal-icon-3d" alt="" width="56" height="56" />
            </div>
            <h2 class="empetix-swal-heading">${title}</h2>
            <p class="empetix-swal-desc">${text}</p>
          </div>
        `,
        showCancelButton: true,
        confirmButtonText: `
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
          <span>${confirmText}</span>
        `,
        cancelButtonText: `<span>${cancelText}</span>`,
        buttonsStyling: false,
        reverseButtons: true,
        focusCancel: true,
        backdrop: 'rgba(10, 14, 22, 0.65)',
        customClass: {
          popup: 'empetix-swal-modal',
          confirmButton: 'btn btn--danger btn--swal-confirm',
          cancelButton: 'btn btn--ghost btn--swal-cancel',
          actions: 'empetix-swal-actions'
        },
        showClass: {
          popup: 'empetix-swal-anim-in'
        },
        hideClass: {
          popup: 'empetix-swal-anim-out'
        }
      });
    };

    // 2. VIP Toast Notification
    window.empetixToast = function(message, type) {
      type = type || 'success';
      var icon = type === 'success' 
        ? "{{ asset('assets/images/3d/3d-checkmark.png') }}"
        : "{{ asset('assets/images/3d/3d-notification.png') }}";

      var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4500,
        timerProgressBar: true,
        backdrop: false,
        customClass: {
          popup: 'empetix-swal-toast'
        }
      });

      Toast.fire({
        html: `
          <div class="empetix-toast-content">
            <img src="${icon}" width="24" height="24" alt="" />
            <span class="empetix-toast-msg">${message}</span>
          </div>
        `
      });
    };

    // 3. Universal Capture-Phase Interceptor
    // Catches clicks on ANY delete button or submission of delete forms before native confirm() can fire!
    document.addEventListener('click', function(e) {
      var btn = e.target.closest('button[type="submit"], input[type="submit"]');
      if (!btn) return;

      var form = btn.closest('form');
      if (!form) return;

      var methodInput = form.querySelector('input[name="_method"]');
      var isDeleteMethod = methodInput && methodInput.value.toUpperCase() === 'DELETE';
      var isDangerBtn = btn.classList.contains('btn--danger');
      var hasDataConfirm = form.hasAttribute('data-confirm');
      var hasConfirmOnSubmit = form.getAttribute('onsubmit') && form.getAttribute('onsubmit').includes('confirm');

      if (!isDeleteMethod && !isDangerBtn && !hasDataConfirm && !hasConfirmOnSubmit) {
        return;
      }

      // If this submission was already approved by SweetAlert, let it pass!
      if (form.__empetixConfirmed) {
        form.__empetixConfirmed = false;
        return;
      }

      // Prevent the form submission and native confirm()
      e.preventDefault();
      e.stopImmediatePropagation();

      // Temporarily strip onsubmit so no native popup appears
      if (form.getAttribute('onsubmit')) {
        form.__savedOnSubmit = form.getAttribute('onsubmit');
        form.removeAttribute('onsubmit');
      }

      var confirmMsg = form.getAttribute('data-confirm') || 'Are you sure you want to delete this record?';
      var confirmTitle = form.getAttribute('data-confirm-title') || 'Confirm Deletion';

      window.empetixConfirm({
        title: confirmTitle,
        text: confirmMsg,
        confirmText: 'Yes, Delete',
        cancelText: 'Cancel'
      }).then(function(result) {
        if (result.isConfirmed) {
          form.__empetixConfirmed = true;
          form.submit();
        } else if (form.__savedOnSubmit) {
          form.setAttribute('onsubmit', form.__savedOnSubmit);
        }
      });
    }, true); // Capture phase is essential!
  })();
</script>
@stack('datatables')
{{-- Livewire drives wire:navigate SPA swaps across the admin shell. --}}
@livewireScripts
</body>
</html>
