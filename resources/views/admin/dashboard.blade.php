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
              @case('messages')<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 2.5h7.2l5.3 5.3V20a1.8 1.8 0 01-1.8 1.8H6A1.8 1.8 0 014.2 20V4.3A1.8 1.8 0 016 2.5z"/><path d="M13.2 2.5l5.3 5.3h-4.3a1 1 0 01-1-1z" fill="#fff"/><rect x="7.5" y="11.5" width="9" height="1.7" rx="0.85" fill="#fff"/><rect x="7.5" y="14.7" width="6.5" height="1.7" rx="0.85" fill="#fff"/><rect x="7.5" y="17.9" width="7.5" height="1.7" rx="0.85" fill="#fff"/></svg>@break
              @case('projects')<svg viewBox="0 0 24 24" fill="currentColor"><path d="M4.2 6.5A2.3 2.3 0 016.5 4.2h3.6l2 2.2h7.2a2.3 2.3 0 012.3 2.3v8.8a2.3 2.3 0 01-2.3 2.3H6.5a2.3 2.3 0 01-2.3-2.3z"/><path d="M9.4 13.4l2.1 2.1 3.9-4" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>@break
              @case('services')<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3.5a7.5 7.5 0 00-7.5 7.5v4.7a2.8 2.8 0 002.8 2.8H9a1 1 0 001-1v-4.8a1 1 0 00-1-1H6.5a5.5 5.5 0 0111 0H15a1 1 0 00-1 1v4.8a1 1 0 001 1h.7a3.4 3.4 0 01-3.2 2.4H11a1 1 0 000 2h1.5a5.4 5.4 0 005.2-4V11A7.5 7.5 0 0012 3.5z"/></svg>@break
              @case('members')<svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="8" r="3.6"/><path d="M4.8 20.2a7.2 7.2 0 0114.4 0z"/></svg>@break
              @case('jobs')<svg viewBox="0 0 24 24" fill="currentColor"><path d="M5.3 4.5C7.9 4.5 10.1 5.2 12 6.5c1.9-1.3 4.1-2 6.7-2a1.3 1.3 0 011.3 1.3v11.4a1.3 1.3 0 01-1.3 1.3c-2.3 0-4.3.7-5.7 1.7a1.3 1.3 0 01-1.9 0c-1.4-1-3.4-1.7-5.8-1.7a1.3 1.3 0 01-1.3-1.3V5.8a1.3 1.3 0 011.3-1.3z"/><rect x="11.2" y="7" width="1.6" height="11.4" rx="0.8" fill="#fff"/></svg>@break
              @case('posts')<svg viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 3.5h5.3a2.2 2.2 0 012.2 2.2v4.6a2.2 2.2 0 01-2.2 2.2h-.8v3.2l-3.6-3.2h-.9z" opacity="0.55"/><path d="M5.2 8h8.6a2.2 2.2 0 012.2 2.2v4.4a2.2 2.2 0 01-2.2 2.2H9l-3.9 3.2v-3.2h-.9a2.2 2.2 0 01-2.2-2.2v-4.4A2.2 2.2 0 015.2 8z"/><circle cx="7" cy="12.4" r="1.05" fill="#fff"/><circle cx="10" cy="12.4" r="1.05" fill="#fff"/><circle cx="13" cy="12.4" r="1.05" fill="#fff"/></svg>@break
              @case('partners')<svg viewBox="0 0 24 24" fill="currentColor"><circle cx="8.6" cy="8.2" r="3.1"/><path d="M2.8 19.8a5.9 5.9 0 0111.6 0z"/><circle cx="16.9" cy="9.4" r="2.5" opacity="0.85"/><path d="M14.6 19.8a5 5 0 016.6-4.7 5 5 0 011.6 4.7z" opacity="0.85"/></svg>@break
              @default<svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            @endswitch
          </span>
          <span class="stat-card__meta">
            <span class="stat-card__label">{{ $card['label'] }}</span>
            <span class="stat-card__sub">{{ $card['delta'] === null ? 'New this period' : 'vs last week' }} &middot; 14-day activity</span>
          </span>
          <span class="stat-card__chev" aria-hidden="true"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg></span>
        </div>
        <div class="stat-card__num">{{ number_format($card['count']) }}@if($card['delta'] === null)<span class="delta-chip" title="No records last week to compare against">—</span>@else<span class="delta-chip {{ $card['delta'] >= 0 ? 'delta-chip--up' : 'delta-chip--down' }}" title="Created this week vs. last week">{{ $card['delta'] >= 0 ? '↑' : '↓' }} {{ abs($card['delta']) }}%</span>@endif</div>
        <svg class="sparkline" viewBox="0 0 120 36" preserveAspectRatio="none" role="img" aria-label="Records created per day over the last 14 days">
          <polygon points="0,36 {{ $polyline }} 120,36" fill="currentColor" opacity="0.16" stroke="none"/>
          <polyline points="{{ $polyline }}" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
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
