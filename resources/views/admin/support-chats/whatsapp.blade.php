@extends('layouts.admin')

@section('title', 'WhatsApp Cloud API')
@section('crumb', 'Support Chats')

@section('actions')
  @if($whatsappConfigured)
    <form method="post" action="{{ route('admin.support-chats.whatsapp.subscribe') }}" style="margin:0;">
      @csrf
      <button type="submit" class="btn btn--primary btn--sm">⚡ Activate WABA Webhook</button>
    </form>
  @endif
@endsection

@section('content')
{{-- Webhook Status & Endpoint Hero --}}
<div class="card" style="background:#0E0C0A;border-color:rgba(255,255,255,0.09);color:#fff;margin-bottom:22px;">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:14px;">
    <div>
      <h2 class="card__title" style="color:#fff;font-size:1.08rem;">Meta Webhook Endpoint &amp; Connection Status</h2>
      <p class="card__sub" style="color:rgba(255,255,255,0.58);margin-bottom:0;">Paste these values in Meta Developers &rarr; WhatsApp &rarr; Configuration &rarr; Webhook, subscribe to <code>messages</code>, then click Activate WABA Webhook.</p>
    </div>
    <span class="pill" style="{{ $whatsappConfigured ? 'background:rgba(37,211,102,0.18);color:#25D366;border:1px solid rgba(37,211,102,0.4);' : 'background:rgba(251,191,36,0.18);color:#fbbf24;' }}">
      {{ $whatsappConfigured ? ($whatsappEnabled ? '● WhatsApp Connected' : '⏸ Paused') : '⚠ Setup Needed' }}
    </span>
  </div>

  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:12px;">
    <div style="padding:12px 15px;border-radius:12px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.09);">
      <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.08em;color:rgba(255,255,255,0.5);margin-bottom:4px;">Callback URL</div>
      <code style="font-size:0.84rem;color:#fff;user-select:all;word-break:break-all;">{{ $whatsappWebhookUrl }}</code>
    </div>
    <div style="padding:12px 15px;border-radius:12px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.09);">
      <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.08em;color:rgba(255,255,255,0.5);margin-bottom:4px;">Verify Token</div>
      <code style="font-size:0.84rem;color:#25D366;user-select:all;word-break:break-all;">{{ $whatsappVerifyToken }}</code>
    </div>
  </div>
</div>

{{-- Credentials Form --}}
<div class="card card--form">
  <h2 class="card__title">Meta Graph API Credentials</h2>
  <p class="card__sub">Overrides `.env` values when filled. Secret tokens remain encrypted/masked once saved.</p>

  <form method="post" action="{{ route('admin.support-chats.whatsapp.settings.update') }}">
    @csrf @method('PUT')
    <div class="form-grid">
      <div class="full">
        <x-admin.toggle
          name="whatsapp_enabled"
          label="Enable WhatsApp AI Customer Support webhook, auto-replies &amp; website WhatsApp icon"
          :checked="$whatsappEnabled"
        />
      </div>

      <x-admin.input
        name="whatsapp_public_number"
        label="Public WhatsApp Number (Website Icon wa.me)"
        :value="setting('whatsapp_public_number', config('assistant.whatsapp.public_number'))"
        placeholder="e.g. +15551414650 or +201150949593"
        hint="Number opened when website visitors click the green WhatsApp icon."
      />

      <x-admin.input
        name="whatsapp_prefill_message"
        label="Website WhatsApp Welcome Prefill Text"
        :value="setting('whatsapp_prefill_message', config('assistant.whatsapp.prefill_message'))"
        placeholder="مرحباً Empetix، أريد الاستفسار عن خدماتكم."
      />

      <x-admin.input
        name="whatsapp_phone_number_id"
        label="Phone Number ID"
        :value="setting('whatsapp_phone_number_id', config('assistant.whatsapp.phone_number_id'))"
        placeholder="e.g. 1408285782376279"
      />

      <x-admin.input
        name="whatsapp_business_account_id"
        label="WhatsApp Business Account ID (WABA)"
        :value="setting('whatsapp_business_account_id', config('assistant.whatsapp.business_account_id'))"
        placeholder="e.g. 1503826371669647"
      />

      <x-admin.input
        name="whatsapp_verify_token"
        label="Webhook Verify Token"
        :value="$whatsappVerifyToken"
        :required="true"
      />

      <x-admin.input
        name="whatsapp_api_version"
        label="Graph API Version"
        :value="setting('whatsapp_api_version', config('assistant.whatsapp.api_version', 'v22.0'))"
        placeholder="v22.0"
      />

      <x-admin.input
        name="whatsapp_access_token"
        type="password"
        label="System User / Temporary Access Token"
        placeholder="{{ $whatsappConfigured ? '•••••••••••••••• (Saved — leave blank to keep)' : 'EAA...' }}"
      />

      <x-admin.input
        name="whatsapp_app_secret"
        type="password"
        label="Meta App Secret (Optional HMAC Security)"
        placeholder="{{ (setting('whatsapp_app_secret') || config('assistant.whatsapp.app_secret')) ? '•••••••••••••••• (Saved)' : 'Leave blank during Quickstart testing' }}"
      />
    </div>

    <div class="form-actions">
      <button class="btn btn--primary" type="submit">Save WhatsApp API Settings</button>
    </div>
  </form>
</div>
@endsection
