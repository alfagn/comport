{{--
  On/off switch backed by a hidden 0 + checkbox 1 pair.
  Props: name, label, checked
--}}
@props(['name', 'label', 'checked' => false])
<div class="field">
  <label class="toggle">
    <input type="hidden" name="{{ $name }}" value="0"/>
    <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name, $checked))/>
    <span class="track"></span>
    <span class="toggle-label">{{ $label }}</span>
  </label>
</div>
