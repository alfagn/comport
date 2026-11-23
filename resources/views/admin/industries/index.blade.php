@extends('layouts.admin')

@section('title', 'Industries')
@section('crumb', 'Sector grid on the home page')

@section('actions')
  <a class="btn btn--primary" href="{{ route('admin.industries.create') }}">+ New industry</a>
@endsection

@section('content')
<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Industry</th><th>Order</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($items as $industry)
          <tr>
            <td>
              <div class="cell-title">{{ $industry->title }}</div>
              <div class="cell-sub">{{ $industry->subtitle }}</div>
            </td>
            <td>{{ $industry->sort_order }}</td>
            <td><span class="pill {{ $industry->is_active ? 'pill--on' : 'pill--off' }}">{{ $industry->is_active ? 'Live' : 'Hidden' }}</span></td>
            <td class="row-actions">
              <a class="btn btn--ghost btn--sm" href="{{ route('admin.industries.edit', $industry->id) }}">Edit</a>
              <form method="post" action="{{ route('admin.industries.destroy', $industry->id) }}" onsubmit="return confirm('Delete this industry?')">
                @csrf @method('DELETE')
                <button class="btn btn--danger btn--sm" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="4"><div class="empty"><p>No industries yet.</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <x-admin.pagination :paginator="$items"/>
</div>
@endsection
