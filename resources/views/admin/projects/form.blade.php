@extends('layouts.admin')

@section('title', $item ? 'Edit Project' : 'New Project')
@section('crumb', 'Projects')

@section('content')
<div class="card">
  <form method="post" enctype="multipart/form-data" action="{{ $item ? route('admin.projects.update', $item->id) : route('admin.projects.store') }}">
    @csrf
    @if($item) @method('PUT') @endif
    <div class="form-grid">
      <x-admin.input name="title" label="Title" :value="$item?->title" :required="true"/>
      <x-admin.input name="slug" label="Slug" :value="$item?->slug" hint="Leave empty to generate from the title."/>
      <x-admin.input name="client" label="Client" :value="$item?->client"/>
      <x-admin.input name="location" label="Location" :value="$item?->location" placeholder="Cairo & Riyadh"/>
      <x-admin.input name="year" label="Year" :value="$item?->year" placeholder="2026"/>
      <div class="full"><x-admin.input name="kicker" label="Kicker" :value="$item?->kicker" placeholder="FINTECH · SOFTWARE + GROWTH"/></div>
      <div class="full"><x-admin.textarea name="excerpt" label="Card excerpt" :value="$item?->excerpt" rows="2"/></div>
      <div class="full"><x-admin.textarea name="facts" label="Fact rows (Label | value, one per line)" :value="$item ? collect((array) $item->facts)->map(fn($x) => ($x['label'] ?? '').' | '.($x['value'] ?? ''))->implode(PHP_EOL) : ''" rows="4" hint="Client / Scope / Timeline / Verified Outcome rows under the title."/></div>
      <div class="full"><x-admin.textarea name="stats" label="Outcome stats (target | decimals | suffix | label, one per line)" :value="$item ? collect((array) $item->stats)->map(fn($x) => ($x['target'] ?? '').' | '.($x['decimals'] ?? 0).' | '.($x['suffix'] ?? '').' | '.($x['label'] ?? ''))->implode(PHP_EOL) : ''" rows="4" hint="e.g. 310 | 0 | % | Increase in activation"/></div>
      <div class="full"><x-admin.textarea name="sections" label="Narrative sections (Eyebrow || Heading || paragraph || paragraph, one section per line)" :value="$item ? collect((array) $item->sections)->map(fn($s) => ($s['eyebrow'] ?? '').' || '.($s['heading'] ?? '').' || '.implode(' || ', (array) ($s['items'] ?? [])))->implode(PHP_EOL) : ''" rows="8"/></div>
      <div class="full"><x-admin.textarea name="quote" label="Pull quote" :value="$item?->quote" rows="2"/></div>
      <x-admin.input name="quote_cite" label="Quote attribution" :value="$item?->quote_cite"/>
      <x-admin.input name="video_poster" label="Video poster path" :value="$item?->video_poster" placeholder="assets/img/work/nilepay-detail.jpg"/>
      <div class="full"><x-admin.input name="video_src" label="Video source path" :value="$item?->video_src" placeholder="assets/video/design-sprint.mp4"/></div>
      <div class="full"><x-admin.textarea name="body" label="Case study body" :value="$item?->body" rows="10" hint="Rendered on the project detail page. Paragraphs separated by blank lines."/></div>
      <x-admin.input name="tags" label="Tags (comma separated)" :value="$item?->tags?->pluck('name')->implode(', ')" placeholder="FinTech, SaaS, Growth"/>
      <x-admin.input name="sort_order" label="Sort order" type="number" :value="$item?->sort_order ?? 0"/>
      <div class="full"><x-admin.image name="image" label="Cover image" :current="$item?->image"/></div>
      <x-admin.toggle name="is_featured" label="Feature on the home page" :checked="$item?->is_featured ?? false"/>
      <x-admin.toggle name="is_active" label="Live on the site" :checked="$item?->is_active ?? true"/>
    </div>
    <div class="form-actions">
      <button class="btn btn--primary" type="submit">{{ $item ? 'Save changes' : 'Create project' }}</button>
      <a class="btn btn--ghost" href="{{ route('admin.projects.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection
