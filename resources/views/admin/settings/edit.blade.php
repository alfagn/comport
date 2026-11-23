@extends('layouts.admin')

@section('title', 'Site Settings')
@section('crumb', 'Global details used across the site')

@section('content')
<form method="post" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}">
  @csrf @method('PUT')

  <div class="card">
    <h2 class="card__title">Brand assets</h2>
    <p class="card__sub">Favicon and logos used across the site and this dashboard. Leave empty to keep the current ones.</p>
    <div class="form-grid">
      <div class="full"><x-admin.image name="brand_favicon" label="Favicon" :current="setting('brand_favicon') ?: 'assets/img/favicon.svg'" hint="PNG, SVG or ICO, square. Used as the browser tab icon everywhere."/></div>
      <x-admin.image name="brand_logo" label="Site logo" :current="setting('brand_logo') ?: 'assets/img/logo/empetix-logo.png'"/>
      <x-admin.image name="brand_logo_dark" label="Site logo (dark)" :current="setting('brand_logo_dark') ?: 'assets/img/logo/empetix-logo-dark.png'"/>
    </div>
  </div>

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
