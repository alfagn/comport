@extends('layouts.admin')

@section('title', 'Content Lists')
@section('crumb', 'Repeating cards & milestones')

@section('actions')
  <a class="btn btn--primary" href="{{ route('admin.content-lists.create') }}">+ New card</a>
@endsection

@section('content')
<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Title</th><th>List</th><th>Order</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($items as $item)
          <tr>
            <td class="cell-title">{{ $item->title }}</td>
            <td><span class="pill">{{ $groups[$item->group] ?? $item->group }}</span></td>
            <td>{{ $item->sort_order }}</td>
            <td><span class="pill {{ $item->is_active ? 'pill--on' : 'pill--off' }}">{{ $item->is_active ? 'Live' : 'Hidden' }}</span></td>
            <td class="row-actions">
              <a class="btn btn--ghost btn--sm" href="{{ route('admin.content-lists.edit', $item->id) }}">Edit</a>
              <form method="post" action="{{ route('admin.content-lists.destroy', $item->id) }}" onsubmit="return confirm('Delete this card?')">
                @csrf @method('DELETE')
                <button class="btn btn--danger btn--sm" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5"><div class="empty"><p>No cards yet.</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <x-admin.pagination :paginator="$items"/>
</div>
@endsection
