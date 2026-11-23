@extends('layouts.admin')

@section('title', $item ? 'Edit Opening' : 'New Opening')
@section('crumb', 'Careers')

@section('content')
<div class="card">
  <form method="post" action="{{ $item ? route('admin.jobs.update', $item->id) : route('admin.jobs.store') }}">
    @csrf
    @if($item) @method('PUT') @endif
    <div class="form-grid">
      <div class="full"><x-admin.input name="ref_code" label="Requisition ID" :value="$item?->ref_code" placeholder="REQ-2026-014"/>
      <x-admin.input name="title" label="Job title" :value="$item?->title" :required="true"/></div>
      <x-admin.input name="slug" label="Slug" :value="$item?->slug" hint="Leave empty to generate from the title."/>
      <x-admin.select name="hiring_manager_id" label="Hiring manager (shown on the job page)" :options="['' => '— None —'] + $members->mapWithKeys(fn($m) => [$m->id => $m->name.' — '.$m->role])->all()" :value="$item?->hiring_manager_id" hint="Applications for this opening are reviewed by this person."/>
      <x-admin.input name="department" label="Department" :value="$item?->department" placeholder="SOFTWARE ENGINEERING"/>
      <x-admin.input name="location" label="Location" :value="$item?->location" placeholder="Cairo or Remote"/>
      <x-admin.input name="type" label="Type" :value="$item?->type" placeholder="Full-time"/>
      <div class="full"><x-admin.textarea name="summary" label="Card summary" :value="$item?->summary" rows="2"/></div>
      <div class="full"><x-admin.textarea name="intro" label="Detail page intro" :value="$item?->intro" rows="3"/></div>
      <div class="full"><x-admin.textarea name="tags" label="Tech tags (one per line)" :value="$item ? implode(PHP_EOL, (array) $item->tags) : ''" rows="3" hint="e.g. Laravel 13"/></div>
      <div class="full"><x-admin.textarea name="pills" label="Meta chips (one per line)" :value="$item ? implode(PHP_EOL, (array) $item->pills) : ''" rows="3" hint="e.g. Location: Cairo or Remote"/></div>
      <div class="full"><x-admin.textarea name="responsibilities" label="Responsibilities (one per line)" :value="$item ? implode(PHP_EOL, (array) $item->responsibilities) : ''" rows="6"/></div>
      <div class="full"><div class="full"><x-admin.textarea name="sections" label="Detail sections (Heading || item1 || item2, one section per line)" :value="$item ? collect((array) $item->sections)->map(fn($s) => ($s['heading'] ?? '').' || '.implode(' || ', (array) ($s['items'] ?? [])))->implode(PHP_EOL) : ''" rows="8" hint="Rendered as the numbered specification blocks on the job page."/></div>
      <x-admin.textarea name="requirements" label="Requirements (one per line)" :value="$item ? implode(PHP_EOL, (array) $item->requirements) : ''" rows="6"/></div>
      <x-admin.input name="sort_order" label="Sort order" type="number" :value="$item?->sort_order ?? 0"/>
      <x-admin.toggle name="is_active" label="Accepting applications" :checked="$item?->is_active ?? true"/>
    </div>
    <div class="form-actions">
      <button class="btn btn--primary" type="submit">{{ $item ? 'Save changes' : 'Create opening' }}</button>
      <a class="btn btn--ghost" href="{{ route('admin.jobs.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection
