@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_header', 'hide')

@section('content')
  @php
    $cardsCollection = collect($cards);
    // Keys in exact order of the executive reference mockup:
    // 1. messages (Unread Briefs) - dark
    // 2. projects (Projects)      - light
    // 3. services (Services)      - dark
    // 4. jobs (Open Roles)        - light
    // 5. posts (Blog Posts)       - dark
    // 6. partners (Partners)      - light
    $cardOrder = ['messages', 'projects', 'services', 'jobs', 'posts', 'partners'];
    $gridCards = collect($cardOrder)->map(function ($key) use ($cardsCollection) {
        return $cardsCollection->firstWhere('key', $key);
    })->filter()->values();

    // Mockup specs for faithful executive match with undulating multi-crest sine waves
    $specs = [
        'messages' => [
            'title' => 'Unread Briefs',
            'sub' => fn($c) => ($c['count'] ?? 0) . ' new',
            'delta_text' => '↘ 0%',
            'delta_type' => 'flat',
            'wave_line' => 'M 0,56 C 40,56 70,64 105,50 C 145,36 175,32 210,44 C 245,56 275,32 300,28',
            'wave_area' => 'M 0,56 C 40,56 70,64 105,50 C 145,36 175,32 210,44 C 245,56 275,32 300,28 L 300,70 L 0,70 Z',
        ],
        'projects' => [
            'title' => 'Projects',
            'sub' => fn($c) => (($c['count'] ?? 0) > 0 ? min($c['count'], 3) : 0) . ' active',
            'delta_text' => '↑ 12%',
            'delta_type' => 'up',
            'wave_line' => 'M 0,62 C 45,62 80,46 120,52 C 160,58 195,42 235,46 C 265,50 285,32 300,34',
            'wave_area' => 'M 0,62 C 45,62 80,46 120,52 C 160,58 195,42 235,46 C 265,50 285,32 300,34 L 300,70 L 0,70 Z',
        ],
        'services' => [
            'title' => 'Services',
            'sub' => fn($c) => ($c['count'] ?? 0) . ' live',
            'delta_text' => '↑ 8%',
            'delta_type' => 'up',
            'wave_line' => 'M 0,54 C 40,54 75,62 115,48 C 155,34 190,42 230,30 C 260,20 280,18 300,22',
            'wave_area' => 'M 0,54 C 40,54 75,62 115,48 C 155,34 190,42 230,30 C 260,20 280,18 300,22 L 300,70 L 0,70 Z',
        ],
        'jobs' => [
            'title' => 'Open Roles',
            'sub' => fn($c) => 'Hiring',
            'delta_text' => '↑ 20%',
            'delta_type' => 'up',
            'wave_line' => 'M 0,60 C 45,60 85,42 125,48 C 165,54 205,36 245,40 C 275,44 290,28 300,30',
            'wave_area' => 'M 0,60 C 45,60 85,42 125,48 C 165,54 205,36 245,40 C 275,44 290,28 300,30 L 300,70 L 0,70 Z',
        ],
        'posts' => [
            'title' => 'Blog Posts',
            'sub' => fn($c) => 'Published',
            'delta_text' => '→ 0%',
            'delta_type' => 'flat',
            'wave_line' => 'M 0,54 C 40,54 75,40 110,46 C 150,54 185,34 225,38 C 260,42 285,22 300,24',
            'wave_area' => 'M 0,54 C 40,54 75,40 110,46 C 150,54 185,34 225,38 C 260,42 285,22 300,24 L 300,70 L 0,70 Z',
        ],
        'partners' => [
            'title' => 'Partners',
            'sub' => fn($c) => 'Global',
            'delta_text' => '↑ 14%',
            'delta_type' => 'up',
            'wave_line' => 'M 0,60 C 50,60 90,44 135,48 C 180,52 215,34 255,38 C 275,40 290,28 300,30',
            'wave_area' => 'M 0,60 C 50,60 90,44 135,48 C 180,52 215,34 255,38 C 275,40 290,28 300,30 L 300,70 L 0,70 Z',
        ],
    ];
  @endphp

  <span class="welcome-pill">Welcome back! 👋</span>
  <h1 class="page-title">Dashboard</h1>
  <p class="page-sub">Here's what's happening with your website today.</p>

  {{-- 6 Executive Stat Cards (All Black / Dark) --}}
  <div class="stat-grid">
    @foreach($gridCards as $card)
      @php
        $key = $card['key'];
        $spec = $specs[$key] ?? [
            'title' => $card['label'],
            'sub' => fn($c) => 'Active',
            'delta_text' => '→ 0%',
            'delta_type' => 'flat',
            'wave_line' => 'M 0,55 C 60,55 100,40 160,45 C 220,50 260,25 300,28',
            'wave_area' => 'M 0,55 C 60,55 100,40 160,45 C 220,50 260,25 300,28 L 300,70 L 0,70 Z',
        ];
        $dark = true;

        // Wave: use smoothed points if rich multi-day activity exists, else use reference radiant sine wave
        $points = $card['sparkline'] ?? [];
        $max = !empty($points) ? max($points) : 0;
        $total = !empty($points) ? array_sum($points) : 0;
        $nonZeroDays = count(array_filter($points, fn($v) => $v > 0));

        if ($max > 0 && $total > 0 && $nonZeroDays >= 3) {
            $w = 300; $h = 70; $base = $h - 4;
            $step = $w / max(1, count($points) - 1);
            $pts = [];
            foreach ($points as $i => $v) {
                $pts[] = [round($i * $step, 1), round($base - ($v / $max) * ($h - 22), 1)];
            }
            $n = count($pts);
            $waveLine = 'M ' . $pts[0][0] . ',' . $pts[0][1];
            for ($i = 0; $i < $n - 1; $i++) {
                $p0 = $pts[max(0, $i - 1)]; $p1 = $pts[$i];
                $p2 = $pts[$i + 1];         $p3 = $pts[min($n - 1, $i + 2)];
                $c1x = round($p1[0] + ($p2[0] - $p0[0]) / 6, 1);
                $c1y = min(max(round($p1[1] + ($p2[1] - $p0[1]) / 6, 1), min($p1[1], $p2[1])), max($p1[1], $p2[1]));
                $c2x = round($p2[0] - ($p3[0] - $p1[0]) / 6, 1);
                $c2y = min(max(round($p2[1] - ($p3[1] - $p1[1]) / 6, 1), min($p1[1], $p2[1])), max($p1[1], $p2[1]));
                $waveLine .= " C $c1x,$c1y $c2x,$c2y {$p2[0]},{$p2[1]}";
            }
            $waveArea = $waveLine . " L $w,$base L 0,$base Z";
        } else {
            $waveLine = $spec['wave_line'];
            $waveArea = $spec['wave_area'];
        }

        // Delta pill
        if ($card['delta'] !== null) {
            $deltaVal = abs(round($card['delta']));
            $deltaText = ($card['delta'] >= 0 ? '↑ ' : '↓ ') . $deltaVal . '%';
            $deltaType = $card['delta'] > 0 ? 'up' : ($card['delta'] < 0 ? 'down' : 'flat');
        } else {
            $deltaText = $spec['delta_text'];
            $deltaType = $spec['delta_type'];
        }
      @endphp

      <a class="stat-card {{ $dark ? 'stat-card--dark' : 'stat-card--light' }}" href="{{ route($card['route']) }}">
        <div class="stat-card__head">
          <span class="stat-card__icon" aria-hidden="true">
            @switch($key)
              @case('messages')
                <img src="{{ asset('assets/images/3d/3d-envelope-letter.png') }}" alt="" loading="lazy">
                @break
              @case('projects')
                <img src="{{ asset('assets/images/3d/3d-folder.png') }}" alt="" loading="lazy">
                @break
              @case('services')
                <img src="{{ asset('assets/images/3d/3d-headphones.png') }}" alt="" loading="lazy">
                @break
              @case('jobs')
                <img src="{{ asset('assets/images/3d/3d-briefcase.png') }}" alt="" loading="lazy">
                @break
              @case('posts')
                <img src="{{ asset('assets/images/3d/3d-chat-bubbles.png') }}" alt="" loading="lazy">
                @break
              @case('partners')
                <img src="{{ asset('assets/images/3d/3d-puzzle-partners.png') }}" alt="" loading="lazy">
                @break
              @default
                <img src="{{ asset('assets/images/3d/3d-checkmark.png') }}" alt="" loading="lazy">
            @endswitch
          </span>
          <span class="stat-card__meta">
            <span class="stat-card__label">{{ $spec['title'] }}</span>
            <span class="stat-card__sub">{{ ($spec['sub'])($card) }}</span>
          </span>
          <span class="stat-card__chev" aria-hidden="true">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
          </span>
        </div>

        <div class="stat-card__body">
          <span class="stat-card__num">{{ number_format($card['count']) }}</span>
          <span class="delta-chip delta-chip--{{ $deltaType }}">{{ $deltaText }}</span>
        </div>

        <svg class="sparkline" viewBox="0 0 300 70" preserveAspectRatio="none" role="img" aria-hidden="true">
          <defs>
            <linearGradient id="wave-grad-{{ $loop->index }}" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#FF5722" stop-opacity="{{ $dark ? '0.42' : '0.26' }}"/>
              <stop offset="100%" stop-color="#FF7A50" stop-opacity="0.0"/>
            </linearGradient>
            <filter id="wave-glow-{{ $loop->index }}" x="-10%" y="-30%" width="120%" height="160%">
              <feDropShadow dx="0" dy="2" stdDeviation="3.5" flood-color="#FF5722" flood-opacity="{{ $dark ? '0.55' : '0.35' }}"/>
            </filter>
          </defs>
          <path d="{{ $waveArea }}" fill="url(#wave-grad-{{ $loop->index }})" stroke="none"/>
          <path d="{{ $waveLine }}" fill="none" stroke="#FF5722" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" filter="url(#wave-glow-{{ $loop->index }})"/>
        </svg>
      </a>
    @endforeach
  </div>

  {{-- Bottom Row: Latest Briefs + Executive Brand Quote Panel --}}
  <div class="dash-split">
    {{-- Latest briefs: the most recent contact-form submissions --}}
    <div class="card card--accent">
      <div class="card__bar">
        <div>
          <h2 class="card__title">Latest Briefs</h2>
          <p class="card__sub">Project inquiries from the contact form.</p>
        </div>
        <a class="pill-link" href="{{ route('admin.inbox.index') }}">View All</a>
      </div>
      @if($latestMessages->isEmpty())
        {{-- 3D Envelope illustration, compact and balanced --}}
        <div class="empty empty--illustrated">
          <img class="empty__art" src="{{ asset('assets/images/3d/3d-envelope-letter.png') }}" alt="" loading="lazy">
          <p class="empty__title">No briefs yet!</p>
          <p class="empty__sub">New contact-form submissions land here.</p>
          <a class="btn btn--primary btn--briefs-inbox" href="{{ route('admin.inbox.index') }}">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            Check Inbox
          </a>
        </div>
      @else
        <div class="table-wrap">
          <table class="table">
            <thead><tr><th>From</th><th>Company</th><th>Budget</th><th>Received</th><th class="row-actions">Actions</th></tr></thead>
            <tbody>
              @foreach($latestMessages as $m)
                <tr class="message-row {{ $m->read_at ? '' : 'unread' }}">
                  <td>
                    <div class="cell-title">{{ $m->name }}</div>
                    <div class="cell-sub">{{ $m->email }}</div>
                  </td>
                  <td>{{ $m->company ?? '—' }}</td>
                  <td>{{ $m->budget_tier ?? '—' }}</td>
                  <td>{{ $m->created_at->diffForHumans() }}</td>
                  <td class="row-actions">
                    <div class="dt-actions">
                      <a class="btn btn--ghost btn--sm dt-btn" href="{{ route('admin.inbox.show', $m->id) }}">Open</a>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>

    {{-- Brand panel: executive 3D illustration and brand quote --}}
    <div class="brand-panel">
      {{-- Floating 3D Analytics Card + Origami Paper Plane with Contrail --}}
      <div class="brand-panel__art" aria-hidden="true">
        <svg class="brand-panel__svg" viewBox="0 0 280 155" fill="none" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <linearGradient id="bp-card-bg" x1="0" y1="0" x2="1" y2="1">
              <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.98"/>
              <stop offset="100%" stop-color="#FFF7F3" stop-opacity="0.94"/>
            </linearGradient>
            <linearGradient id="bp-orange-bar" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#FF7547"/>
              <stop offset="100%" stop-color="#FF4D1C"/>
            </linearGradient>
            <linearGradient id="bp-trail" x1="0" y1="0" x2="1" y2="0">
              <stop offset="0%" stop-color="#FFB39A" stop-opacity="0.2"/>
              <stop offset="60%" stop-color="#FF7A50" stop-opacity="0.8"/>
              <stop offset="100%" stop-color="#FF4D1C"/>
            </linearGradient>
            <filter id="bp-shadow" x="-20%" y="-20%" width="150%" height="150%">
              <feDropShadow dx="0" dy="8" stdDeviation="12" flood-color="#FF4D1C" flood-opacity="0.12"/>
            </filter>
            <filter id="bp-plane-shadow" x="-30%" y="-30%" width="170%" height="170%">
              <feDropShadow dx="0" dy="6" stdDeviation="8" flood-color="#D63806" flood-opacity="0.32"/>
            </filter>
          </defs>

          {{-- Ambient warm backdrop orbs --}}
          <circle cx="215" cy="45" r="50" fill="#FFEFE8" opacity="0.75"/>
          <circle cx="85" cy="85" r="45" fill="#FFF4EE" opacity="0.65"/>

          {{-- Floating Glassmorphic 3D Card --}}
          <g class="bp-floating-card" transform="translate(18, 14) rotate(-3)">
            <rect x="0" y="0" width="134" height="100" rx="14" fill="url(#bp-card-bg)" stroke="rgba(255, 91, 38, 0.16)" stroke-width="1.5" filter="url(#bp-shadow)"/>
            
            <circle cx="16" cy="16" r="4" fill="#FF4D1C"/>
            <rect x="25" y="13.5" width="40" height="5.5" rx="2.5" fill="#FFE2D6"/>
            <rect x="96" y="11.5" width="24" height="10" rx="5" fill="#E8F8F0"/>
            <text x="99" y="19" fill="#10B981" font-family="system-ui, sans-serif" font-size="7.5" font-weight="800">↗ 34%</text>

            <line x1="12" y1="28" x2="122" y2="28" stroke="#F6EEE7" stroke-width="1.2"/>

            {{-- 4 Growth Bars --}}
            <rect x="18" y="64" width="13" height="22" rx="3.5" fill="#FFE6DC"/>
            <rect x="38" y="52" width="13" height="34" rx="3.5" fill="#FFBAA2"/>
            <rect x="58" y="42" width="13" height="44" rx="3.5" fill="#FF8E68"/>
            <rect x="78" y="30" width="13" height="56" rx="3.5" fill="url(#bp-orange-bar)"/>

            <path d="M24.5,64 L44.5,52 L64.5,42 L84.5,30" stroke="#FF4D1C" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="84.5" cy="30" r="3.2" fill="#FFFFFF" stroke="#FF4D1C" stroke-width="1.8"/>
          </g>

          {{-- Dotted Flight Contrail --}}
          <path class="bp-flight-trail" d="M112,68 C148,78 180,56 198,38 C207,28 216,24 226,22" stroke="url(#bp-trail)" stroke-width="2.2" stroke-linecap="round" stroke-dasharray="3.5 5"/>

          {{-- 3D Origami Airplane --}}
          <g class="bp-floating-plane" transform="translate(216, 14) rotate(-16)" filter="url(#bp-plane-shadow)">
            <polygon points="0,22 42,0 25,35" fill="#FF5320"/>
            <polygon points="25,35 42,0 12,25" fill="#FF784E"/>
            <polygon points="0,22 12,25 25,35" fill="#D93907"/>
            <line x1="12" y1="25" x2="42" y2="0" stroke="#FFA385" stroke-width="0.8"/>
          </g>
        </svg>
      </div>

      {{-- Quote Section --}}
      <div class="brand-panel__content">
        <div class="brand-panel__mark-wrap">
          <span class="brand-panel__mark">“</span>
        </div>
        <h3 class="brand-panel__quote">Build. Grow. Together.</h3>
        <div class="brand-panel__by-wrap">
          <span class="brand-panel__dash" aria-hidden="true"></span>
          <span class="brand-panel__by">Empetix Solutions</span>
        </div>
      </div>
    </div>
  </div>
@endsection