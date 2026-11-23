{{--
  Select field. Props: name, label, options (assoc value=>label), value, hint
--}}
@props(['name', 'label', 'options' => [], 'value' => null, 'hint' => null])
<div class="field">
  <label for="{{ $name }}">{{ $label }}</label>
  <select class="select" id="{{ $name }}" name="{{ $name }}">
    @foreach($options as $optValue => $optLabel)
      <option value="{{ $optValue }}" @selected(old($name, $value) == $optValue)>{{ $optLabel }}</option>
    @endforeach
  </select>
  @error($name)<div class="error">{{ $message }}</div>@enderror
  @if($hint)<div class="hint">{{ $hint }}</div>@endif
</div>
