@extends('layouts.admin')

@section('title', 'Projects')
@section('crumb', 'Case studies & portfolio')

@section('actions')
  <a class="btn btn--primary" href="{{ route('admin.projects.create') }}">+ New project</a>
@endsection

@section('content')
<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Project</th><th>Tags</th><th>Featured</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($items as $project)
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:14px">
                @if($project->image)<img class="thumb" src="{{ str_starts_with($project->image, 'uploads/') ? asset('storage/'.$project->image) : asset($project->image) }}" alt=""/>@endif
                <div>
                  <div class="cell-title">{{ $project->title }}</div>
                  <div class="cell-sub">{{ $project->kicker }}</div>
                </div>
              </div>
            </td>
            <td>
              @foreach($project->tags as $tag)<span class="pill pill--accent" style="margin:1px">{{ $tag->name }}</span>@endforeach
            </td>
            <td>@if($project->is_featured)<span class="pill pill--warn">Featured</span>@else—@endif</td>
            <td><span class="pill {{ $project->is_active ? 'pill--on' : 'pill--off' }}">{{ $project->is_active ? 'Live' : 'Hidden' }}</span></td>
            <td class="row-actions">
              <a class="btn btn--ghost btn--sm" href="{{ route('admin.projects.edit', $project->id) }}">Edit</a>
              <form method="post" action="{{ route('admin.projects.destroy', $project->id) }}" onsubmit="return confirm('Delete this project?')">
                @csrf @method('DELETE')
                <button class="btn btn--danger btn--sm" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5"><div class="empty"><p>No projects yet.</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <x-admin.pagination :paginator="$items"/>
</div>
@endsection
