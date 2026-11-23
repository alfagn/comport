@extends('layouts.admin')

@section('title', 'Team & Founders')
@section('crumb', 'People shown on the site')

@section('actions')
  <a class="btn btn--primary" href="{{ route('admin.members.create') }}">+ New person</a>
@endsection

@section('content')
<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Person</th><th>Kind</th><th>Order</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($items as $member)
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:14px">
                @if($member->photo)<img class="thumb" src="{{ str_starts_with($member->photo, 'uploads/') ? asset('storage/'.$member->photo) : asset($member->photo) }}" alt=""/>@endif
                <div>
                  <div class="cell-title">{{ $member->name }}</div>
                  <div class="cell-sub">{{ $member->role }}</div>
                </div>
              </div>
            </td>
            <td><span class="pill {{ $member->kind === 'founder' ? 'pill--accent' : 'pill--off' }}">{{ ucfirst($member->kind) }}</span></td>
            <td>{{ $member->sort_order }}</td>
            <td><span class="pill {{ $member->is_active ? 'pill--on' : 'pill--off' }}">{{ $member->is_active ? 'Live' : 'Hidden' }}</span></td>
            <td class="row-actions">
              <a class="btn btn--ghost btn--sm" href="{{ route('admin.members.edit', $member->id) }}">Edit</a>
              <form method="post" action="{{ route('admin.members.destroy', $member->id) }}" onsubmit="return confirm('Delete this person?')">
                @csrf @method('DELETE')
                <button class="btn btn--danger btn--sm" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5"><div class="empty"><p>No people yet.</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <x-admin.pagination :paginator="$items"/>
</div>
@endsection
