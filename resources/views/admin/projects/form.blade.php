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
      <div class="full"><x-admin.input name="live_url" label="Live Project URL (Visit Live Site)" :value="$item?->live_url" placeholder="https://alfagn.com" hint="Direct link for visitors to test or inspect the live platform."/></div>
      <x-admin.input name="client" label="Client" :value="$item?->client" placeholder="Alfagn EduTech"/>
      <x-admin.input name="location" label="Location" :value="$item?->location" placeholder="Riyadh & Cairo"/>
      <x-admin.input name="year" label="Year" :value="$item?->year" placeholder="2026"/>
      <div class="full"><x-admin.input name="kicker" label="Kicker / Category" :value="$item?->kicker" placeholder="EDTECH · HIGH-CONCURRENCY LEARNING & EXAMS"/></div>
      <div class="full"><x-admin.textarea name="excerpt" label="Card short summary" :value="$item?->excerpt" rows="2" hint="Brief overview displayed on the project card in the grid."/></div>
      
      <div class="full"><x-admin.textarea name="facts" label="Fact rows (Label | value, one per line)" :value="$item ? collect((array) $item->facts)->map(fn($x) => ($x['label'] ?? '').' | '.($x['value'] ?? ''))->implode(PHP_EOL) : ''" rows="4" hint="e.g. Client | Alfagn Saudi Arabia \n Stack | Laravel 11, Redis, WebRTC \n Scope | Architecture, LMS, Live Streaming"/></div>
      <div class="full"><x-admin.textarea name="stats" label="Outcome stats (target | decimals | suffix | label, one per line)" :value="$item ? collect((array) $item->stats)->map(fn($x) => ($x['target'] ?? '').' | '.($x['decimals'] ?? 0).' | '.($x['suffix'] ?? '').' | '.($x['label'] ?? ''))->implode(PHP_EOL) : ''" rows="4" hint="e.g. 45000 | 0 | + | Active Enrolled Students \n 180 | 0 | ms | Live Exam Response Latency"/></div>
      <div class="full"><x-admin.textarea name="sections" label="Narrative sections (Eyebrow || Heading || paragraph || paragraph, one section per line)" :value="$item ? collect((array) $item->sections)->map(fn($s) => ($s['eyebrow'] ?? '').' || '.($s['heading'] ?? '').' || '.implode(' || ', (array) ($s['items'] ?? [])))->implode(PHP_EOL) : ''" rows="8"/></div>
      
      <div class="full"><x-admin.textarea name="quote" label="Client Testimonial / Pull Quote" :value="$item?->quote" rows="2"/></div>
      <x-admin.input name="quote_cite" label="Quote attribution" :value="$item?->quote_cite" placeholder="Dr. Founder & CTO"/>
      
      <div class="full">
        <h3 style="margin: 1.5rem 0 0.5rem; font-size: 1.1rem; color: var(--text-heading, #111);">Media Showcase & Videos</h3>
      </div>
      <x-admin.input name="video_poster" label="Video poster path" :value="$item?->video_poster" placeholder="assets/img/work/alfagn-detail.png"/>
      <x-admin.input name="video_src" label="Video source path / local stream" :value="$item?->video_src" placeholder="assets/video/alfagn-demo.mp4"/>
      <div class="full"><x-admin.input name="video_url" label="External Video / Embed URL" :value="$item?->video_url" placeholder="https://www.youtube.com/watch?v=... or direct MP4 URL" hint="Optional external video link."/></div>
      
      <div class="full">
        <label class="form-label" style="display:block; font-weight:600; margin-bottom:0.35rem;">Detailed Project Description & Architecture (HTML Supported)</label>
        <x-admin.textarea name="body" label="" :value="$item?->body" rows="12" hint="Full case-study overview, system design, technical solutions, and architecture. Raw HTML tags (<h3>, <p>, <ul>, <code>, <div>) are rendered directly inside the project details page."/>
      </div>

      <div class="full">
        <h3 style="margin: 1.5rem 0 0.5rem; font-size: 1.1rem; color: var(--text-heading, #111);">Screenshots & Gallery</h3>
      </div>
      <div class="full"><x-admin.image name="image" label="Cover banner image" :current="$item?->image"/></div>
      
      <div class="full">
        <label class="form-label" style="display:block; font-weight:600; margin-bottom:0.35rem;">Upload Project Gallery Screenshots</label>
        <input type="file" name="gallery_files[]" multiple accept="image/*" class="form-control" style="padding: 0.6rem; border: 1px dashed var(--border-color, #ccc); width:100%; border-radius: 8px;">
        <p style="font-size:0.8rem; color:var(--text-muted, #777); margin-top:0.3rem;">You can select multiple screenshots/designs to upload.</p>
      </div>

      <div class="full">
        <x-admin.textarea name="gallery" label="Gallery Image Paths (one per line)" :value="$item && is_array($item->gallery) ? implode(PHP_EOL, $item->gallery) : (is_string($item?->gallery) ? $item->gallery : '')" rows="4" hint="Add image paths or URLs (e.g. assets/img/work/alfagn/course_preview_1.png). Uploaded files above will also be added here."/>
      </div>

      @if($item && !empty($item->gallery))
      <div class="full">
        <label style="display:block; font-weight:600; margin-bottom:0.5rem;">Current Gallery Images</label>
        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px;">
          @foreach((array) $item->gallery as $gImg)
            <div style="border-radius: 8px; overflow:hidden; border: 1px solid rgba(0,0,0,0.1); background:#f9f9f9; text-align:center;">
              <img src="{{ str_starts_with((string) $gImg, 'uploads/') ? asset('storage/'.$gImg) : asset($gImg) }}" alt="Gallery thumbnail" style="width:100%; height:90px; object-fit:cover; display:block;">
              <div style="font-size: 0.68rem; padding: 4px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:#666;">{{ basename($gImg) }}</div>
            </div>
          @endforeach
        </div>
      </div>
      @endif

      <div class="full">
        <h3 style="margin: 1.5rem 0 0.5rem; font-size: 1.1rem; color: var(--text-heading, #111);">Classification & Status</h3>
      </div>
      <x-admin.input name="tags" label="Tags (comma separated)" :value="$item?->tags?->pluck('name')->implode(', ')" placeholder="LMS, High Concurrency, WebRTC, SaaS"/>
      <x-admin.input name="sort_order" label="Sort order" type="number" :value="$item?->sort_order ?? 0"/>
      <x-admin.toggle name="is_featured" label="Feature on the home page" :checked="$item?->is_featured ?? false"/>
      <x-admin.toggle name="is_active" label="Live on the site" :checked="$item?->is_active ?? true"/>
    </div>
    <div class="form-actions" style="margin-top: 2rem;">
      <button class="btn btn--primary" type="submit">{{ $item ? 'Save changes' : 'Create project' }}</button>
      <a class="btn btn--ghost" href="{{ route('admin.projects.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection
