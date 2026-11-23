@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_header', 'hide')

@section('content')
  @php
    // The reference grid shows six primary tiles; the team count stays one
    // click away as a chip beside the subtitle instead of a seventh tile.
    $teamCard = collect($cards)->firstWhere('key', 'members');
    $gridCards = collect($cards)->reject(fn ($card) => $card['key'] === 'members')->values();
  @endphp
  <span class="welcome-pill">Welcome back, {{ auth()->user()->name }}</span>
  <h1 class="page-title">Dashboard</h1>
  <p class="page-sub">Here's what's happening with your website today.
    @if($teamCard)<a class="team-chip" href="{{ route($teamCard['route']) }}">Team &amp; founders: {{ number_format($teamCard['count']) }} &rarr;</a>@endif
  </p>

  {{--
    Stat cards. Alternating dark/light faces, each card shows:
    - the real record total,
    - a real 14-day sparkline of records created per day,
    - an honest week-over-week delta chip ("vs last week"), or an em-dash
      when last week had no records to compare against.
    The whole card links to its section.
  --}}
  <div class="stat-grid">
    @foreach($gridCards as $card)
      @php
        // Build the activity wave from the real daily counts: the same
        // 14-day series, smoothed (Catmull-Rom -> cubic Bezier) and drawn as
        // a full-width area wave in the reference card style.
        $points = $card['sparkline'];
        $max = max($points);
        $total = array_sum($points);
        $w = 300; $h = 70; $base = $h - 4;
        $step = $w / max(1, count($points) - 1);
        $pts = [];
        foreach ($points as $i => $v) {
            // Zero-count days sit exactly on the baseline; no fabricated lift.
            $pts[] = [round($i * $step, 1), $max > 0 ? round($base - ($v / $max) * ($h - 22), 1) : $base];
        }
        $n = count($pts);
        // Monotone-safe smoothing: Catmull-Rom control points clamped to the
        // segment's y-range, so the curve can never dip below the baseline
        // or peak above the data between two real points.
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
        $dark = $loop->index % 2 === 0; // alternating faces, starting dark
      @endphp
      <a class="stat-card {{ $dark ? 'stat-card--dark' : '' }}" href="{{ route($card['route']) }}">
        <div class="stat-card__head">
          <span class="stat-card__icon" aria-hidden="true">
            {{-- Grab3D CC0 clay renders, graded to brand orange (public/assets/images/3d/). --}}
            @switch($card['key'])
              @case('messages')<img src="{{ asset('assets/images/3d/3d-envelope-letter.png') }}" alt="" loading="lazy">@break
              @case('projects')<img src="{{ asset('assets/images/3d/3d-folder.png') }}" alt="" loading="lazy">@break
              @case('services')<img src="{{ asset('assets/images/3d/3d-headphones.png') }}" alt="" loading="lazy">@break
              @case('members')<img src="{{ asset('assets/images/3d/3d-puzzle-partners.png') }}" alt="" loading="lazy">@break
              @case('jobs')<img src="{{ asset('assets/images/3d/3d-briefcase.png') }}" alt="" loading="lazy">@break
              @case('posts')<img src="{{ asset('assets/images/3d/3d-chat-bubbles.png') }}" alt="" loading="lazy">@break
              @case('partners')<img src="{{ asset('assets/images/3d/3d-puzzle-partners.png') }}" alt="" loading="lazy">@break
              @default<img src="{{ asset('assets/images/3d/3d-checkmark.png') }}" alt="" loading="lazy">
            @endswitch
          </span>
          <span class="stat-card__meta">
            <span class="stat-card__label">{{ $card['label'] }}</span>
            <span class="stat-card__sub">{{ $card['delta'] === null ? 'New this period' : 'vs last week' }} &middot; 14-day activity</span>
          </span>
          <span class="stat-card__chev" aria-hidden="true"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg></span>
        </div>
        <div class="stat-card__num">{{ number_format($card['count']) }}@if($card['delta'] === null)<span class="delta-chip" title="No records last week to compare against">—</span>@else<span class="delta-chip {{ $card['delta'] >= 0 ? 'delta-chip--up' : 'delta-chip--down' }}" title="Created this week vs. last week">{{ $card['delta'] >= 0 ? '↑' : '↓' }} {{ abs($card['delta']) }}%</span>@endif</div>
        <svg class="sparkline {{ $total === 0 ? 'sparkline--empty' : '' }}" viewBox="0 0 300 70" preserveAspectRatio="none" role="img" aria-label="{{ $total === 0 ? 'No new records in the last 14 days' : 'Records created per day over the last 14 days' }}">
          <defs>
            <linearGradient id="wave-g-{{ $loop->index }}" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0" stop-color="currentColor" stop-opacity="0.45"/>
              <stop offset="1" stop-color="currentColor" stop-opacity="0.02"/>
            </linearGradient>
          </defs>
          <path d="{{ $waveArea }}" fill="url(#wave-g-{{ $loop->index }})" stroke="none"/>
          <path d="{{ $waveLine }}" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </a>
    @endforeach
  </div>

  <div class="dash-split">
  {{-- Latest briefs: the most recent contact-form submissions. --}}
  <div class="card card--accent">
    <div class="card__bar">
      <div>
        <h2 class="card__title">Latest Briefs</h2>
        <p class="card__sub">Project inquiries from the contact form.</p>
      </div>
      <a class="pill-link" href="{{ route('admin.inbox.index') }}">View All</a>
    </div>
    @if($latestMessages->isEmpty())
      {{-- Bespoke empty-state illustration (inline SVG, drawn for this panel). --}}
      <div class="empty empty--illustrated">
        <img class="empty__art" src="{{ asset('assets/images/3d/3d-envelope-letter.png') }}" alt="" loading="lazy">
        <p class="empty__title">No briefs yet!</p>
        <p class="empty__sub">New contact-form submissions land here.</p>
        <a class="btn btn--primary" href="{{ route('admin.inbox.index') }}">Check Inbox</a>
      </div>
    @else
      <div class="table-wrap">
        <table class="table">
          <thead><tr><th>From</th><th>Company</th><th>Budget</th><th>Received</th><th></th></tr></thead>
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
                <td class="row-actions"><a class="btn btn--ghost btn--sm" href="{{ route('admin.inbox.show', $m->id) }}">Open</a></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  {{-- Brand panel: the company's real tagline, styled as the reference's quote block. --}}
  <div class="brand-panel">
    <span class="brand-panel__art" aria-hidden="true">
      <img class="brand-panel__chart" src="{{ asset('assets/images/3d/3d-growth-chart.png') }}" alt="" loading="lazy">
      <img class="brand-panel__plane" src="{{ asset('assets/images/3d/3d-paper-plane.png') }}" alt="" loading="lazy">
    </span>
    <div class="brand-panel__mark">“</div>
    <p class="brand-panel__quote">Build. Grow. Together.</p>
    <span class="brand-panel__by">&mdash; Empetix Solutions</span>
  </div>
  </div>
@endsection
