@extends('layouts.admin')
@section('title', 'Edit service page edition')
@section('content')
<div class="content-header"><h1>{{ $item->title }}</h1><p>{{ $item->locale === 'ar' ? 'Arabic / العربية' : 'English' }}. Draft copy needs your review. Confirm the service scope before publishing. This does not deploy the site or submit it to search engines.</p><a class="btn btn--ghost" href="{{ route('admin.service-pages.preview', $item->id) }}" target="_blank">Private noindex preview ↗</a></div>
<div class="card"><form method="post" action="{{ route('admin.service-pages.update', $item->id) }}">@csrf @method('PUT')
<div class="form-grid" dir="{{ $item->locale === 'ar' ? 'rtl' : 'ltr' }}">
<div class="full"><x-admin.input name="title" label="Page heading" :value="$item->title" :required="true"/></div>
<div class="full"><x-admin.textarea name="summary" label="Intro summary" :value="$item->summary" rows="3"/></div>
<div class="full"><x-admin.textarea name="body" label="Sections: heading then paragraph, blank line between sections. Plain text only." :value="$item->body" rows="18"/></div>
<div class="full"><x-admin.input name="meta_title" label="Search title" :value="$item->meta_title"/></div>
<div class="full"><x-admin.textarea name="meta_description" label="Search description" :value="$item->meta_description" rows="3"/></div>
<div class="full"><x-admin.toggle name="is_published" label="I reviewed this edition and want it public on this installation" :checked="$item->is_published"/></div>
</div><div class="form-actions"><button class="btn btn--primary" type="submit">Save edition</button><a href="{{ route('admin.service-pages.index') }}" class="btn btn--ghost">Back</a></div>
</form></div>
@endsection
