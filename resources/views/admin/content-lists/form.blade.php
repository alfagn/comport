@extends('layouts.admin')

@section('title', $item ? 'Edit Card' : 'New Card')
@section('crumb', 'Content Lists')

@section('content')
<div class="card">
  <form method="post" action="{{ $item ? route('admin.content-lists.update', $item->id) : route('admin.content-lists.store') }}">
    @csrf
    @if($item) @method('PUT') @endif
    <div class="form-grid">
      <div class="field">
        <label for="group">List</label>
        <select class="select" id="group" name="group">
          @foreach($groups as $value => $label)
            <option value="{{ $value }}" @selected(old('group', $item?->group) === $value)>{{ $label }}</option>
          @endforeach
        </select>
        @error('group')<p class="error">{{ $message }}</p>@enderror
      </div>
      <x-admin.input name="sort_order" label="Sort order" type="number" :value="$item?->sort_order ?? 0"/>
      <div class="full"><x-admin.input name="title" label="Title" :value="$item?->title" :required="true"/></div>
      <div class="full"><x-admin.textarea name="body" label="Body text" :value="$item?->body"/></div>
      <x-admin.input name="meta" label="Small top label" :value="$item?->meta" placeholder="2018 · STEP 30 MIN · TRACK CODE"/>
      <x-admin.input name="badge" label="Secondary label" :value="$item?->badge" placeholder="3 ROLES"/>
      <x-admin.input name="cta" label="Link text" :value="$item?->cta" placeholder="Filter Engineering Jobs"/>
      <x-admin.input name="url" label="URL / filter key" :value="$item?->url" placeholder="/work or engineering"/>
      <x-admin.toggle name="is_active" label="Visible on the site" :checked="$item?->is_active ?? true"/>
    </div>
    <div class="form-actions">
      <button class="btn btn--primary" type="submit">{{ $item ? 'Save changes' : 'Create card' }}</button>
      <a class="btn btn--ghost" href="{{ route('admin.content-lists.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection
