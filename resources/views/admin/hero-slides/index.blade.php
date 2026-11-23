@extends('layouts.admin')

@section('title', 'Hero Slides')
@section('crumb', 'Home page rotation')

@section('actions')
  <a class="btn btn--primary" href="{{ route('admin.hero-slides.create') }}">+ New slide</a>
@endsection

@section('content')
<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Slide</th><th>Tag</th><th>Order</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($items as $slide)
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:14px">
                @if($slide->image)<img class="thumb" src="{{ str_starts_with($slide->image, 'uploads/') ? asset('storage/'.$slide->image) : asset($slide->image) }}" alt=""/>@endif
                <div>
                  <div class="cell-title">{{ $slide->title }}</div>
                  <div class="cell-sub">{{ \Illuminate\Support\Str::limit($slide->description, 70) }}</div>
                </div>
              </div>
            </td>
            <td><span class="pill pill--accent">{{ $slide->tag }}</span></td>
            <td>{{ $slide->sort_order }}</td>
            <td><span class="pill {{ $slide->is_active ? 'pill--on' : 'pill--off' }}">{{ $slide->is_active ? 'Live' : 'Hidden' }}</span></td>
            <td class="row-actions">
              <a class="btn btn--ghost btn--sm" href="{{ route('admin.hero-slides.edit', $slide->id) }}">Edit</a>
              <form method="post" action="{{ route('admin.hero-slides.destroy', $slide->id) }}" onsubmit="return confirm('Delete this slide?')">
                @csrf @method('DELETE')
                <button class="btn btn--danger btn--sm" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5"><div class="empty"><p>No slides yet.</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <x-admin.pagination :paginator="$items"/>
</div>
@endsection
