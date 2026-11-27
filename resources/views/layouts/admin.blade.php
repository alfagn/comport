<!DOCTYPE html>
{{-- Empetix Command — the CMS dashboard shell. --}}
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <meta name="robots" content="noindex, nofollow"/>
  <title>@yield('title', 'Dashboard') — Empetix Command</title>
  <link rel="icon" href="{{ brand_favicon() }}"/>
  <link rel="preload" as="font" type="font/woff2" href="{{ asset('assets/fonts/archivo-800.woff2') }}" crossorigin/>
  <link rel="preload" as="font" type="font/woff2" href="{{ asset('assets/fonts/public-sans-400.woff2') }}" crossorigin/>
  <link rel="stylesheet" href="{{ asset('assets/css/base/typography.css') }}"/>
  <link rel="stylesheet" href="{{ asset('assets/admin/admin.css') }}?v={{ file_exists(public_path('assets/admin/admin.css')) ? filemtime(public_path('assets/admin/admin.css')) : time() }}"/>
  <link rel="stylesheet" href="{{ asset('assets/admin/assistant.css') }}?v={{ file_exists(public_path('assets/admin/assistant.css')) ? filemtime(public_path('assets/admin/assistant.css')) : time() }}"/>
  <link rel="stylesheet" href="{{ asset('assets/admin/whatsapp-inbox.css') }}?v={{ file_exists(public_path('assets/admin/whatsapp-inbox.css')) ? filemtime(public_path('assets/admin/whatsapp-inbox.css')) : time() }}"/>
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
      // Support chats handed off to humans by the public AI concierge.
      $handedOffChats = \Illuminate\Support\Facades\Schema::hasTable('support_chat_sessions')
          ? \App\Models\SupportChatSession::handedOff()->count()
          : 0;
      // Unread inbound WhatsApp messages for the dedicated WhatsApp Inbox item.
      $unreadWhatsapp = (\Illuminate\Support\Facades\Schema::hasTable('support_chat_sessions')
          && \Illuminate\Support\Facades\Schema::hasColumn('support_chat_sessions', 'unread_count'))
          ? (int) \App\Models\SupportChatSession::whatsapp()->sum('unread_count')
          : 0;
      // Unread Website AI Chat messages for the dedicated Website Chat Studio item.
      $unreadWebChat = (\Illuminate\Support\Facades\Schema::hasTable('support_chat_sessions')
          && \Illuminate\Support\Facades\Schema::hasColumn('support_chat_sessions', 'unread_count'))
          ? (int) \App\Models\SupportChatSession::web()->sum('unread_count')
          : 0;
      $adminUser = auth()->user();
      $adminRawPhoto = $adminUser?->member?->photo;
      if (! $adminRawPhoto && $adminUser?->email) {
          $adminRawPhoto = \App\Models\Member::where('email', $adminUser->email)->value('photo');
      }
      if (! $adminRawPhoto && $adminUser) {
          $firstLower = strtolower(explode(' ', trim((string) $adminUser->name))[0] ?? '');
          if (in_array($firstLower, ['mohanad', 'mohand', 'ayman', 'bishoy'], true)) {
              $slugLookup = $firstLower === 'mohand' ? 'mohanad' : $firstLower;
              $adminRawPhoto = \App\Models\Member::where('slug', $slugLookup)->value('photo');
          }
      }
      $adminPhotoUrl = $adminRawPhoto
          ? (str_starts_with((string) $adminRawPhoto, 'uploads/') ? asset('storage/'.$adminRawPhoto) : asset($adminRawPhoto))
          : null;
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
    @can('manage support chats')
    <a class="nav-item {{ str_starts_with($r, 'admin.whatsapp.') ? 'is-active' : '' }}" href="{{ route('admin.whatsapp.index') }}" wire:navigate>
      <span class="nav-ico" style="width:22px;height:22px;border-radius:7px;background:linear-gradient(145deg,#25D366,#128C7E);color:#fff;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 3px 10px rgba(37,211,102,0.32);" aria-hidden="true">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M20.52 3.48A11.82 11.82 0 0 0 12.06 0C5.52 0 .2 5.32.2 11.86c0 2.09.55 4.13 1.59 5.93L0 24l6.38-1.67a11.83 11.83 0 0 0 5.68 1.45h.01c6.54 0 11.86-5.32 11.86-11.86 0-3.17-1.23-6.15-3.41-8.44ZM12.07 21.78h-.01a9.84 9.84 0 0 1-5.02-1.38l-.36-.21-3.79.99 1.01-3.69-.24-.38a9.83 9.83 0 0 1-1.51-5.25c0-5.44 4.43-9.87 9.88-9.87 2.64 0 5.12 1.03 6.98 2.9a9.81 9.81 0 0 1 2.89 6.98c0 5.44-4.43 9.91-9.83 9.91Zm5.41-7.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.65-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35Z"/></svg>
      </span>
      <span class="nav-item__text">WhatsApp</span>
      <span class="badge" data-sidebar-wa-badge style="background:#25D366;color:#052e16;font-weight:800;{{ $unreadWhatsapp > 0 ? '' : 'display:none;' }}">{{ $unreadWhatsapp }}</span>
    </a>
    <a class="nav-item {{ str_starts_with($r, 'admin.website-chat.') ? 'is-active' : '' }}" href="{{ route('admin.website-chat.index') }}" wire:navigate>
      <span class="nav-ico" style="width:22px;height:22px;border-radius:7px;background:linear-gradient(145deg,#4F46E5,#7C3AED);color:#fff;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 3px 10px rgba(79,70,229,0.32);" aria-hidden="true">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 10h.01"/><path d="M12 10h.01"/><path d="M16 10h.01"/></svg>
      </span>
      <span class="nav-item__text">Website Chat</span>
      <span class="badge" data-sidebar-webchat-badge style="background:#25D366;color:#052e16;font-weight:800;{{ $unreadWebChat > 0 ? '' : 'display:none;' }}">{{ $unreadWebChat }}</span>
    </a>
    @endcan
    @can('use ai assistant')
    <a class="nav-item {{ str_starts_with($r, 'admin.assistant') ? 'is-active' : '' }}" href="{{ route('admin.assistant.index') }}" wire:navigate>
      <img class="nav-ico" src="{{ asset('assets/images/3d/3d-rocket.png') }}" alt="" width="22" height="22"/>
      <span class="nav-item__text">AI Copilot</span>
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
      <span class="nav-item__text">System Settings</span>
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
      <a href="{{ route('admin.profile.edit') }}" class="user-chip" wire:navigate title="Manage My Profile &amp; Team Details" style="text-decoration:none; color:inherit; display:flex; cursor:pointer;">
        <div class="user-chip__avatar" style="overflow:hidden; padding:0; flex-shrink:0;">
          @if($adminPhotoUrl)
            <img src="{{ $adminPhotoUrl }}" alt="{{ $adminUser->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:50%; display:block;"/>
          @else
            {{ strtoupper(substr($adminUser->name, 0, 1)) }}
          @endif
        </div>
        <div>
          <div class="user-chip__name">{{ $adminUser->name }}</div>
          <div class="user-chip__role" style="font-size:0.75rem;">Edit Profile &rarr;</div>
        </div>
      </a>
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
        @can('use ai assistant')
          @if(! str_starts_with($r, 'admin.assistant'))
            <button class="topbar-copilot-btn" type="button" onclick="empetixOpenCopilotDrawer()" title="Open AI Copilot (Alt+A)">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 2.5L14.35 8.65L20.5 11L14.35 13.35L12 19.5L9.65 13.35L3.5 11L9.65 8.65L12 2.5Z" fill="#FF5E36" stroke="#FFA082" stroke-width="1.2" stroke-linejoin="round"/>
                <path d="M19 3L19.75 5.25L22 6L19.75 6.75L19 9L18.25 6.75L16 6L18.25 5.25L19 3Z" fill="#FFFFFF"/>
              </svg>
              <span>Copilot</span>
            </button>
          @endif
        @endcan
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
                  style="overflow:hidden; padding:0; display:inline-flex; align-items:center; justify-content:center;"
                  onclick="document.getElementById('avatar-menu').classList.toggle('is-open')">
            @if($adminPhotoUrl)
              <img src="{{ $adminPhotoUrl }}" alt="{{ $adminUser->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:50%; display:block;"/>
            @else
              {{ strtoupper(substr($adminUser->name, 0, 1)) }}
            @endif
          </button>
          <div class="avatar-menu__drop">
            <div class="avatar-menu__head">
              <strong>{{ $adminUser->name }}</strong>
              <span>{{ $adminUser->email }}</span>
            </div>
            <a href="{{ route('admin.profile.edit') }}" wire:navigate style="font-weight:600; color:var(--color-text-accent, #eb5e28); display:flex; align-items:center; gap:6px;">
              <span>👤</span>
              <span>View &amp; Edit Profile</span>
            </a>
            @if($adminUser->member?->slug && $adminUser->member?->kind === 'founder')
              <a href="{{ route('founders.show', $adminUser->member->slug) }}" target="_blank" style="display:flex; align-items:center; gap:6px;">
                <span>🌐</span>
                <span>View Public Profile ↗</span>
              </a>
            @else
              <a href="{{ url('/team') }}" target="_blank" style="display:flex; align-items:center; gap:6px;">
                <span>🌐</span>
                <span>View Public Team ↗</span>
              </a>
            @endif
            <a href="{{ url('/') }}" target="_blank">View Website ↗</a>
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
          @can('use ai assistant')
          <li data-name="ai copilot assistant tasks agent"><a href="{{ route('admin.assistant.index') }}" wire:navigate>AI Copilot</a></li>
          @endcan
          @can('manage support chats')
          <li data-name="whatsapp inbox messages customers"><a href="{{ route('admin.whatsapp.index') }}" wire:navigate>WhatsApp</a></li>
          <li data-name="website chat live visitor ai concierge"><a href="{{ route('admin.website-chat.index') }}" wire:navigate>Website Chat</a></li>
          @endcan
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
          @can('manage settings')
          <li data-name="system settings cards general"><a href="{{ route('admin.settings.edit') }}" wire:navigate>System Settings</a></li>
          <li data-name="system settings whatsapp cloud api meta token"><a href="{{ route('admin.settings.edit', ['section' => 'whatsapp']) }}" wire:navigate>System Settings &mdash; WhatsApp</a></li>
          <li data-name="system settings chatbot ai faq gemini openai"><a href="{{ route('admin.settings.edit', ['section' => 'chatbot']) }}" wire:navigate>System Settings &mdash; Chatbot &amp; AI</a></li>
          @endcan
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
      @if(session('error'))
        <script>
          (function() {
            var fireToast = function() {
              if (window.empetixToast) {
                window.empetixToast(@json(session('error')), 'error');
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

    // 4. Universal One-Click Status Toggle Handler (Tables & Cards)
    document.addEventListener('click', function(e) {
      var toggleBtn = e.target.closest('[data-status-toggle]');
      if (!toggleBtn || toggleBtn.classList.contains('is-busy')) return;

      e.preventDefault();
      var url = toggleBtn.getAttribute('data-toggle-url');
      if (!url) return;

      var wasOn = toggleBtn.classList.contains('is-on');
      var nextOn = !wasOn;
      var labelOn = toggleBtn.getAttribute('data-label-on') || 'Live';
      var labelOff = toggleBtn.getAttribute('data-label-off') || 'Hidden';
      var labelEl = toggleBtn.querySelector('.dt-status-toggle__label');

      function applyState(on, customLabel) {
        toggleBtn.classList.toggle('is-on', on);
        toggleBtn.classList.toggle('is-off', !on);
        toggleBtn.setAttribute('aria-checked', on ? 'true' : 'false');
        if (labelEl) {
          labelEl.textContent = customLabel || (on ? labelOn : labelOff);
        }
      }

      // Optimistic instant flip
      toggleBtn.classList.add('is-busy');
      applyState(nextOn);

      var csrfMeta = document.querySelector('meta[name="csrf-token"]');
      var token = csrfMeta ? csrfMeta.getAttribute('content') : '';

      fetch(url, {
        method: 'PATCH',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': token
        },
        credentials: 'same-origin'
      })
        .then(function(res) {
          if (!res.ok) throw new Error('Status update failed');
          return res.json();
        })
        .then(function(data) {
          applyState(Boolean(data.active), data.label);
          if (window.empetixToast && data.message) {
            window.empetixToast(data.message, 'success');
          }
        })
        .catch(function() {
          applyState(wasOn);
          if (window.empetixToast) {
            window.empetixToast('Could not update status. Please try again.', 'error');
          }
        })
        .finally(function() {
          toggleBtn.classList.remove('is-busy');
        });
    });

    // 5. Universal DataTable Bulk Selection & Floating Bulk Bar
    function ensureBulkBar() {
      var bar = document.getElementById('empetix-bulk-bar');
      if (!bar) {
        bar = document.createElement('div');
        bar.id = 'empetix-bulk-bar';
        bar.className = 'dt-bulk-bar';
        bar.innerHTML = `
          <div class="dt-bulk-bar__badge">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            <span><strong class="dt-bulk-count">0</strong> selected</span>
          </div>
          <div class="dt-bulk-bar__actions">
            <button type="button" class="dt-bulk-clear-btn">Deselect</button>
            <button type="button" class="btn btn--danger btn--sm dt-bulk-delete-btn">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
              <span>Delete Selected (<span class="dt-bulk-count-btn">0</span>)</span>
            </button>
          </div>
        `;
        document.body.appendChild(bar);
      }
      return bar;
    }

    function updateBulkBarForTable(table) {
      if (!table) return;
      var bar = ensureBulkBar();
      var checked = table.querySelectorAll('tbody .dt-row-select:checked');
      var count = checked.length;
      var allRows = table.querySelectorAll('tbody .dt-row-select');
      var master = table.querySelector('thead .dt-select-all');

      if (master) {
        if (count === 0) {
          master.checked = false;
          master.indeterminate = false;
        } else if (count === allRows.length && allRows.length > 0) {
          master.checked = true;
          master.indeterminate = false;
        } else {
          master.checked = false;
          master.indeterminate = true;
        }
      }

      var countEls = document.querySelectorAll('.dt-bulk-count, .dt-bulk-count-btn');
      countEls.forEach(function(el) { el.textContent = count; });

      // Also inject and update top toolbar bulk button in the card
      var wrap = table.closest('.table-wrap--dt') || table.closest('.card');
      if (wrap) {
        var topBtn = wrap.querySelector('.dt-card-bulk-delete');
        if (!topBtn) {
          var lengthBox = wrap.querySelector('.dataTables_length');
          if (lengthBox) {
            topBtn = document.createElement('button');
            topBtn.type = 'button';
            topBtn.hidden = true;
            topBtn.className = 'btn btn--danger btn--sm dt-bulk-delete-btn dt-card-bulk-delete is-hidden';
            topBtn.style.cssText = 'margin-left:14px; vertical-align:middle;';
            topBtn.innerHTML = `
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
              <span>Delete Selected (<span class="dt-bulk-count-btn">0</span>)</span>
            `;
            lengthBox.appendChild(topBtn);
          }
        }
        if (topBtn) {
          var hasSelected = count > 0;
          topBtn.hidden = !hasSelected;
          topBtn.classList.toggle('is-hidden', !hasSelected);
          var topNum = topBtn.querySelector('.dt-bulk-count-btn');
          if (topNum) topNum.textContent = count;
        }
      }

      if (count > 0) {
        bar.classList.add('is-active');
        bar.__activeTable = table;
      } else {
        bar.classList.remove('is-active');
        bar.__activeTable = null;
      }
    }

    function executeBulkDelete(table) {
      if (!table) return;
      var checkedBoxes = table.querySelectorAll('tbody .dt-row-select:checked');
      var ids = Array.from(checkedBoxes).map(function(cb) { return cb.value; });
      if (!ids.length) return;

      var bulkUrl = table.getAttribute('data-bulk-url');
      if (!bulkUrl) {
        var entity = table.getAttribute('data-entity');
        if (!entity) {
          var pathParts = window.location.pathname.split('/').filter(Boolean);
          entity = pathParts[pathParts.length - 1];
        }
        bulkUrl = '/admin/bulk-delete/' + entity;
      }

      var count = ids.length;
      var label = count === 1 ? 'record' : 'records';

      window.empetixConfirm({
        title: 'Delete ' + count + ' ' + label + '?',
        text: 'Are you sure you want to permanently delete these ' + count + ' selected ' + label + '? This action cannot be undone.',
        confirmText: 'Yes, Delete ' + count + ' ' + label,
        cancelText: 'Cancel'
      }).then(function(result) {
        if (!result.isConfirmed) return;

        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        var token = csrfMeta ? csrfMeta.getAttribute('content') : '';

        fetch(bulkUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({ ids: ids })
        })
        .then(function(res) {
          if (!res.ok) throw new Error('Deletion failed');
          return res.json();
        })
        .then(function(data) {
          if (window.empetixToast) {
            window.empetixToast(data.message || (count + ' ' + label + ' deleted successfully.'), 'success');
          }
          var master = table.querySelector('thead .dt-select-all');
          if (master) { master.checked = false; master.indeterminate = false; }
          table.querySelectorAll('tbody .dt-row-select').forEach(function(cb) {
            cb.checked = false;
            var tr = cb.closest('tr');
            if (tr) tr.classList.remove('is-selected');
          });
          var bar = ensureBulkBar();
          bar.classList.remove('is-active');
          bar.__activeTable = null;
          updateBulkBarForTable(table);

          if (window.jQuery) {
            var dt = window.jQuery(table).DataTable();
            if (dt) {
              dt.ajax.reload(function() {
                updateBulkBarForTable(table);
              }, false);
            }
          }
        })
        .catch(function() {
          if (window.empetixToast) {
            window.empetixToast('Could not delete selected records. Please try again.', 'error');
          }
        });
      });
    }

    // Global event listeners for Checkboxes & Bulk Bar
    document.addEventListener('change', function(e) {
      var target = e.target;
      if (target.matches('.dt-select-all')) {
        var table = target.closest('table');
        if (!table) return;
        var isChecked = target.checked;
        var rowChecks = table.querySelectorAll('tbody .dt-row-select');
        rowChecks.forEach(function(cb) {
          cb.checked = isChecked;
          var tr = cb.closest('tr');
          if (tr) tr.classList.toggle('is-selected', isChecked);
        });
        updateBulkBarForTable(table);
      } else if (target.matches('.dt-row-select')) {
        var table = target.closest('table');
        if (!table) return;
        var tr = target.closest('tr');
        if (tr) tr.classList.toggle('is-selected', target.checked);
        updateBulkBarForTable(table);
      }
    });

    // Bulk bar buttons delegation
    document.addEventListener('click', function(e) {
      var clearBtn = e.target.closest('.dt-bulk-clear-btn');
      if (clearBtn) {
        var bar = ensureBulkBar();
        var table = bar.__activeTable || document.querySelector('table.dt-table');
        if (table) {
          var master = table.querySelector('thead .dt-select-all');
          if (master) { master.checked = false; master.indeterminate = false; }
          table.querySelectorAll('tbody .dt-row-select').forEach(function(cb) {
            cb.checked = false;
            var tr = cb.closest('tr');
            if (tr) tr.classList.remove('is-selected');
          });
          updateBulkBarForTable(table);
        }
        return;
      }

      var delBtn = e.target.closest('.dt-bulk-delete-btn');
      if (delBtn) {
        var bar = ensureBulkBar();
        var table = bar.__activeTable || delBtn.closest('.card')?.querySelector('table.dt-table') || document.querySelector('table.dt-table');
        if (table) executeBulkDelete(table);
      }
    });

    // Hook onto DataTables draw events
    if (window.jQuery) {
      window.jQuery(document).off('draw.dt.bulk').on('draw.dt.bulk', 'table', function() {
        updateBulkBarForTable(this);
      });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
      ensureBulkBar();
    });
    document.addEventListener('livewire:navigated', function() {
      ensureBulkBar();
    });
  })();
</script>
@can('use ai assistant')
  @if(! str_starts_with($r, 'admin.assistant'))
    @include('admin._partials.copilot-drawer')
  @endif
@endcan
<script src="{{ asset('assets/admin/assistant.js') }}?v={{ file_exists(public_path('assets/admin/assistant.js')) ? filemtime(public_path('assets/admin/assistant.js')) : time() }}" data-navigate-once></script>
<script src="{{ asset('assets/admin/whatsapp-inbox.js') }}?v={{ file_exists(public_path('assets/admin/whatsapp-inbox.js')) ? filemtime(public_path('assets/admin/whatsapp-inbox.js')) : time() }}" data-navigate-once></script>
<script src="{{ asset('assets/admin/website-chat.js') }}?v={{ file_exists(public_path('assets/admin/website-chat.js')) ? filemtime(public_path('assets/admin/website-chat.js')) : time() }}" data-navigate-once></script>
@stack('datatables')
{{-- Livewire drives wire:navigate SPA swaps across the admin shell. --}}
@livewireScripts
</body>
</html>
