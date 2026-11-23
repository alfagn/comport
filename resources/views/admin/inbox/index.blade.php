@extends('layouts.admin')

@section('title', 'Inbox')
@section('crumb', 'Project briefs from the contact form')

@section('actions')
  <a class="btn btn--ghost" href="{{ route('admin.inbox.subscribers') }}">Newsletter subscribers</a>
@endsection

@section('content')
<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>From</th><th>Company</th><th>Services</th><th>Budget</th><th>Received</th><th></th></tr></thead>
      <tbody>
        @forelse($items as $m)
          <tr class="message-row {{ $m->read_at ? '' : 'unread' }}">
            <td>
              <div class="cell-title">{{ $m->name }}</div>
              <div class="cell-sub">{{ $m->email }}</div>
            </td>
            <td>{{ $m->company ?? '—' }}</td>
            <td>{{ $m->services ? implode(', ', array_slice($m->services, 0, 2)) : '—' }}</td>
            <td>{{ $m->budget_tier ?? '—' }}</td>
            <td>{{ $m->created_at->diffForHumans() }}</td>
            <td class="row-actions">
              <a class="btn btn--ghost btn--sm" href="{{ route('admin.inbox.show', $m->id) }}">Open</a>
              <form method="post" action="{{ route('admin.inbox.destroy', $m->id) }}" onsubmit="return confirm('Delete this message?')">
                @csrf @method('DELETE')
                <button class="btn btn--danger btn--sm" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6"><div class="empty"><p>No briefs yet.</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <x-admin.pagination :paginator="$items"/>
</div>
@endsection
