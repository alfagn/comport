{{--
  Textarea field.
  Props: name, label, value, hint, rows, required
--}}
@props(['name', 'label', 'value' => null, 'hint' => null, 'rows' => 4, 'required' => false])
<div class="field">
  <label for="{{ $name }}">{{ $label }} @if($required)<span style="color:var(--accent)">*</span>@endif</label>
  <textarea class="textarea" id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" {{ $required ? 'required' : '' }}>{{ old($name, $value) }}</textarea>
  @error($name)<div class="error">{{ $message }}</div>@enderror
  @if($hint)<div class="hint">{{ $hint }}</div>@endif
</div>
