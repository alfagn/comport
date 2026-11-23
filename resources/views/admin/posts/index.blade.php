@extends('layouts.admin')

@section('title', 'Blog & Media')
@section('crumb', 'Articles, videos and photo essays')

@section('actions')
  <a class="btn btn--primary" href="{{ route('admin.posts.create') }}">+ New post</a>
@endsection

@section('content')
<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Post</th><th>Type</th><th>Published</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($items as $post)
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:14px">
                @if($post->cover_image)<img class="thumb" src="{{ str_starts_with($post->cover_image, 'uploads/') ? asset('storage/'.$post->cover_image) : asset($post->cover_image) }}" alt=""/>@endif
                <div>
                  <div class="cell-title">{{ \Illuminate\Support\Str::limit($post->title, 60) }}</div>
                  <div class="cell-sub">{{ $post->category }}</div>
                </div>
              </div>
            </td>
            <td><span class="pill pill--accent">{{ ucfirst($post->type) }}</span></td>
            <td>{{ $post->published_at?->format('d M Y') ?? '—' }}</td>
            <td><span class="pill {{ $post->is_published ? 'pill--on' : 'pill--off' }}">{{ $post->is_published ? 'Published' : 'Draft' }}</span></td>
            <td class="row-actions">
              <a class="btn btn--ghost btn--sm" href="{{ route('admin.posts.edit', $post->id) }}">Edit</a>
              <form method="post" action="{{ route('admin.posts.destroy', $post->id) }}" onsubmit="return confirm('Delete this post?')">
                @csrf @method('DELETE')
                <button class="btn btn--danger btn--sm" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5"><div class="empty"><p>No posts yet.</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <x-admin.pagination :paginator="$items"/>
</div>
@endsection
