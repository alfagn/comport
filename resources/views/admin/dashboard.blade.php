@extends('layouts.admin')

@section('title', 'Dashboard')
@section('crumb', 'Site overview')

@section('content')
  <span class="welcome-pill">Welcome back, {{ auth()->user()->name }} 👋</span>
  <p class="page-sub">Here's what's happening with your website today.</p>

  {{--
    Stat cards. Alternating dark/light faces, each card shows:
    - the real record total,
    - a real 14-day sparkline of records created per day,
    - an honest week-over-week delta chip ("vs last week"), or an em-dash
      when last week had no records to compare against.
    The whole card links to its section.
  --}}
  <div class="stat-grid">
    @foreach($cards as $card)
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
              @case('messages')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 6h16v12H4z"/><path d="M4 7l8 6 8-6"/></svg>@break
              @case('projects')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 7h16v13H4z"/><path d="M9 7V4h6v3"/></svg>@break
              @case('services')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3l8 4.5v9L12 21l-8-4.5v-9z"/></svg>@break
              @case('members')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 20c.6-3.4 2.8-5 5.5-5s4.9 1.6 5.5 5"/></svg>@break
              @case('jobs')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2"/></svg>@break
              @case('posts')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M5 4h11l3 3v13H5z"/><path d="M9 9h6M9 13h6"/></svg>@break
              @default<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M8 12l3 3 5-6"/><circle cx="12" cy="12" r="9"/></svg>
            @endswitch
          </span>
          <svg class="stat-card__chev" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
        </div>
        <div class="stat-card__label">{{ $card['label'] }}</div>
        <div class="stat-card__num">{{ number_format($card['count']) }}@if($card['delta'] === null)<span class="delta-chip" title="No records last week to compare against">—</span>@else<span class="delta-chip {{ $card['delta'] >= 0 ? 'delta-chip--up' : 'delta-chip--down' }}" title="Created this week vs. last week">{{ $card['delta'] >= 0 ? '↑' : '↓' }} {{ abs($card['delta']) }}%</span>@endif</div>
        <svg class="sparkline" viewBox="0 0 120 36" preserveAspectRatio="none" aria-hidden="true">
          <polyline points="{{ $polyline }}" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @unless($card['delta'] === null)
          <div class="stat-card__foot">vs last week</div>
        @endunless
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
        <svg viewBox="0 0 200 140" fill="none" aria-hidden="true">
          <rect x="55" y="30" width="90" height="60" rx="8" fill="#FFE9E2"/>
          <rect x="55" y="30" width="90" height="60" rx="8" stroke="#FF4D1C" stroke-width="2"/>
          <path d="M55 44l45 26 45-26" stroke="#FF4D1C" stroke-width="2"/>
          <circle cx="152" cy="42" r="14" fill="#FF4D1C"/>
          <path d="M146 42l4 4 8-8" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M30 110h140" stroke="#E7E4DE" stroke-width="3" stroke-linecap="round"/>
          <circle cx="40" cy="55" r="4" fill="#FFC9B8"/>
          <circle cx="166" cy="86" r="5" fill="#FFC9B8"/>
          <path d="M28 78l6 6M34 78l-6 6" stroke="#FFB59C" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <p>No briefs yet. New contact-form submissions land here.</p>
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
    <div class="brand-panel__mark">“</div>
    <p class="brand-panel__quote">Build. Grow. Together.</p>
    <span class="brand-panel__by">Empetix Solutions</span>
  </div>
  </div>
@endsection
