{{-- Per-row Edit/Delete controls rendered inside DataTables rows. --}}
<a class="btn btn--ghost btn--sm" href="{{ $edit }}">Edit</a>
<form method="post" action="{{ $delete }}" data-confirm="Delete this {{ $label }}?" data-confirm-title="Delete {{ ucfirst($label) }}" style="display:inline">
  @csrf @method('DELETE')
  <button class="btn btn--danger btn--sm" type="submit">Delete</button>
</form>
