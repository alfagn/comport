@extends('layouts.admin')

@section('title', 'Site Settings')
@section('crumb', 'Global brand identity, contact desks, social channels & cookie consent')

@section('content')
@php
  $settingLabels = [
    'site_name' => 'Company / Site Name',
    'site_domain' => 'Official Website Domain (URL)',
    'tagline' => 'Studio Tagline',
    'email' => 'Primary / New Business Email',
    'partners_email' => 'Partnerships Desk Email',
    'press_email' => 'Press & Media Email',
    'talent_email' => 'Careers & Talent Email',
    'phone' => 'Executive Telephone',
    'address' => 'Headquarters Address',
    'facebook' => 'Facebook Page URL',
    'linkedin' => 'LinkedIn Profile URL',
    'instagram' => 'Instagram Profile URL',
    'x' => 'X (Twitter) Profile URL',
    'youtube' => 'YouTube Channel URL',
    'github' => 'GitHub Organization URL',
  ];
@endphp

<form method="post" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}">
  @csrf @method('PUT')

  <div class="card card--form">
    <h2 class="card__title">Official Domain &amp; Brand Assets</h2>
    <p class="card__sub">Primary domain for Empetix, plus the favicon and logos displayed across the public website and admin dashboard.</p>
    <div class="form-grid">
      <div class="full">
        <x-admin.input
          name="settings[site_domain]"
          label="Official Website Domain (URL)"
          :value="setting('site_domain', setting('seo_canonical_base', 'https://empetix.com'))"
          placeholder="https://empetix.com"
          hint="Primary public domain used across canonical links, sitemaps, and studio showcase badges."
        />
      </div>
      <div class="full"><x-admin.image name="brand_favicon" label="Favicon" :current="setting('brand_favicon') ?: 'assets/img/favicon.png'" hint="PNG, SVG or ICO, square. Used as the browser tab icon everywhere."/></div>
      <x-admin.image name="brand_logo" label="Site logo (light / inverse)" :current="setting('brand_logo') ?: 'assets/img/logo/empetix-logo.png'"/>
      <x-admin.image name="brand_logo_dark" label="Site logo (header / dark ink)" :current="setting('brand_logo_dark') ?: 'assets/img/logo/empetix-logo-dark.png'"/>
    </div>
  </div>

  <div class="card card--form">
    <h2 class="card__title">Cookie Consent &amp; Privacy</h2>
    <p class="card__sub">Ask visitors whether they allow optional cookies to be saved on their device, and customize the consent prompt and footer Cookie Settings modal.</p>
    <div class="form-grid">
      <div class="full">
        <x-admin.toggle
          name="settings[cookie_consent_enabled]"
          label="Show cookie consent message to new visitors"
          :checked="setting('cookie_consent_enabled', '1') !== '0'"
        />
      </div>
      <x-admin.input
        name="settings[cookie_banner_title]"
        label="Consent banner heading"
        :value="setting('cookie_banner_title', 'Cookie & Privacy Preferences')"
      />
      <x-admin.input
        name="settings[cookie_settings_label]"
        label="Footer & modal trigger label"
        :value="setting('cookie_settings_label', 'Cookie Settings')"
      />
      <div class="full">
        <x-admin.textarea
          name="settings[cookie_banner_message]"
          label="Consent banner message"
          rows="3"
          :value="setting('cookie_banner_message', 'We use strictly necessary cookies to keep Empetix secure and reliable, and optional analytics and marketing cookies to understand how visitors use our site. Do you allow us to store optional cookies on your device?')"
        />
      </div>
      <x-admin.input
        name="settings[cookie_accept_label]"
        label="Accept all button label"
        :value="setting('cookie_accept_label', 'Accept All Cookies')"
      />
      <x-admin.input
        name="settings[cookie_reject_label]"
        label="Decline / essential-only button label"
        :value="setting('cookie_reject_label', 'Essential Only')"
      />
    </div>
  </div>

  @foreach($groups as $group => $settings)
    <div class="card card--form">
      <h2 class="card__title">
        @switch($group)
          @case('general') General Identity @break
          @case('contact') Studio Contact Desks @break
          @case('social') Social Media Channels @break
          @default {{ ucfirst($group) }}
        @endswitch
      </h2>
      <p class="card__sub">
        @switch($group)
          @case('contact') Direct emails, executive phone line, and headquarters address displayed in the footer and contact page. @break
          @case('social') Official social profile URLs linked in the footer and schema metadata. @break
          @default Core studio naming and tagline across the site.
        @endswitch
      </p>
      <div class="form-grid">
        @if($group === 'social' && ! $settings->contains('key', 'facebook'))
          <div>
            <x-admin.input
              name="settings[facebook]"
              label="Facebook Page URL"
              :value="setting('facebook', 'https://www.facebook.com')"
              placeholder="https://www.facebook.com/empetix"
            />
          </div>
        @endif
        @foreach($settings as $setting)
          @php($isWide = in_array($setting->key, ['address', 'tagline'], true))
          <div class="{{ $isWide ? 'full' : '' }}">
            <x-admin.input
              :name="'settings['.$setting->key.']'"
              :label="$settingLabels[$setting->key] ?? ucwords(str_replace('_', ' ', $setting->key))"
              :value="$setting->value"
            />
          </div>
        @endforeach
      </div>
    </div>
  @endforeach

  <div class="form-actions">
    <button class="btn btn--primary" type="submit">Save settings</button>
  </div>
</form>
@endsection
