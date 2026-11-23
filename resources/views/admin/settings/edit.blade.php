@extends('layouts.admin')

@section('title', 'Site Settings')
@section('crumb', 'Global details used across the site')

@section('content')
<form method="post" action="{{ route('admin.settings.update') }}">
  @csrf @method('PUT')

  @foreach($groups as $group => $settings)
    <div class="card">
      <h2 class="card__title">{{ ucfirst($group) }}</h2>
      <p class="card__sub">
        @switch($group)
          @case('contact') How clients reach the studio. @break
          @case('social') Social profile links. @break
          @case('brand') Names and taglines. @break
          @default General configuration.
        @endswitch
      </p>
      <div class="form-grid">
        @foreach($settings as $setting)
          <x-admin.input :name="'settings['.$setting->key.']'" :label="ucwords(str_replace('_', ' ', $setting->key))" :value="$setting->value"/>
        @endforeach
      </div>
    </div>
  @endforeach

  <div class="form-actions">
    <button class="btn btn--primary" type="submit">Save settings</button>
  </div>
</form>
@endsection
