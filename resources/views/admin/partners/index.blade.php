@extends('layouts.admin')

@section('title', 'Partners')
@section('crumb', 'Logos & alliances')

@section('actions')
  <a class="btn btn--primary" href="{{ route('admin.partners.create') }}">+ New partner</a>
@endsection

@section('content')
<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Partner</th><th>Tier</th><th>Order</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($items as $partner)
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:14px">
                @if($partner->logo)<img class="thumb" style="object-fit:contain;padding:6px;background:#fff" src="{{ str_starts_with($partner->logo, 'uploads/') ? asset('storage/'.$partner->logo) : asset($partner->logo) }}" alt=""/>@endif
                <div class="cell-title">{{ $partner->name }}</div>
              </div>
            </td>
            <td>{{ $partner->tier ?? '—' }}</td>
            <td>{{ $partner->sort_order }}</td>
            <td><span class="pill {{ $partner->is_active ? 'pill--on' : 'pill--off' }}">{{ $partner->is_active ? 'Live' : 'Hidden' }}</span></td>
            <td class="row-actions">
              <a class="btn btn--ghost btn--sm" href="{{ route('admin.partners.edit', $partner->id) }}">Edit</a>
              <form method="post" action="{{ route('admin.partners.destroy', $partner->id) }}" onsubmit="return confirm('Delete this partner?')">
                @csrf @method('DELETE')
                <button class="btn btn--danger btn--sm" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5"><div class="empty"><p>No partners yet.</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <x-admin.pagination :paginator="$items"/>
</div>
@endsection
