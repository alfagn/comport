@extends('layouts.admin')

@section('title', 'Dashboard')
@section('crumb', 'Site overview')

@section('content')
  {{-- Stat cards: one per managed entity + the unread inbox badge. --}}
  <div class="stat-grid">
    <div class="stat-card">
      <div class="stat-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 6h16v12H4z"/><path d="M4 7l8 6 8-6"/></svg></div>
      <div class="stat-card__num">{{ $stats['unread_messages'] }}</div>
      <div class="stat-card__label">Unread briefs</div>
    </div>
    <div class="stat-card">
      <div class="stat-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 7h16v13H4z"/><path d="M9 7V4h6v3"/></svg></div>
      <div class="stat-card__num">{{ $stats['projects'] }}</div>
      <div class="stat-card__label">Projects</div>
    </div>
    <div class="stat-card">
      <div class="stat-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3l8 4.5v9L12 21l-8-4.5v-9z"/></svg></div>
      <div class="stat-card__num">{{ $stats['services'] }}</div>
      <div class="stat-card__label">Services</div>
    </div>
    <div class="stat-card">
      <div class="stat-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 20c.6-3.4 2.8-5 5.5-5s4.9 1.6 5.5 5"/></svg></div>
      <div class="stat-card__num">{{ $stats['members'] }}</div>
      <div class="stat-card__label">Team &amp; founders</div>
    </div>
    <div class="stat-card">
      <div class="stat-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2"/></svg></div>
      <div class="stat-card__num">{{ $stats['jobs'] }}</div>
      <div class="stat-card__label">Open roles</div>
    </div>
    <div class="stat-card">
      <div class="stat-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M5 4h11l3 3v13H5z"/><path d="M9 9h6M9 13h6"/></svg></div>
      <div class="stat-card__num">{{ $stats['posts'] }}</div>
      <div class="stat-card__label">Blog posts</div>
    </div>
    <div class="stat-card">
      <div class="stat-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M8 12l3 3 5-6"/><circle cx="12" cy="12" r="9"/></svg></div>
      <div class="stat-card__num">{{ $stats['partners'] }}</div>
      <div class="stat-card__label">Partners</div>
    </div>
  </div>

  <div class="card">
    <h2 class="card__title">Latest briefs</h2>
    <p class="card__sub">Project inquiries from the contact form.</p>
    @if($latestMessages->isEmpty())
      <div class="empty">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 6h16v12H4z"/><path d="M4 7l8 6 8-6"/></svg>
        <p>No briefs yet. New contact-form submissions land here.</p>
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
@endsection
