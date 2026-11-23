@extends('layouts.admin')

@section('title', 'Edit: '.ucfirst($page))
@section('crumb', 'Pages & Content')

@section('content')
<div class="card">
  <h2 class="card__title">{{ ucfirst($page) }} content blocks</h2>
  <p class="card__sub">Changes go live on the public site as soon as you save.</p>

  <form method="post" enctype="multipart/form-data" action="{{ route('admin.pages.update', $page) }}">
    @csrf @method('PUT')

    @foreach($sections->where('type', '!=', 'internal') as $section)
      @if($section->type === 'textarea')
        <x-admin.textarea :name="'sections['.$section->key.']'" :label="$section->label" :value="$section->value" rows="3"/>
      @elseif($section->type === 'image')
        <x-admin.image :name="'images['.$section->key.']'" :label="$section->label" :current="$section->value"/>
      @elseif($section->type === 'number')
        <x-admin.input :name="'sections['.$section->key.']'" :label="$section->label" type="number" step="any" :value="$section->value"/>
      @else
        <x-admin.input :name="'sections['.$section->key.']'" :label="$section->label" :value="$section->value"/>
      @endif
    @endforeach

    <div class="form-actions">
      <button class="btn btn--primary" type="submit">Save {{ $page }} content</button>
      <a class="btn btn--ghost" href="{{ route('admin.pages.index') }}">Back to pages</a>
    </div>
  </form>
</div>
@endsection
