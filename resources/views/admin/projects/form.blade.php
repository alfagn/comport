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
        <label class="form-label" style="display:block; font-weight:600; margin-bottom:0.35rem;">Detailed Project Description & Architecture (HTML Supported)</label>
        <x-admin.textarea name="body" label="" :value="$item?->body" rows="12" hint="Full case-study overview, system design, technical solutions, and architecture. Raw HTML tags (<h3>, <p>, <ul>, <code>, <div>) are rendered directly inside the project details page."/>
      </div>

      {{-- ═══════════════════════════════════════════════════════════════════
           MEDIA SECTION: All uploads stacked vertically (full width)
           ═══════════════════════════════════════════════════════════════════ --}}
      <div class="full">
        <h3 style="margin: 2rem 0 0.5rem; font-size: 1.15rem; color: var(--text-heading, #111); border-bottom: 2px solid var(--accent, #E85D2A); padding-bottom: 0.5rem;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-3px; margin-right:6px;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
          Brand Emblem, Cover & Media
        </h3>
      </div>

      {{-- 1. Project Logo --}}
      <div class="full"><x-admin.image name="logo" label="Project / Client Logo (Animated Hero Emblem)" :current="$item?->logo"/></div>

      {{-- 2. Cover Banner Image --}}
      <div class="full"><x-admin.image name="image" label="Cover Banner Image" :current="$item?->image"/></div>

      {{-- 3. Video Poster (Upload or Link) --}}
      <div class="full">
        <div class="media-dual-field" id="video_poster_dual">
          <div class="media-dual-field__head">
            <label class="field__label">Video Poster / Thumbnail</label>
            <div class="media-dual-field__tabs" role="tablist">
              <button type="button" class="media-dual-field__tab is-active" data-dual-mode="upload" data-dual-target="video_poster_dual">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Upload File
              </button>
              <button type="button" class="media-dual-field__tab" data-dual-mode="link" data-dual-target="video_poster_dual">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                Path / URL
              </button>
            </div>
          </div>
          <div class="media-dual-field__pane is-active" data-dual-pane="upload" data-dual-parent="video_poster_dual">
            <x-admin.image name="video_poster_file" label="" :current="null" accept="image/*" hint="Upload a poster/thumbnail image for the video player."/>
          </div>
          <div class="media-dual-field__pane" data-dual-pane="link" data-dual-parent="video_poster_dual">
            <x-admin.input name="video_poster" label="" :value="$item?->video_poster" placeholder="assets/img/work/alfagn-detail.png" hint="Path to existing asset or external URL."/>
          </div>
          @if($item?->video_poster)
            <div class="media-dual-field__current">
              <span class="media-dual-field__badge">Current:</span>
              <code>{{ $item->video_poster }}</code>
              @if(!str_starts_with($item->video_poster, 'http'))
                <img src="{{ str_starts_with($item->video_poster, 'uploads/') ? asset('storage/'.$item->video_poster) : asset($item->video_poster) }}" alt="Current poster" style="height:48px; border-radius:6px; margin-left:8px; vertical-align:middle;">
              @endif
            </div>
          @endif
        </div>
      </div>

      {{-- 4. Video Source (Upload or Link) --}}
      <div class="full">
        <div class="media-dual-field" id="video_src_dual">
          <div class="media-dual-field__head">
            <label class="field__label">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-3px; margin-right:4px;"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
              Video Source (MP4 / WebM)
            </label>
            <div class="media-dual-field__tabs" role="tablist">
              <button type="button" class="media-dual-field__tab is-active" data-dual-mode="upload" data-dual-target="video_src_dual">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Upload Video
              </button>
              <button type="button" class="media-dual-field__tab" data-dual-mode="link" data-dual-target="video_src_dual">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                Path / URL
              </button>
            </div>
          </div>
          <div class="media-dual-field__pane is-active" data-dual-pane="upload" data-dual-parent="video_src_dual">
            <x-admin.image name="video_src_file" label="" :current="null" accept="video/mp4,video/webm,video/quicktime" hint="Upload MP4, WebM, or MOV video file (up to 100 MB)."/>
          </div>
          <div class="media-dual-field__pane" data-dual-pane="link" data-dual-parent="video_src_dual">
            <x-admin.input name="video_src" label="" :value="$item?->video_src" placeholder="assets/video/alfagn-demo.mp4" hint="Path to existing local video or direct HTTPS URL."/>
          </div>
          @if($item?->video_src)
            <div class="media-dual-field__current">
              <span class="media-dual-field__badge">Current:</span>
              <code>{{ $item->video_src }}</code>
            </div>
          @endif
        </div>
      </div>

      {{-- 5. External Video / Embed URL --}}
      <div class="full"><x-admin.input name="video_url" label="External Video / Embed URL (YouTube, Vimeo)" :value="$item?->video_url" placeholder="https://www.youtube.com/watch?v=... or direct MP4 URL" hint="Optional external video link (embedded on the project page)."/></div>

      {{-- ═══════════════════════════════════════════════════════════════════
           GALLERY SECTION: Upload files + path/link textarea
           ═══════════════════════════════════════════════════════════════════ --}}
      <div class="full">
        <h3 style="margin: 2rem 0 0.5rem; font-size: 1.15rem; color: var(--text-heading, #111); border-bottom: 2px solid var(--accent, #E85D2A); padding-bottom: 0.5rem;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-3px; margin-right:6px;"><rect x="2" y="2" width="20" height="20" rx="2"/><path d="M2 17l5-5 4 4 4-4 7 7"/><circle cx="8.5" cy="8.5" r="1.5"/></svg>
          Project Gallery & Screenshots
        </h3>
      </div>

      {{-- Gallery File Upload --}}
      <div class="full">
        <label class="form-label" style="display:block; font-weight:600; margin-bottom:0.35rem;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px; margin-right:4px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          Upload Gallery Screenshots
        </label>
        <div class="gallery-upload-zone" id="gallery_drop_zone" style="border: 2px dashed var(--border-color, #d0d0d0); border-radius: 12px; padding: 1.5rem; text-align: center; cursor: pointer; transition: all 0.2s; background: var(--bg-offset, #fafafa);">
          <input type="file" name="gallery_files[]" multiple accept="image/*" id="gallery_files_input" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);opacity:0;">
          <div style="pointer-events:none;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--accent, #E85D2A)" stroke-width="1.5" style="margin-bottom:0.5rem;"><rect x="2" y="2" width="20" height="20" rx="2"/><path d="M2 17l5-5 4 4 4-4 7 7"/><circle cx="8.5" cy="8.5" r="1.5"/></svg>
            <p style="font-weight:600; margin:0; color:var(--text-heading, #111);"><span style="color:var(--accent, #E85D2A);">Click to choose</span> or drag & drop screenshots here</p>
            <p style="font-size:0.8rem; color:var(--text-muted, #777); margin:0.25rem 0 0;">Supports PNG, JPG, WEBP, SVG — multiple files allowed</p>
          </div>
        </div>
        <div id="gallery_file_list" style="margin-top:0.5rem;"></div>
      </div>

      {{-- Gallery Links/Paths --}}
      <div class="full">
        <x-admin.textarea name="gallery" label="Gallery Image Paths / URLs (one per line)" :value="$item && is_array($item->gallery) ? implode(PHP_EOL, $item->gallery) : (is_string($item?->gallery) ? $item->gallery : '')" rows="4" hint="Add asset paths (e.g. assets/img/work/alfagn/screenshot_1.png) or full URLs. Uploaded files above will also be added automatically."/>
      </div>

      {{-- Current Gallery Preview --}}
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

      {{-- ═══════════════════════════════════════════════════════════════════
           CLASSIFICATION & STATUS
           ═══════════════════════════════════════════════════════════════════ --}}
      <div class="full">
        <h3 style="margin: 2rem 0 0.5rem; font-size: 1.15rem; color: var(--text-heading, #111); border-bottom: 2px solid var(--accent, #E85D2A); padding-bottom: 0.5rem;">Classification & Status</h3>
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

{{-- Dual-mode tab switching (Upload ↔ Path/URL) --}}
<script>
(function(){
  document.querySelectorAll('.media-dual-field__tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
      var target = tab.getAttribute('data-dual-target');
      var mode = tab.getAttribute('data-dual-mode');
      var parent = document.getElementById(target);
      if (!parent) return;

      parent.querySelectorAll('.media-dual-field__tab').forEach(function(t) {
        t.classList.toggle('is-active', t.getAttribute('data-dual-mode') === mode);
      });
      parent.querySelectorAll('.media-dual-field__pane').forEach(function(p) {
        p.classList.toggle('is-active', p.getAttribute('data-dual-pane') === mode);
      });
    });
  });

  // Gallery drag & drop zone
  var dropZone = document.getElementById('gallery_drop_zone');
  var galleryInput = document.getElementById('gallery_files_input');
  var fileList = document.getElementById('gallery_file_list');

  if (dropZone && galleryInput) {
    dropZone.addEventListener('click', function() { galleryInput.click(); });

    ['dragenter', 'dragover'].forEach(function(evt) {
      dropZone.addEventListener(evt, function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.style.borderColor = 'var(--accent, #E85D2A)';
        dropZone.style.background = 'rgba(232,93,42,0.05)';
      });
    });
    ['dragleave', 'drop'].forEach(function(evt) {
      dropZone.addEventListener(evt, function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.style.borderColor = '';
        dropZone.style.background = '';
      });
    });
    dropZone.addEventListener('drop', function(e) {
      if (e.dataTransfer && e.dataTransfer.files.length) {
        galleryInput.files = e.dataTransfer.files;
        showSelectedFiles(galleryInput.files);
      }
    });

    galleryInput.addEventListener('change', function() {
      showSelectedFiles(galleryInput.files);
    });

    function showSelectedFiles(files) {
      if (!fileList) return;
      if (!files || files.length === 0) { fileList.innerHTML = ''; return; }
      var html = '<div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:0.5rem;">';
      for (var i = 0; i < files.length; i++) {
        var f = files[i];
        var url = URL.createObjectURL(f);
        html += '<div style="width:80px; text-align:center;">';
        html += '<img src="'+url+'" alt="'+f.name+'" style="width:80px; height:60px; object-fit:cover; border-radius:6px; border:1px solid #ddd;">';
        html += '<div style="font-size:0.65rem; color:#666; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-top:2px;">'+f.name+'</div>';
        html += '</div>';
      }
      html += '</div>';
      fileList.innerHTML = html;
    }
  }
})();
</script>

<style>
.media-dual-field { margin-bottom: 0.5rem; }
.media-dual-field__head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}
.media-dual-field__tabs {
  display: inline-flex;
  gap: 4px;
  background: var(--bg-offset, #f3f3f3);
  padding: 3px;
  border-radius: 8px;
}
.media-dual-field__tab {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 5px 12px;
  border: none;
  background: transparent;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 500;
  color: var(--text-muted, #888);
  cursor: pointer;
  transition: all 0.2s;
}
.media-dual-field__tab.is-active {
  background: #fff;
  color: var(--accent, #E85D2A);
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.media-dual-field__tab:hover:not(.is-active) { color: var(--text-heading, #333); }
.media-dual-field__pane { display: none; }
.media-dual-field__pane.is-active { display: block; }
.media-dual-field__current {
  margin-top: 0.5rem;
  padding: 0.5rem 0.75rem;
  background: var(--bg-offset, #f7f7f7);
  border-radius: 8px;
  font-size: 0.8rem;
  color: var(--text-muted, #666);
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
}
.media-dual-field__badge {
  font-weight: 600;
  color: var(--accent, #E85D2A);
  font-size: 0.75rem;
}
.media-dual-field__current code {
  background: rgba(0,0,0,0.05);
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 0.75rem;
  word-break: break-all;
}
.gallery-upload-zone:hover {
  border-color: var(--accent, #E85D2A) !important;
  background: rgba(232,93,42,0.03) !important;
}
</style>
@endsection
