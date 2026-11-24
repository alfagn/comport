@extends('layouts.admin')

@section('title', 'Edit: '.ucfirst($page))
@section('crumb', 'Pages & Content')

@section('content')
@php
  $editableSections = $sections->where('type', '!=', 'internal')->values();
  $publicRoutes = [
      'home' => '/',
      'about' => '/about',
      'work' => '/work',
      'team' => '/team',
      'careers' => '/careers',
      'partners' => '/partners',
      'blog' => '/blog',
      'contact' => '/contact',
  ];
  $liveUrl = isset($publicRoutes[$page]) ? url($publicRoutes[$page]) : null;
  $cardGroups = $cardGroups ?? [];
@endphp

<div class="card card--form">
  <div class="form-card__head">
    <div class="form-card__intro">
      <span class="form-card__icon" aria-hidden="true">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="18" height="18" rx="3"/>
          <path d="M3 9h18M9 21V9"/>
        </svg>
      </span>
      <div>
        <div class="form-card__title-row">
          <h2 class="card__title">{{ ucfirst($page) }} content blocks</h2>
          <span class="form-card__count">{{ $editableSections->count() }} {{ Str::plural('field', $editableSections->count()) }}</span>
        </div>
        <p class="card__sub">Changes go live on the public site as soon as you save.</p>
      </div>
    </div>

    <div class="form-card__head-actions" style="display:flex;align-items:center;gap:12px">
      @if(isset(\App\Services\PageStatusService::PAGES[$page]))
        @include('admin._partials.status-toggle', [
          'entity' => 'pages',
          'id' => $page,
          'active' => page_active($page),
          'labelOn' => 'Live',
          'labelOff' => 'Hidden',
        ])
      @endif

      @if($liveUrl)
        <a class="btn btn--ghost btn--sm" href="{{ $liveUrl }}" target="_blank" rel="noopener">
          <span>View live page</span>
          <span aria-hidden="true">↗</span>
        </a>
      @endif
    </div>
  </div>

  <form method="post" enctype="multipart/form-data" action="{{ route('admin.pages.update', $page) }}">
    @csrf @method('PUT')

    <div class="form-grid form-grid--editorial">
      @foreach($editableSections as $section)
        @php
          $isFullWidth = in_array($section->type, ['textarea', 'image', 'video'], true)
              || mb_strlen((string) $section->value) > 85;
        @endphp

        @if($section->type === 'textarea')
          <div class="full">
            <x-admin.textarea :name="'sections['.$section->key.']'" :label="$section->label" :value="$section->value" rows="4"/>
          </div>
        @elseif($section->type === 'image')
          <div class="full">
            <x-admin.image :name="'images['.$section->key.']'" :label="$section->label" :current="$section->value"/>
          </div>
        @elseif($section->type === 'video')
          <div class="full">
            <x-admin.video :sectionKey="$section->key" :label="$section->label" :value="$section->value"/>
          </div>
        @elseif($section->type === 'number')
          <div class="{{ $isFullWidth ? 'full' : '' }}">
            <x-admin.input :name="'sections['.$section->key.']'" :label="$section->label" type="number" step="any" :value="$section->value"/>
          </div>
        @else
          <div class="{{ $isFullWidth ? 'full' : '' }}">
            <x-admin.input :name="'sections['.$section->key.']'" :label="$section->label" :value="$section->value"/>
          </div>
        @endif
      @endforeach
    </div>

    @if(!empty($cardGroups))
      <div class="cms-card-sections">
        @foreach($cardGroups as $groupKey => $group)
          @php
            $groupDomId = 'cards_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $groupKey);
          @endphp
          <section class="cms-cards-block" data-cards-group="{{ $groupKey }}" id="{{ $groupDomId }}">
            <div class="cms-cards-block__head">
              <div>
                <h3 class="cms-cards-block__title">{{ $group['title'] }}</h3>
                <p class="cms-cards-block__sub">{{ $group['subtitle'] }}</p>
              </div>
              <button type="button"
                      class="btn btn--ghost btn--sm cms-cards-block__add"
                      data-add-card="{{ $groupDomId }}"
                      data-group-key="{{ $groupKey }}"
                      data-has-meta="{{ !empty($group['has_meta']) ? '1' : '0' }}"
                      data-meta-label="{{ $group['meta_label'] ?? 'Badge' }}"
                      data-meta-placeholder="{{ $group['meta_placeholder'] ?? '' }}">
                <span>+ {{ $group['add_label'] ?? 'Add Card' }}</span>
              </button>
            </div>

            {{-- Ensure empty array can still clear a group if all cards are removed --}}
            <input type="hidden" name="lists[{{ $groupKey }}][__present][title]" value=""/>

            <div class="cms-cards-grid" data-cards-list>
              @foreach($group['items'] as $idx => $cardItem)
                <div class="cms-card-editor" data-card-item>
                  <div class="cms-card-editor__top">
                    <span class="cms-card-editor__num" data-card-num>Card {{ str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT) }}</span>
                    <button type="button" class="cms-card-editor__remove" data-remove-card title="Remove card">× Remove</button>
                  </div>

                  @if(!empty($group['has_meta']))
                    <div class="field">
                      <label>{{ $group['meta_label'] ?? 'Year / Badge' }}</label>
                      <input class="input"
                             type="text"
                             name="lists[{{ $groupKey }}][{{ $idx }}][meta]"
                             value="{{ $cardItem->meta }}"
                             placeholder="{{ $group['meta_placeholder'] ?? '2025' }}"/>
                    </div>
                  @endif

                  <div class="field">
                    <label>Card Title</label>
                    <input class="input"
                           type="text"
                           name="lists[{{ $groupKey }}][{{ $idx }}][title]"
                           value="{{ $cardItem->title }}"
                           placeholder="Card heading"/>
                  </div>

                  <div class="field">
                    <label>Card Description</label>
                    <textarea class="textarea"
                              rows="3"
                              name="lists[{{ $groupKey }}][{{ $idx }}][body]"
                              placeholder="Supporting copy">{{ $cardItem->body }}</textarea>
                  </div>
                </div>
              @endforeach
            </div>
          </section>
        @endforeach
      </div>
    @endif

    <div class="form-actions">
      <button class="btn btn--primary" type="submit">Save {{ $page }} content</button>
      <a class="btn btn--ghost" href="{{ route('admin.pages.index') }}">Back to pages</a>
    </div>
  </form>
</div>

@if(!empty($cardGroups))
<script>
(function () {
  function renumber(block) {
    block.querySelectorAll('[data-card-item]').forEach(function (card, idx) {
      var badge = card.querySelector('[data-card-num]');
      if (badge) badge.textContent = 'Card ' + String(idx + 1).padStart(2, '0');
    });
  }

  document.querySelectorAll('[data-cards-group]').forEach(function (block) {
    block.addEventListener('click', function (e) {
      var rm = e.target.closest('[data-remove-card]');
      if (rm) {
        var card = rm.closest('[data-card-item]');
        if (card) {
          card.remove();
          renumber(block);
        }
      }
    });
  });

  document.querySelectorAll('[data-add-card]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var block = document.getElementById(btn.getAttribute('data-add-card'));
      if (!block) return;
      var list = block.querySelector('[data-cards-list]');
      var groupKey = btn.getAttribute('data-group-key');
      var hasMeta = btn.getAttribute('data-has-meta') === '1';
      var metaLabel = btn.getAttribute('data-meta-label') || 'Year / Badge';
      var metaPlaceholder = btn.getAttribute('data-meta-placeholder') || '';
      var uid = 'new_' + Date.now() + '_' + Math.floor(Math.random() * 1000);

      var el = document.createElement('div');
      el.className = 'cms-card-editor';
      el.setAttribute('data-card-item', '');
      el.innerHTML =
        '<div class="cms-card-editor__top">' +
          '<span class="cms-card-editor__num" data-card-num>New Card</span>' +
          '<button type="button" class="cms-card-editor__remove" data-remove-card title="Remove card">× Remove</button>' +
        '</div>' +
        (hasMeta
          ? '<div class="field"><label></label><input class="input" type="text" name="lists[' + groupKey + '][' + uid + '][meta]" placeholder="' + metaPlaceholder.replace(/"/g, '&quot;') + '"/></div>'
          : '') +
        '<div class="field"><label>Card Title</label><input class="input" type="text" name="lists[' + groupKey + '][' + uid + '][title]" placeholder="Card heading"/></div>' +
        '<div class="field"><label>Card Description</label><textarea class="textarea" rows="3" name="lists[' + groupKey + '][' + uid + '][body]" placeholder="Supporting copy"></textarea></div>';

      if (hasMeta) {
        el.querySelector('.field label').textContent = metaLabel;
      }
      list.appendChild(el);
      renumber(block);
      var firstInput = el.querySelector('input');
      if (firstInput) firstInput.focus();
    });
  });
})();
</script>
@endif
@endsection
