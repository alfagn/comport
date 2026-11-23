{{--
  Drag & Drop Media (Image/Video) upload component with high-end live preview.
  Props: name, label, current (path or URL), hint, accept (default 'image/*')
--}}
@props(['name', 'label', 'current' => null, 'hint' => null, 'accept' => 'image/*'])
@php
  // Uploaded files live on the public disk; seeded content points at /assets.
  $src = $current
      ? (str_starts_with($current, 'uploads/') ? asset('storage/'.$current) : asset($current))
      : null;
  $fieldId = 'uploader_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $name) . '_' . substr(md5($name), 0, 6);
  $isVideo = $current && preg_match('/\.(mp4|webm|mov|ogg)$/i', $current);
@endphp
<div class="field field--media" id="{{ $fieldId }}">
  @if($label)
    <label class="field__label">{{ $label }}</label>
  @endif

  <div class="media-uploader {{ $src ? 'has-media' : 'is-empty' }}" data-uploader-id="{{ $fieldId }}">
    {{-- Native input kept hidden, controlled via drag/drop and click --}}
    <input type="file"
           id="{{ $fieldId }}_input"
           name="{{ $name }}"
           accept="{{ $accept }}"
           class="media-uploader__native-input"
           style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);border:0;opacity:0;"/>

    @if($current)
      <input type="hidden" name="{{ $name }}_current" value="{{ $current }}" id="{{ $fieldId }}_current"/>
    @endif

    {{-- Dropzone Area: visible when empty OR as an overlay drop target --}}
    <div class="media-dropzone" id="{{ $fieldId }}_dropzone" role="button" tabindex="0" aria-label="Upload media file">
      <div class="media-dropzone__glow"></div>
      <div class="media-dropzone__icon-wrap">
        <img class="media-dropzone__3d-icon" src="{{ asset('assets/images/3d/3d-image.png') }}" alt="" width="44" height="44" loading="lazy">
        <div class="media-dropzone__plus-badge">+</div>
      </div>
      <div class="media-dropzone__text">
        <p class="media-dropzone__title">
          <span class="media-dropzone__highlight">Drag & drop</span> your image here, or <span class="media-dropzone__browse">Browse files</span>
        </p>
        <p class="media-dropzone__sub">
          @if($hint)
            {{ $hint }}
          @else
            Supports PNG, JPG, WEBP, SVG or GIF (up to 4MB)
          @endif
        </p>
      </div>
    </div>

    {{-- Live Preview Card: visible when file exists (current or freshly dropped) --}}
    <div class="media-preview-card" id="{{ $fieldId }}_preview_card" style="{{ $src ? '' : 'display:none;' }}">
      <div class="media-preview-card__visual">
        <div class="media-preview-card__checker"></div>
        <img src="{{ $src ?? '' }}"
             alt="{{ $label }}"
             id="{{ $fieldId }}_preview_img"
             class="media-preview-card__img"
             style="{{ $isVideo ? 'display:none;' : '' }}"/>
        <video src="{{ $src ?? '' }}"
               id="{{ $fieldId }}_preview_video"
               class="media-preview-card__video"
               controls
               style="{{ $isVideo ? '' : 'display:none;' }}"></video>
        <div class="media-preview-card__overlay">
          <span class="media-preview-card__drop-hint">Drop new file to replace</span>
        </div>
      </div>

      <div class="media-preview-card__meta">
        <div class="media-preview-card__info">
          <span class="media-preview-card__badge" id="{{ $fieldId }}_badge">{{ $src ? 'Current Asset' : 'New Selection' }}</span>
          <span class="media-preview-card__name" id="{{ $fieldId }}_filename" title="{{ $current ?? '' }}">{{ $current ? basename($current) : '' }}</span>
          <span class="media-preview-card__size" id="{{ $fieldId }}_filesize"></span>
        </div>
        <div class="media-preview-card__actions">
          <button type="button" class="btn-media btn-media--change" id="{{ $fieldId }}_btn_change" title="Choose another file">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <span>Replace</span>
          </button>
          <button type="button" class="btn-media btn-media--remove" id="{{ $fieldId }}_btn_remove" title="Clear selection">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            <span>Reset</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  @error($name)
    <div class="field__error">{{ $message }}</div>
  @enderror
</div>

<script>
(function() {
  const container = document.getElementById('{{ $fieldId }}');
  if (!container || container.dataset.initialized) return;
  container.dataset.initialized = 'true';

  const uploader = container.querySelector('.media-uploader');
  const fileInput = document.getElementById('{{ $fieldId }}_input');
  const dropzone = document.getElementById('{{ $fieldId }}_dropzone');
  const previewCard = document.getElementById('{{ $fieldId }}_preview_card');
  const previewImg = document.getElementById('{{ $fieldId }}_preview_img');
  const previewVideo = document.getElementById('{{ $fieldId }}_preview_video');
  const badge = document.getElementById('{{ $fieldId }}_badge');
  const filenameEl = document.getElementById('{{ $fieldId }}_filename');
  const filesizeEl = document.getElementById('{{ $fieldId }}_filesize');
  const btnChange = document.getElementById('{{ $fieldId }}_btn_change');
  const btnRemove = document.getElementById('{{ $fieldId }}_btn_remove');
  const originalSrc = @json($src);
  const originalName = @json($current ? basename($current) : '');

  function formatBytes(bytes) {
    if (!bytes || bytes === 0) return '';
    const k = 1024, dm = 1;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
  }

  function handleFile(file) {
    if (!file) return;
    const isVid = file.type.startsWith('video/');
    const url = URL.createObjectURL(file);

    if (isVid) {
      previewImg.style.display = 'none';
      previewVideo.style.display = 'block';
      previewVideo.src = url;
    } else {
      previewVideo.style.display = 'none';
      previewImg.style.display = 'block';
      previewImg.src = url;
    }

    badge.textContent = 'New Asset (Ready)';
    badge.className = 'media-preview-card__badge media-preview-card__badge--new';
    filenameEl.textContent = file.name;
    filenameEl.title = file.name;
    filesizeEl.textContent = ' · ' + formatBytes(file.size);

    previewCard.style.display = 'flex';
    uploader.classList.add('has-media');
    uploader.classList.remove('is-empty');
  }

  function openFileDialog() {
    fileInput.click();
  }

  if (dropzone) dropzone.addEventListener('click', openFileDialog);
  if (btnChange) btnChange.addEventListener('click', openFileDialog);

  // Drag and drop events on the whole uploader
  ['dragenter', 'dragover'].forEach(evt => {
    uploader.addEventListener(evt, (e) => {
      e.preventDefault();
      e.stopPropagation();
      uploader.classList.add('is-dragover');
    });
  });

  ['dragleave', 'dragend'].forEach(evt => {
    uploader.addEventListener(evt, (e) => {
      e.preventDefault();
      e.stopPropagation();
      uploader.classList.remove('is-dragover');
    });
  });

  uploader.addEventListener('drop', (e) => {
    e.preventDefault();
    e.stopPropagation();
    uploader.classList.remove('is-dragover');
    const files = e.dataTransfer.files;
    if (files && files.length > 0) {
      try {
        fileInput.files = files;
      } catch (err) {
        // Fallback for older browsers
        const dt = new DataTransfer();
        dt.items.add(files[0]);
        fileInput.files = dt.files;
      }
      handleFile(files[0]);
    }
  });

  fileInput.addEventListener('change', () => {
    if (fileInput.files && fileInput.files.length > 0) {
      handleFile(fileInput.files[0]);
    }
  });

  if (btnRemove) {
    btnRemove.addEventListener('click', (e) => {
      e.stopPropagation();
      fileInput.value = '';
      if (originalSrc) {
        // Revert to original
        const isVid = /\.(mp4|webm|mov|ogg)$/i.test(originalSrc);
        if (isVid) {
          previewImg.style.display = 'none';
          previewVideo.style.display = 'block';
          previewVideo.src = originalSrc;
        } else {
          previewVideo.style.display = 'none';
          previewImg.style.display = 'block';
          previewImg.src = originalSrc;
        }
        badge.textContent = 'Current Asset';
        badge.className = 'media-preview-card__badge';
        filenameEl.textContent = originalName;
        filenameEl.title = originalName;
        filesizeEl.textContent = '';
        previewCard.style.display = 'flex';
        uploader.classList.add('has-media');
      } else {
        // Clear completely
        previewImg.src = '';
        previewVideo.src = '';
        previewCard.style.display = 'none';
        uploader.classList.remove('has-media');
        uploader.classList.add('is-empty');
      }
    });
  }
})();
</script>
