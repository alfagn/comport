@extends('layouts.admin')

@section('title', 'Newsletter Subscribers')
@section('crumb', 'Footer signups')

@section('actions')
  <a class="btn btn--ghost" href="{{ route('admin.inbox.index') }}">← Inbox</a>
@endsection

@section('content')
<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Email</th><th>Subscribed</th></tr></thead>
      <tbody>
        @forelse($items as $s)
          <tr>
            <td class="cell-title">{{ $s->email }}</td>
            <td>{{ $s->created_at->diffForHumans() }}</td>
          </tr>
        @empty
          <tr><td colspan="2"><div class="empty"><p>No subscribers yet.</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <x-admin.pagination :paginator="$items"/>
</div>
@endsection
