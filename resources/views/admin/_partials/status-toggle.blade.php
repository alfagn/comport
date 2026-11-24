{{-- Interactive one-click status toggle switch rendered inside DataTables & cards. --}}
<button type="button"
        class="dt-status-toggle {{ $active ? 'is-on' : 'is-off' }}"
        data-status-toggle
        data-toggle-url="{{ route('admin.status.toggle', ['entity' => $entity, 'id' => $id]) }}"
        data-label-on="{{ $labelOn ?? 'Live' }}"
        data-label-off="{{ $labelOff ?? 'Hidden' }}"
        role="switch"
        aria-checked="{{ $active ? 'true' : 'false' }}"
        title="Click to switch between {{ $labelOn ?? 'Live' }} and {{ $labelOff ?? 'Hidden' }}">
  <span class="dt-status-toggle__switch" aria-hidden="true">
    <span class="dt-status-toggle__knob"></span>
  </span>
  <span class="dt-status-toggle__label">{{ $active ? ($labelOn ?? 'Live') : ($labelOff ?? 'Hidden') }}</span>
</button>
