{{--
  Image upload with current-image preview.
  Props: name, label, current (path or URL), hint
--}}
@props(['name', 'label', 'current' => null, 'hint' => null])
@php
  // Uploaded files live on the public disk; seeded content points at /assets.
  $src = $current
      ? (str_starts_with($current, 'uploads/') ? asset('storage/'.$current) : asset($current))
      : null;
@endphp
<div class="field">
  <label>{{ $label }}</label>
  <div class="image-field">
    @if($src)
      <img src="{{ $src }}" alt="{{ $label }}" id="{{ $name }}-preview"/>
    @else
      <div class="placeholder" id="{{ $name }}-preview">No image</div>
    @endif
    <div style="flex:1">
      <input class="input" type="file" id="{{ $name }}" name="{{ $name }}" accept="image/*"
             onchange="const f=this.files[0];if(f){const p=document.getElementById('{{ $name }}-preview');const i=document.createElement('img');i.id='{{ $name }}-preview';i.src=URL.createObjectURL(f);p.replaceWith(i);}"/>
      @if($current)
        <input type="hidden" name="{{ $name }}_current" value="{{ $current }}"/>
        <div class="hint">Current: {{ $current }} — choose a file to replace it.</div>
      @endif
      @error($name)<div class="error">{{ $message }}</div>@enderror
      @if($hint)<div class="hint">{{ $hint }}</div>@endif
    </div>
  </div>
</div>
