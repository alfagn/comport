@extends('layouts.admin')

@section('title', 'Application')
@section('crumb', 'Job applications')

@section('actions')
  <a class="btn btn--ghost" href="{{ route('admin.applications.index') }}">&larr; Applications</a>
  <a class="btn btn--primary" href="{{ route('admin.applications.cv', $application->id) }}">Download CV</a>
@endsection

@section('content')
<div class="card">
  <div class="card__bar">
    <div>
      <h2 class="card__title" style="margin:0">{{ $application->full_name }}
        <span class="pill {{ $application->status === 'new' ? 'pill--accent' : '' }}" style="margin-left:8px">{{ ucfirst($application->status) }}</span>
      </h2>
      <p class="card__sub">Applied {{ $application->created_at->diffForHumans() }} for
        <strong>{{ $application->jobPost?->title ?? 'a removed opening' }}</strong>@if($application->jobPost?->ref_code) ({{ $application->jobPost->ref_code }})@endif
      </p>
    </div>
  </div>

  <div class="meta-grid">
    <div class="meta"><b>Email</b><a href="mailto:{{ $application->email }}" style="color:var(--accent)">{{ $application->email }}</a></div>
    <div class="meta"><b>Phone / WhatsApp</b>{{ $application->phone }}</div>
    <div class="meta"><b>City &amp; Country</b>{{ $application->location }}</div>
    <div class="meta"><b>Experience</b>{{ $application->experience_years }} years</div>
    <div class="meta"><b>Notice period</b>{{ str_replace('-', ' ', ucfirst($application->notice_period)) }}</div>
    @if($application->linkedin)<div class="meta"><b>LinkedIn</b><a href="{{ $application->linkedin }}" target="_blank" rel="noopener" style="color:var(--accent)">Profile</a></div>@endif
    @if($application->portfolio)<div class="meta"><b>Portfolio</b><a href="{{ $application->portfolio }}" target="_blank" rel="noopener" style="color:var(--accent)">Open</a></div>@endif
  </div>

  <h3 class="card__title" style="margin-top:22px;font-size:16px">Most complex system, product or campaign shipped</h3>
  <div class="message-body">{{ $application->achievement }}</div>

  <div class="form-actions">
    <form method="post" action="{{ route('admin.applications.destroy', $application->id) }}" data-confirm="Delete this application and its CV?" data-confirm-title="Delete Application">
      @csrf @method('DELETE')
      <button class="btn btn--danger" type="submit">Delete application</button>
    </form>
  </div>
</div>
@endsection
