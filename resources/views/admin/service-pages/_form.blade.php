@php
  $isEdit = $item->exists;
  $rawBody = old('body', (string) ($item->body ?? ''));
  $oldSections = old('sections');

  if (is_array($oldSections)) {
      $parsedSections = collect($oldSections)
          ->map(fn ($s) => [
              'heading' => (string) ($s['heading'] ?? ''),
              'content' => (string) ($s['content'] ?? ''),
          ])
          ->values()
          ->all();
  } elseif (trim($rawBody) !== '') {
      $parsedSections = collect(preg_split('/\R{2,}/u', trim($rawBody)))
          ->map(function ($chunk) {
              $lines = preg_split('/\R/u', trim($chunk), 2);

              return [
                  'heading' => trim($lines[0] ?? ''),
                  'content' => trim($lines[1] ?? ''),
              ];
          })
          ->filter(fn ($s) => $s['heading'] !== '' || $s['content'] !== '')
          ->values()
          ->all();
  } else {
      $parsedSections = [
          ['heading' => '', 'content' => ''],
          ['heading' => '', 'content' => ''],
      ];
  }
@endphp

<div class="card card--form">
  <div class="form-card__head">
    <div class="form-card__intro">
      <span class="form-card__icon" aria-hidden="true">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" y1="13" x2="8" y2="13"/>
          <line x1="16" y1="17" x2="8" y2="17"/>
        </svg>
      </span>
      <div>
        <div class="form-card__title-row">
          <h2 class="card__title">{{ $isEdit ? $item->title : 'New Service Landing Page' }}</h2>
          @if($isEdit)
            <span class="form-card__count">{{ $item->is_published ? 'Published' : 'Draft' }}</span>
          @endif
        </div>
        <p class="card__sub">Define the service scope, numbered breakdown cards, and search metadata. Drafts remain private until published.</p>
      </div>
    </div>

    @if($isEdit)
      <div style="display:flex; flex-wrap:wrap; gap:8px;">
        <a class="btn btn--ghost btn--sm" href="{{ route('admin.service-pages.preview', $item->id) }}" target="_blank" rel="noopener">Private preview ↗</a>
        @if($item->is_published)
          <a class="btn btn--ghost btn--sm" href="{{ url($item->publicPath()) }}" target="_blank" rel="noopener">Live page ↗</a>
        @endif
      </div>
    @endif
  </div>

  <form method="post" action="{{ $isEdit ? route('admin.service-pages.update', $item->id) : route('admin.service-pages.store') }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-grid form-grid--editorial" dir="ltr">
      <x-admin.input
        name="title"
        label="Service Page Title"
        :value="$item->title"
        placeholder="e.g. Custom Mobile App Engineering"
        :required="true"
      />

      <x-admin.input
        name="slug"
        label="URL Slug (/services/...)"
        :value="$item->slug"
        placeholder="Auto-generated from title if left blank"
        hint="Lowercase letters, numbers, and hyphens (e.g. mobile-app-engineering)."
      />

      <div class="full">
        <x-admin.textarea
          name="summary"
          label="Hero Intro Summary"
          :value="$item->summary"
          rows="3"
          :required="true"
        />
      </div>
    </div>

    {{-- Interactive Numbered Section Cards Builder --}}
    <div class="cms-card-sections" dir="ltr">
      <section class="cms-cards-block" id="sp-sections-builder">
        <div class="cms-cards-block__head">
          <div>
            <h3 class="cms-cards-block__title">Service Breakdown Sections (Cards)</h3>
            <p class="cms-cards-block__sub">Each card renders as a numbered editorial block (01, 02, 03...) on the service page.</p>
          </div>
          <button type="button" class="btn btn--ghost btn--sm" id="sp-add-section-btn">
            + Add Section Card
          </button>
        </div>

        @error('body')
          <div class="error" style="margin-bottom:14px;">{{ $message }}</div>
        @enderror

        <div class="cms-cards-grid" id="sp-sections-list">
          @foreach($parsedSections as $idx => $sec)
            <div class="cms-card-editor" data-sp-card>
              <div class="cms-card-editor__top">
                <span class="cms-card-editor__num" data-sp-num>Section {{ str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT) }}</span>
                <button type="button" class="cms-card-editor__remove" data-sp-remove>× Remove</button>
              </div>
              <div class="field">
                <label>Section Heading</label>
                <input class="input"
                       type="text"
                       name="sections[{{ $idx }}][heading]"
                       value="{{ $sec['heading'] }}"
                       placeholder="e.g. Architecture & Technical Scope"/>
              </div>
              <div class="field">
                <label>Section Description</label>
                <textarea class="textarea"
                          rows="4"
                          name="sections[{{ $idx }}][content]"
                          placeholder="Explain deliverables, workflow, and technical approach...">{{ $sec['content'] }}</textarea>
              </div>
            </div>
          @endforeach
        </div>
      </section>
    </div>

    <div class="form-grid form-grid--editorial" dir="ltr" style="margin-top:26px;">
      <x-admin.input
        name="meta_title"
        label="SEO Meta Title (Optional)"
        :value="$item->meta_title"
        placeholder="Defaults to Service Page Title | Empetix"
      />

      <div class="full">
        <x-admin.textarea
          name="meta_description"
          label="SEO Meta Description (Optional)"
          :value="$item->meta_description"
          rows="2"
        />
      </div>

      <div class="full">
        <x-admin.toggle
          name="is_published"
          label="Publish this service page publicly on /services"
          :checked="(bool) $item->is_published"
        />
      </div>
    </div>

    <div class="form-actions">
      <button class="btn btn--primary" type="submit">{{ $isEdit ? 'Save service page' : 'Create service page' }}</button>
      <a href="{{ route('admin.service-pages.index') }}" class="btn btn--ghost" wire:navigate>Back to service pages</a>
    </div>
  </form>
</div>

<script>
(function () {
  var list = document.getElementById('sp-sections-list');
  var addBtn = document.getElementById('sp-add-section-btn');
  if (!list || !addBtn) return;

  function renumber() {
    list.querySelectorAll('[data-sp-card]').forEach(function (card, idx) {
      var num = card.querySelector('[data-sp-num]');
      if (num) num.textContent = 'Section ' + String(idx + 1).padStart(2, '0');
    });
  }

  list.addEventListener('click', function (e) {
    var rm = e.target.closest('[data-sp-remove]');
    if (!rm) return;
    var cards = list.querySelectorAll('[data-sp-card]');
    var card = rm.closest('[data-sp-card]');
    if (cards.length <= 1) {
      card.querySelectorAll('input, textarea').forEach(function (el) { el.value = ''; });
      return;
    }
    card.remove();
    renumber();
  });

  addBtn.addEventListener('click', function () {
    var uid = 'new_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
    var el = document.createElement('div');
    el.className = 'cms-card-editor';
    el.setAttribute('data-sp-card', '');
    el.innerHTML =
      '<div class="cms-card-editor__top">' +
        '<span class="cms-card-editor__num" data-sp-num>New Section</span>' +
        '<button type="button" class="cms-card-editor__remove" data-sp-remove>× Remove</button>' +
      '</div>' +
      '<div class="field">' +
        '<label>Section Heading</label>' +
        '<input class="input" type="text" name="sections[' + uid + '][heading]" placeholder="e.g. Delivery & Milestones"/>' +
      '</div>' +
      '<div class="field">' +
        '<label>Section Description</label>' +
        '<textarea class="textarea" rows="4" name="sections[' + uid + '][content]" placeholder="Explain deliverables, workflow, and technical approach..."></textarea>' +
      '</div>';
    list.appendChild(el);
    renumber();
    var first = el.querySelector('input');
    if (first) first.focus();
  });
})();
</script>
