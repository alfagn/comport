{{--
  Dual-mode Video field:
  1) Upload video file directly to Private Storage (storage/app/private/videos/pages/...)
  2) Or provide an external HTTPS URL / local public asset path (assets/video/...)
  Props: sectionKey, label, value, hint
--}}
@props(['sectionKey', 'label', 'value' => null, 'hint' => null])
@php
  $currentVal = old('sections.'.$sectionKey, $value ?? '');
  $isPrivate = str_starts_with((string) $currentVal, 'private:videos/');
  $resolvedSrc = $currentVal ? content_asset($currentVal) : '';
  $widgetId = 'vid_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $sectionKey) . '_' . substr(md5($sectionKey), 0, 6);
@endphp

<div class="field field--video" id="{{ $widgetId }}">
  <div class="video-field__head">
    <label class="field__label" for="{{ $widgetId }}_link">{{ $label }}</label>
    <div class="video-field__tabs" role="tablist" aria-label="Video source mode">
      <button type="button" class="video-field__tab {{ $isPrivate ? 'is-active' : '' }}" data-video-mode="upload">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        <span>Upload File (Private Storage)</span>
      </button>
      <button type="button" class="video-field__tab {{ ! $isPrivate ? 'is-active' : '' }}" data-video-mode="link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
        <span>Video Link / Path</span>
      </button>
    </div>
  </div>

  <div class="video-field__box">
    {{-- Panel 1: Upload Video to Private Storage --}}
    <div class="video-field__pane {{ $isPrivate ? 'is-active' : '' }}" data-video-pane="upload">
      <input type="file"
             id="{{ $widgetId }}_file"
             name="videos[{{ $sectionKey }}]"
             accept="video/mp4,video/webm,video/quicktime,video/ogg"
             class="video-field__native-file"/>

      <label for="{{ $widgetId }}_file" class="video-field__dropzone" id="{{ $widgetId }}_drop">
        <span class="video-field__drop-ico" aria-hidden="true">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
        </span>
        <span class="video-field__drop-copy">
          <strong>Click to choose a video file</strong> or drag &amp; drop here
          <small id="{{ $widgetId }}_file_meta">Stored securely in Private Storage (MP4, WebM, MOV — streamed with byte-range playback)</small>
        </span>
      </label>
    </div>

    {{-- Panel 2: Direct Link or Public Asset Path --}}
    <div class="video-field__pane {{ ! $isPrivate ? 'is-active' : '' }}" data-video-pane="link">
      <input type="text"
             id="{{ $widgetId }}_link"
             name="sections[{{ $sectionKey }}]"
             value="{{ $currentVal }}"
             class="input"
             placeholder="https://example.com/overview.mp4 or assets/video/empetix-overview.mp4"/>
      <div class="hint">Paste a direct HTTPS MP4/WebM link, local public path, or keep the current Private Storage token.</div>
    </div>

    {{-- Live Video Preview Strip --}}
    <div class="video-field__preview" id="{{ $widgetId }}_preview_wrap" style="{{ $resolvedSrc ? '' : 'display:none;' }}">
      <video id="{{ $widgetId }}_player"
             class="video-field__player"
             src="{{ $resolvedSrc }}"
             controls
             preload="metadata"
             playsinline></video>
      <div class="video-field__status">
        @if($isPrivate)
          <span class="video-field__badge video-field__badge--private" id="{{ $widgetId }}_badge">Private Storage</span>
        @else
          <span class="video-field__badge" id="{{ $widgetId }}_badge">Active Video Source</span>
        @endif
        <span class="video-field__source-text" id="{{ $widgetId }}_source_label">{{ $isPrivate ? basename(substr((string) $currentVal, 8)) : $currentVal }}</span>
      </div>
    </div>
  </div>

  @error('videos.'.$sectionKey)
    <div class="field__error">{{ $message }}</div>
  @enderror
  @error('sections.'.$sectionKey)
    <div class="field__error">{{ $message }}</div>
  @enderror
  @if($hint)
    <div class="field__hint">{{ $hint }}</div>
  @endif
</div>

<script>
(function () {
  var root = document.getElementById(@json($widgetId));
  if (!root || root.dataset.bound) return;
  root.dataset.bound = '1';

  var tabs = root.querySelectorAll('[data-video-mode]');
  var panes = root.querySelectorAll('[data-video-pane]');
  var fileInput = document.getElementById(@json($widgetId.'_file'));
  var linkInput = document.getElementById(@json($widgetId.'_link'));
  var drop = document.getElementById(@json($widgetId.'_drop'));
  var fileMeta = document.getElementById(@json($widgetId.'_file_meta'));
  var previewWrap = document.getElementById(@json($widgetId.'_preview_wrap'));
  var player = document.getElementById(@json($widgetId.'_player'));
  var badge = document.getElementById(@json($widgetId.'_badge'));
  var sourceLabel = document.getElementById(@json($widgetId.'_source_label'));

  function switchMode(mode) {
    tabs.forEach(function (t) {
      t.classList.toggle('is-active', t.getAttribute('data-video-mode') === mode);
    });
    panes.forEach(function (p) {
      p.classList.toggle('is-active', p.getAttribute('data-video-pane') === mode);
    });
  }

  tabs.forEach(function (btn) {
    btn.addEventListener('click', function () {
      switchMode(btn.getAttribute('data-video-mode'));
    });
  });

  if (fileInput) {
    fileInput.addEventListener('change', function () {
      var f = fileInput.files && fileInput.files[0];
      if (!f) return;
      var mb = (f.size / (1024 * 1024)).toFixed(2);
      if (fileMeta) fileMeta.textContent = f.name + ' (' + mb + ' MB) — Ready to upload to Private Storage on Save';
      if (player && previewWrap) {
        player.src = URL.createObjectURL(f);
        previewWrap.style.display = '';
      }
      if (badge) {
        badge.textContent = 'Pending Private Upload';
        badge.className = 'video-field__badge video-field__badge--private';
      }
      if (sourceLabel) sourceLabel.textContent = f.name + ' (' + mb + ' MB)';
    });
  }

  if (drop && fileInput) {
    ['dragenter', 'dragover'].forEach(function (ev) {
      drop.addEventListener(ev, function (e) { e.preventDefault(); drop.classList.add('is-dragover'); });
    });
    ['dragleave', 'drop'].forEach(function (ev) {
      drop.addEventListener(ev, function (e) { e.preventDefault(); drop.classList.remove('is-dragover'); });
    });
    drop.addEventListener('drop', function (e) {
      if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        fileInput.dispatchEvent(new Event('change'));
      }
    });
  }

  if (linkInput) {
    linkInput.addEventListener('input', function () {
      var v = linkInput.value.trim();
      if (fileInput) fileInput.value = '';
      if (v && (v.indexOf('https://') === 0 || v.indexOf('assets/') === 0)) {
        if (player && previewWrap) {
          player.src = v.indexOf('https://') === 0 ? v : ('/' + v.replace(/^\/+/, ''));
          previewWrap.style.display = '';
        }
        if (badge) {
          badge.textContent = 'Direct Link';
          badge.className = 'video-field__badge';
        }
        if (sourceLabel) sourceLabel.textContent = v;
      }
    });
  }
})();
</script>
