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
        // Build the sparkline polyline from the real daily counts.
        $points = $card['sparkline'];
        $max = max(1, max($points));
        $w = 120; $h = 36;
        $step = $w / max(1, count($points) - 1);
        $coords = [];
        foreach ($points as $i => $v) {
            $coords[] = round($i * $step, 1) . ',' . round($h - ($v / $max) * ($h - 4) - 2, 1);
        }
        $polyline = implode(' ', $coords);
        $dark = $loop->index % 2 === 0; // alternating faces, starting dark
      @endphp
      <a class="stat-card {{ $dark ? 'stat-card--dark' : '' }}" href="{{ route($card['route']) }}">
        <div class="stat-card__head">
          <span class="stat-card__icon" aria-hidden="true">
            @switch($card['key'])
              @case('messages')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 13h4l2 3h6l2-3h4"/><path d="M5 6h14a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1z"/><path d="M4 7l8 5 8-5"/></svg>@break
              @case('projects')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 7h16v13H4z"/><path d="M9 7V4h6v3"/></svg>@break
              @case('services')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 13a8 8 0 0116 0"/><rect x="3" y="13" width="4" height="6" rx="1.5"/><rect x="17" y="13" width="4" height="6" rx="1.5"/><path d="M19 19a4 4 0 01-4 3h-2"/></svg>@break
              @case('members')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 20c.6-3.4 2.8-5 5.5-5s4.9 1.6 5.5 5"/></svg>@break
              @case('jobs')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 6c-2-1.4-4.5-1.7-8-1v13c3.5-.7 6-.4 8 1 2-1.4 4.5-1.7 8-1V5c-3.5-.7-6-.4-8 1z"/><path d="M12 6v13"/></svg>@break
              @case('posts')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 5h16v11H9l-5 4z"/><path d="M8 9h8M8 12.5h5"/></svg>@break
              @case('partners')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="9" cy="8" r="3"/><circle cx="16.5" cy="9.5" r="2.3"/><path d="M3.5 19c.5-3.2 2.7-5 5.5-5s5 1.8 5.5 5"/><path d="M14.5 14.6c2.6.2 4.3 1.8 4.8 4.4"/></svg>@break
              @default<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M8 12l3 3 5-6"/><circle cx="12" cy="12" r="9"/></svg>
            @endswitch
          </span>
          <span class="stat-card__label">{{ $card['label'] }}</span>
          <span class="stat-card__chev" aria-hidden="true"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg></span>
        </div>
        <div class="stat-card__num">{{ number_format($card['count']) }}@if($card['delta'] === null)<span class="delta-chip" title="No records last week to compare against">—</span>@else<span class="delta-chip {{ $card['delta'] >= 0 ? 'delta-chip--up' : 'delta-chip--down' }}" title="Created this week vs. last week">{{ $card['delta'] >= 0 ? '↑' : '↓' }} {{ abs($card['delta']) }}%</span>@endif</div>
        <svg class="sparkline" viewBox="0 0 120 36" preserveAspectRatio="none" role="img" aria-label="Records created per day over the last 14 days">
          <polygon points="0,36 {{ $polyline }} 120,36" fill="currentColor" opacity="0.12" stroke="none"/>
          <polyline points="{{ $polyline }}" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <div class="stat-card__foot">{{ $card['delta'] === null ? 'New this period · 14-day activity' : 'vs last week · 14-day activity' }}</div>
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
        <svg viewBox="0 0 220 150" fill="none" aria-hidden="true">
          <ellipse cx="110" cy="126" rx="72" ry="8" fill="#F3EEE8"/>
          <rect x="62" y="38" width="96" height="66" rx="10" fill="#171310"/>
          <rect x="62" y="38" width="96" height="66" rx="10" stroke="#2A221C" stroke-width="1.5"/>
          <rect x="76" y="18" width="68" height="44" rx="4" fill="#fff" stroke="#E7E4DE"/>
          <rect x="84" y="26" width="52" height="4" rx="2" fill="#FFE9E2"/>
          <rect x="84" y="34" width="40" height="4" rx="2" fill="#FFE9E2"/>
          <rect x="84" y="42" width="46" height="4" rx="2" fill="#FFE9E2"/>
          <path d="M62 52l48 30 48-30" stroke="#FF4D1C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
          <circle cx="160" cy="40" r="17" fill="#FF4D1C"/>
          <path d="M152 40l7 2 14-5-5 14-7 2-2-6-7-2z" fill="#fff"/>
          <path d="M40 40l3 7 7 3-7 3-3 7-3-7-7-3 7-3z" fill="#FFB59C"/>
          <path d="M182 84l2.4 5.6 5.6 2.4-5.6 2.4-2.4 5.6-2.4-5.6-5.6-2.4 5.6-2.4z" fill="#FFC9B8"/>
          <circle cx="48" cy="96" r="4" fill="#FFE9E2" stroke="#FFB59C"/>
          <circle cx="178" cy="24" r="3" fill="#FFC9B8"/>
        </svg>
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
    <svg class="brand-panel__art" viewBox="0 0 120 90" fill="none" aria-hidden="true">
      <rect x="18" y="22" width="62" height="46" rx="8" fill="#fff" stroke="#EFE7DF"/>
      <rect x="28" y="48" width="8" height="12" rx="2" fill="#FFC9B8"/>
      <rect x="40" y="42" width="8" height="18" rx="2" fill="#FF9E7A"/>
      <rect x="52" y="34" width="8" height="26" rx="2" fill="#FF4D1C"/>
      <rect x="64" y="40" width="8" height="20" rx="2" fill="#FF7A50"/>
      <path d="M96 8l2.6 6.4L105 17l-6.4 2.6L96 26l-2.6-6.4L87 17l6.4-2.6z" fill="#FF4D1C"/>
      <path d="M84 4l22 8-9 5-3 9-4-11z" fill="#FF6B3D"/>
    </svg>
    <div class="brand-panel__mark">“</div>
    <p class="brand-panel__quote">Build. Grow. Together.</p>
    <span class="brand-panel__by">&mdash; Empetix Solutions</span>
  </div>
  </div>
@endsection
