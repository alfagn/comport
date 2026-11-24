{{-- Per-row Edit/Delete controls rendered inside DataTables rows. --}}
<div class="dt-actions">
  <a class="btn btn--ghost btn--sm dt-btn" href="{{ $edit }}">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
    </svg>
    <span>Edit</span>
  </a>
  <form method="post" action="{{ $delete }}" data-confirm="Delete this {{ $label }}?" data-confirm-title="Delete {{ ucfirst($label) }}" class="dt-inline-form">
    @csrf @method('DELETE')
    <button class="btn btn--danger btn--sm dt-btn" type="submit">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
      </svg>
      <span>Delete</span>
    </button>
  </form>
</div>
