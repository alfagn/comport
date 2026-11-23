@extends('layouts.admin')

@section('title', 'Careers')
@section('crumb', 'Open positions')

@section('actions')
  <a class="btn btn--primary" href="{{ route('admin.jobs.create') }}">+ New opening</a>
@endsection

@section('content')
<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Role</th><th>Department</th><th>Location</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($items as $job)
          <tr>
            <td>
              <div class="cell-title">{{ $job->title }}</div>
              <div class="cell-sub">{{ \Illuminate\Support\Str::limit($job->summary, 70) }}</div>
            </td>
            <td><span class="pill pill--accent">{{ $job->department }}</span></td>
            <td>{{ $job->location ?? '—' }}</td>
            <td><span class="pill {{ $job->is_active ? 'pill--on' : 'pill--off' }}">{{ $job->is_active ? 'Open' : 'Closed' }}</span></td>
            <td class="row-actions">
              <a class="btn btn--ghost btn--sm" href="{{ route('admin.jobs.edit', $job->id) }}">Edit</a>
              <form method="post" action="{{ route('admin.jobs.destroy', $job->id) }}" onsubmit="return confirm('Delete this opening?')">
                @csrf @method('DELETE')
                <button class="btn btn--danger btn--sm" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5"><div class="empty"><p>No openings yet.</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <x-admin.pagination :paginator="$items"/>
</div>
@endsection
