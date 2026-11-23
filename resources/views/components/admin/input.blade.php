{{--
  Text input field.
  Props: name, label, type (text), value, hint, required, placeholder
--}}
@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null, 'required' => false, 'placeholder' => ''])
<div class="field">
  <label for="{{ $name }}">{{ $label }} @if($required)<span style="color:var(--accent)">*</span>@endif</label>
  <input class="input" type="{{ $type }}" id="{{ $name }}" name="{{ $name }}"
         value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}/>
  @error($name)<div class="error">{{ $message }}</div>@enderror
  @if($hint)<div class="hint">{{ $hint }}</div>@endif
</div>
