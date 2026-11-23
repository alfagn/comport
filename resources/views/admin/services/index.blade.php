@extends('layouts.admin')

@section('title', 'Services')
@section('crumb', 'Solutions shown on the site')

@section('actions')
  <a class="btn btn--primary" href="{{ route('admin.services.create') }}">+ New service</a>
@endsection

@section('content')
<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Service</th><th>Tag</th><th>Order</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($items as $service)
          <tr>
            <td>
              <div class="cell-title">{{ $service->title }}</div>
              <div class="cell-sub">{{ $service->heading }}</div>
            </td>
            <td><span class="pill pill--accent">{{ $service->tag }}</span></td>
            <td>{{ $service->sort_order }}</td>
            <td><span class="pill {{ $service->is_active ? 'pill--on' : 'pill--off' }}">{{ $service->is_active ? 'Live' : 'Hidden' }}</span></td>
            <td class="row-actions">
              <a class="btn btn--ghost btn--sm" href="{{ route('admin.services.edit', $service->id) }}">Edit</a>
              <form method="post" action="{{ route('admin.services.destroy', $service->id) }}" onsubmit="return confirm('Delete this service?')">
                @csrf @method('DELETE')
                <button class="btn btn--danger btn--sm" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5"><div class="empty"><p>No services yet.</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <x-admin.pagination :paginator="$items"/>
</div>
@endsection
