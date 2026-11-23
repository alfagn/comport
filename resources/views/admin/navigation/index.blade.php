@extends('layouts.admin')

@section('title', 'Navigation')
@section('crumb', 'Menus & links')

@section('actions')
  <a class="btn btn--primary" href="{{ route('admin.navigation.create') }}">+ New link</a>
@endsection

@section('content')
<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Label</th><th>Menu</th><th>URL</th><th>Order</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($items as $item)
          <tr>
            <td class="cell-title">{{ $item->label }}</td>
            <td><span class="pill">{{ $item->menu === 'utility' ? 'Utility bar' : 'Mobile drawer' }}</span></td>
            <td>{{ $item->url }}</td>
            <td>{{ $item->sort_order }}</td>
            <td><span class="pill {{ $item->is_active ? 'pill--on' : 'pill--off' }}">{{ $item->is_active ? 'Live' : 'Hidden' }}</span></td>
            <td class="row-actions">
              <a class="btn btn--ghost btn--sm" href="{{ route('admin.navigation.edit', $item->id) }}">Edit</a>
              <form method="post" action="{{ route('admin.navigation.destroy', $item->id) }}" onsubmit="return confirm('Delete this link?')">
                @csrf @method('DELETE')
                <button class="btn btn--danger btn--sm" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6"><div class="empty"><p>No links yet.</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <x-admin.pagination :paginator="$items"/>
</div>
@endsection
