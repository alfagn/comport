@extends('layouts.admin')

@section('title', 'Brief from '.$message->name)
@section('crumb', 'Inbox')

@section('actions')
  <a class="btn btn--ghost" href="{{ route('admin.inbox.index') }}">← Inbox</a>
@endsection

@section('content')
<div class="card">
  <div class="meta-grid">
    <div class="meta"><b>Name</b>{{ $message->name }}</div>
    <div class="meta"><b>Email</b><a href="mailto:{{ $message->email }}" style="color:var(--accent)">{{ $message->email }}</a></div>
    <div class="meta"><b>Company</b>{{ $message->company ?? '—' }}</div>
    <div class="meta"><b>Budget</b>{{ $message->budget_tier ?? '—' }}</div>
    <div class="meta"><b>Timeline</b>{{ $message->timeline ?? '—' }}</div>
    <div class="meta"><b>Received</b>{{ $message->created_at->format('d M Y, H:i') }}</div>
  </div>

  @if($message->services)
    <p style="margin-top:0">
      @foreach($message->services as $s)<span class="pill pill--accent" style="margin:2px">{{ $s }}</span>@endforeach
    </p>
  @endif

  <div class="message-body">{{ $message->brief }}</div>

  <div class="form-actions">
    <a class="btn btn--primary" href="mailto:{{ $message->email }}">Reply by email</a>
    <form method="post" action="{{ route('admin.inbox.destroy', $message->id) }}" onsubmit="return confirm('Delete this message?')">
      @csrf @method('DELETE')
      <button class="btn btn--danger" type="submit">Delete</button>
    </form>
  </div>
</div>
@endsection
