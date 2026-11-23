@extends('layouts.admin')

@section('title', $item ? 'Edit Post' : 'New Post')
@section('crumb', 'Blog & Media')

@section('content')
<div class="card">
  <form method="post" enctype="multipart/form-data" action="{{ $item ? route('admin.posts.update', $item->id) : route('admin.posts.store') }}">
    @csrf
    @if($item) @method('PUT') @endif
    <div class="form-grid">
      <div class="full"><x-admin.input name="title" label="Title" :value="$item?->title" :required="true"/></div>
      <x-admin.input name="slug" label="Slug" :value="$item?->slug" hint="Leave empty to generate from the title."/>
      <x-admin.select name="type" label="Format" :options="['article' => 'Article', 'video' => 'Video episode', 'photos' => 'Photo essay']" :value="$item?->type ?? 'article'"/>
      <x-admin.input name="category" label="Category" :value="$item?->category" placeholder="DESIGN & CRO"/>
      <x-admin.input name="published_at" label="Publish date" type="date" :value="$item?->published_at?->format('Y-m-d')"/>
      <div class="full"><x-admin.textarea name="excerpt" label="Card excerpt" :value="$item?->excerpt" rows="2"/></div>
      <div class="full"><x-admin.textarea name="body" label="Body" :value="$item?->body" rows="12" hint="Paragraphs separated by blank lines."/></div>
      <x-admin.input name="video_url" label="Video URL (video posts)" type="url" :value="$item?->video_url"/>
      <x-admin.textarea name="meta" label="Meta chips (one per line)" :value="$item ? implode(PHP_EOL, (array) $item->meta) : ''" rows="2" hint="e.g. 8 MIN READ"/>
      <div class="full"><x-admin.image name="cover_image" label="Cover image" :current="$item?->cover_image"/></div>
      <x-admin.toggle name="is_published" label="Published" :checked="$item?->is_published ?? false"/>
    </div>
    <div class="form-actions">
      <button class="btn btn--primary" type="submit">{{ $item ? 'Save changes' : 'Create post' }}</button>
      <a class="btn btn--ghost" href="{{ route('admin.posts.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection
