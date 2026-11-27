@extends('layouts.admin')

@section('title', 'System Settings')
@section('crumb', 'Unified configuration hub for General Identity, WhatsApp Cloud API & AI Chatbot')

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

  $providerOptions = [
    '' => 'Default (from .env AI_PROVIDER)',
    'openai' => 'OpenAI',
    'gemini' => 'Google Gemini',
    'anthropic' => 'Anthropic Claude',
    'groq' => 'Groq',
    'deepseek' => 'DeepSeek',
    'mistral' => 'Mistral',
    'openrouter' => 'OpenRouter',
    'xai' => 'xAI (Grok)',
    'ollama' => 'Ollama (Local)',
    'openai-compatible' => 'OpenAI-Compatible Endpoint',
  ];

  $webWidgetLive = setting('ai_support_enabled', '1') !== '0';
@endphp

<style>
  .sys-hub-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 4px;
  }
  .sys-hub-card {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 18px;
    padding: 26px 26px 22px;
    border-radius: 20px;
    background: var(--surface);
    border: 1px solid var(--line-strong);
    box-shadow: 0 12px 32px rgba(10, 16, 24, 0.06);
    cursor: pointer;
    text-align: left;
    transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
  }
  .sys-hub-card:hover {
    transform: translateY(-3px);
    border-color: rgba(255, 77, 28, 0.45);
    box-shadow: 0 20px 44px rgba(10, 16, 24, 0.12);
  }
  .sys-hub-card--wa:hover {
    border-color: rgba(37, 211, 102, 0.55);
  }
  .sys-hub-card--ai:hover {
    border-color: rgba(124, 58, 237, 0.55);
  }
  .sys-hub-card__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
  }
  .sys-hub-card__icon {
    width: 50px;
    height: 50px;
    border-radius: 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    flex-shrink: 0;
  }
  .sys-hub-card__title {
    margin: 14px 0 6px;
    font-family: var(--font-display);
    font-size: 1.18rem;
    font-weight: 800;
    color: var(--text);
  }
  .sys-hub-card__desc {
    margin: 0;
    font-size: 0.87rem;
    line-height: 1.58;
    color: var(--text-muted);
  }
  .sys-hub-card__foot {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 14px;
    border-top: 1px solid var(--line);
    font-size: 0.83rem;
    font-weight: 700;
    color: var(--text);
  }
  .sys-section-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;
    padding: 12px 16px;
    border-radius: 16px;
    background: var(--surface);
    border: 1px solid var(--line-strong);
  }
  .sys-section-tabs {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }
  .sys-tab-pill {
    border: 1px solid var(--line);
    background: transparent;
    color: var(--text-muted);
    font-family: var(--font-body);
    font-size: 0.8rem;
    font-weight: 700;
    padding: 6px 13px;
    border-radius: 999px;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .sys-tab-pill.is-active {
    background: var(--text);
    color: var(--surface);
    border-color: var(--text);
  }
  [data-settings-hub][hidden],
  [data-settings-panel][hidden] {
    display: none !important;
  }
</style>

<div id="empetix-settings-root" data-initial-section="{{ $activeSection ?? '' }}">

  {{-- ==================== CARDS HUB VIEW (Default when clicking System Settings) ==================== --}}
  <div data-settings-hub @if($activeSection !== null) hidden @endif>
    <div class="sys-hub-grid">

      {{-- Card 1: General Settings --}}
      <button type="button" class="sys-hub-card" data-open-settings-card="general">
        <div>
          <div class="sys-hub-card__top">
            <span class="sys-hub-card__icon" style="background:linear-gradient(145deg,#FF4D1C,#EA580C);box-shadow:0 8px 20px rgba(255,77,28,0.28);">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            </span>
            <span class="pill">{{ setting('site_domain', 'empetix.com') }}</span>
          </div>
          <h2 class="sys-hub-card__title">General Settings</h2>
          <p class="sys-hub-card__desc">Official domain, brand logos &amp; favicon, studio contact desks, phone &amp; HQ address, social links, and cookie privacy banner.</p>
        </div>
        <div class="sys-hub-card__foot">
          <span>Open General Settings</span>
          <span aria-hidden="true">&rarr;</span>
        </div>
      </button>

      {{-- Card 2: WhatsApp Settings --}}
      <button type="button" class="sys-hub-card sys-hub-card--wa" data-open-settings-card="whatsapp">
        <div>
          <div class="sys-hub-card__top">
            <span class="sys-hub-card__icon" style="background:linear-gradient(145deg,#25D366,#128C7E);box-shadow:0 8px 20px rgba(37,211,102,0.28);">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20.52 3.48A11.82 11.82 0 0 0 12.06 0C5.52 0 .2 5.32.2 11.86c0 2.09.55 4.13 1.59 5.93L0 24l6.38-1.67a11.83 11.83 0 0 0 5.68 1.45h.01c6.54 0 11.86-5.32 11.86-11.86 0-3.17-1.23-6.15-3.41-8.44ZM12.07 21.78h-.01a9.84 9.84 0 0 1-5.02-1.38l-.36-.21-3.79.99 1.01-3.69-.24-.38a9.83 9.83 0 0 1-1.51-5.25c0-5.44 4.43-9.87 9.88-9.87 2.64 0 5.12 1.03 6.98 2.9a9.81 9.81 0 0 1 2.89 6.98c0 5.44-4.43 9.91-9.83 9.91Zm5.41-7.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.65-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35Z"/></svg>
            </span>
            <span class="pill" style="{{ $whatsappConfigured ? 'background:rgba(37,211,102,0.16);color:#15803d;' : 'background:rgba(245,158,11,0.16);color:#b45309;' }}">
              {{ $whatsappConfigured ? ($whatsappEnabled ? '● Connected' : '⏸ Paused') : '⚠ Setup Needed' }}
            </span>
          </div>
          <h2 class="sys-hub-card__title">WhatsApp</h2>
          <p class="sys-hub-card__desc">Meta WhatsApp Cloud API credentials, Phone Number ID, Business Account ID, permanent System User token, and live webhook status.</p>
        </div>
        <div class="sys-hub-card__foot">
          <span>Open WhatsApp Settings</span>
          <span aria-hidden="true">&rarr;</span>
        </div>
      </button>

      {{-- Card 3: Chatbot & AI Settings --}}
      <button type="button" class="sys-hub-card sys-hub-card--ai" data-open-settings-card="chatbot">
        <div>
          <div class="sys-hub-card__top">
            <span class="sys-hub-card__icon" style="background:linear-gradient(145deg,#7C3AED,#4F46E5);box-shadow:0 8px 20px rgba(124,58,237,0.28);">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 10h.01"/><path d="M12 10h.01"/><path d="M16 10h.01"/></svg>
            </span>
            <span class="pill">{{ $webWidgetLive ? '● Active: '.strtoupper($providerName) : '⏸ Widget Off' }}</span>
          </div>
          <h2 class="sys-hub-card__title">Chatbot &amp; AI</h2>
          <p class="sys-hub-card__desc">Website AI Concierge greeting, public chat widget toggle, shared FAQ knowledge base, and LLM provider/model routing.</p>
        </div>
        <div class="sys-hub-card__foot">
          <span>Open Chatbot Settings</span>
          <span aria-hidden="true">&rarr;</span>
        </div>
      </button>

    </div>
  </div>

  {{-- ==================== SHARED TOP BACK BAR WHEN INSIDE A CARD ==================== --}}
  <div class="sys-section-bar" data-settings-backbar @if($activeSection === null) hidden @endif>
    <button type="button" class="btn btn--ghost btn--sm" data-back-to-cards>
      &larr; Back to System Settings Cards
    </button>
    <div class="sys-section-tabs">
      <button type="button" class="sys-tab-pill {{ $activeSection === 'general' ? 'is-active' : '' }}" data-open-settings-card="general">General Settings</button>
      <button type="button" class="sys-tab-pill {{ $activeSection === 'whatsapp' ? 'is-active' : '' }}" data-open-settings-card="whatsapp">WhatsApp</button>
      <button type="button" class="sys-tab-pill {{ $activeSection === 'chatbot' ? 'is-active' : '' }}" data-open-settings-card="chatbot">Chatbot</button>
    </div>
  </div>

  {{-- ==================== PANEL 1: GENERAL SETTINGS ==================== --}}
  <div data-settings-panel="general" @if($activeSection !== 'general') hidden @endif>
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
        <button class="btn btn--primary" type="submit">Save General Settings</button>
      </div>
    </form>
  </div>

  {{-- ==================== PANEL 2: WHATSAPP CLOUD API SETTINGS ==================== --}}
  <div data-settings-panel="whatsapp" @if($activeSection !== 'whatsapp') hidden @endif>
    <div class="card" style="background:#0E0C0A;border-color:rgba(255,255,255,0.09);color:#fff;margin-bottom:22px;">
      <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:14px;">
        <div>
          <h2 class="card__title" style="color:#fff;font-size:1.08rem;">Meta Webhook Endpoint &amp; Connection Status</h2>
          <p class="card__sub" style="color:rgba(255,255,255,0.58);margin-bottom:0;">Paste these values in Meta Developers &rarr; WhatsApp &rarr; Configuration &rarr; Webhook, subscribe to <code>messages</code>, then click Activate WABA Webhook.</p>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
          <span class="pill" style="{{ $whatsappConfigured ? 'background:rgba(37,211,102,0.18);color:#25D366;border:1px solid rgba(37,211,102,0.4);' : 'background:rgba(251,191,36,0.18);color:#fbbf24;' }}">
            {{ $whatsappConfigured ? ($whatsappEnabled ? '● WhatsApp Connected' : '⏸ Paused') : '⚠ Setup Needed' }}
          </span>
          @if($whatsappConfigured)
            <form method="post" action="{{ route('admin.settings.whatsapp.subscribe') }}" style="margin:0;">
              @csrf
              <button type="submit" class="btn btn--primary btn--sm">⚡ Activate WABA Webhook</button>
            </form>
          @endif
        </div>
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

    <div class="card card--form">
      <h2 class="card__title">Meta Graph API Credentials</h2>
      <p class="card__sub">Overrides <code>.env</code> values when filled. Secret tokens remain masked once saved.</p>

      <form method="post" action="{{ route('admin.settings.whatsapp.update') }}">
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
            placeholder="e.g. +201055209612"
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
            placeholder="e.g. 1465962866605942"
          />

          <x-admin.input
            name="whatsapp_business_account_id"
            label="WhatsApp Business Account ID (WABA)"
            :value="setting('whatsapp_business_account_id', config('assistant.whatsapp.business_account_id'))"
            placeholder="e.g. 1735985674703993"
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
            label="System User Permanent Access Token"
            placeholder="{{ $whatsappConfigured ? '•••••••••••••••• (Saved — leave blank to keep)' : 'EAA...' }}"
          />

          <x-admin.input
            name="whatsapp_app_secret"
            type="password"
            label="Meta App Secret (Optional HMAC Security)"
            placeholder="{{ (setting('whatsapp_app_secret') || config('assistant.whatsapp.app_secret')) ? '•••••••••••••••• (Saved)' : 'Optional App Secret' }}"
          />
        </div>

        <div class="form-actions">
          <button class="btn btn--primary" type="submit">Save WhatsApp Settings</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ==================== PANEL 3: AI CHATBOT & COPILOT SETTINGS ==================== --}}
  <div data-settings-panel="chatbot" @if($activeSection !== 'chatbot') hidden @endif>
    @if(! $providerReady)
      <div class="ai-alert" style="margin:0 0 20px;" role="status">
        <strong>AI Provider Not Configured Yet:</strong> {{ $providerHint }}
      </div>
    @endif

    <form method="post" action="{{ route('admin.settings.chatbot.update') }}">
      @csrf @method('PUT')

      <div class="card card--form" style="margin-bottom:22px;">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:6px;">
          <h2 class="card__title" style="margin:0;">Website Live Concierge &amp; Shared Knowledge Base</h2>
          <span class="pill">{{ $webWidgetLive ? 'Web Widget Live' : 'Web Widget Hidden' }}</span>
        </div>
        <p class="card__sub">Controls the floating website chat widget and the custom FAQ instructions shared with your WhatsApp AI agent.</p>

        <div class="form-grid">
          <div class="full">
            <x-admin.toggle
              name="ai_support_enabled"
              label="Display floating AI Customer Support Chatbot widget on public website pages"
              :checked="$webWidgetLive"
            />
          </div>

          <div class="full">
            <x-admin.input
              name="ai_support_greeting"
              label="Website Widget Welcome Greeting"
              :value="setting('ai_support_greeting', \App\Services\CustomerSupportChatService::DEFAULT_GREETING)"
            />
          </div>

          <div class="full">
            <x-admin.textarea
              name="ai_support_knowledge"
              label="Shared Studio FAQ &amp; Guardrail Notes (Website + WhatsApp)"
              rows="6"
              :value="setting('ai_support_knowledge', '')"
              hint="Extra context injected into both Website and WhatsApp support agents — e.g. discovery call workflow, typical project timelines, working hours, or tone guidelines."
            />
          </div>
        </div>
      </div>

      <div class="card card--form">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:6px;">
          <h2 class="card__title" style="margin:0;">AI Model &amp; Provider Routing</h2>
          <span class="pill">Active: {{ strtoupper($providerName) }}</span>
        </div>
        <p class="card__sub">Choose which LLM provider powers Customer Support (Website + WhatsApp) and your internal Dashboard Copilot.</p>

        <div class="form-grid">
          <x-admin.select
            name="ai_support_provider"
            label="Customer Support AI Provider (Web &amp; WhatsApp)"
            :options="$providerOptions"
            :value="setting('ai_support_provider', '')"
          />
          <x-admin.input
            name="ai_support_model"
            label="Customer Support Model Override (Optional)"
            :value="setting('ai_support_model', '')"
            placeholder="Leave blank for provider default"
          />

          <x-admin.select
            name="ai_dashboard_provider"
            label="Dashboard Copilot AI Provider"
            :options="$providerOptions"
            :value="setting('ai_dashboard_provider', '')"
          />
          <x-admin.input
            name="ai_dashboard_model"
            label="Dashboard Copilot Model Override (Optional)"
            :value="setting('ai_dashboard_model', '')"
            placeholder="Leave blank for provider default"
          />
        </div>

        <div class="form-actions">
          <button class="btn btn--primary" type="submit">Save AI &amp; Chatbot Settings</button>
        </div>
      </div>
    </form>
  </div>

</div>

<script>
  (function () {
    function bindSettingsHub() {
      var root = document.getElementById('empetix-settings-root');
      if (!root || root.__hubBound) return;
      root.__hubBound = true;

      var hubEl = root.querySelector('[data-settings-hub]');
      var backBar = root.querySelector('[data-settings-backbar]');
      var panels = Array.prototype.slice.call(root.querySelectorAll('[data-settings-panel]'));
      var tabPills = Array.prototype.slice.call(root.querySelectorAll('.sys-tab-pill'));

      function showSection(section, pushUrl) {
        if (!section) {
          if (hubEl) hubEl.hidden = false;
          if (backBar) backBar.hidden = true;
          panels.forEach(function (p) { p.hidden = true; });
          if (pushUrl && window.history && window.history.replaceState) {
            var cleanUrl = new URL(window.location.href);
            cleanUrl.searchParams.delete('section');
            window.history.replaceState({}, '', cleanUrl.toString());
          }
          return;
        }

        if (hubEl) hubEl.hidden = true;
        if (backBar) backBar.hidden = false;
        panels.forEach(function (p) {
          p.hidden = p.getAttribute('data-settings-panel') !== section;
        });
        tabPills.forEach(function (b) {
          b.classList.toggle('is-active', b.getAttribute('data-open-settings-card') === section);
        });

        if (pushUrl && window.history && window.history.replaceState) {
          var nextUrl = new URL(window.location.href);
          nextUrl.searchParams.set('section', section);
          window.history.replaceState({}, '', nextUrl.toString());
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }

      root.addEventListener('click', function (e) {
        var cardBtn = e.target.closest('[data-open-settings-card]');
        if (cardBtn) {
          showSection(cardBtn.getAttribute('data-open-settings-card'), true);
          return;
        }
        if (e.target.closest('[data-back-to-cards]')) {
          showSection(null, true);
        }
      });
    }

    document.addEventListener('DOMContentLoaded', bindSettingsHub);
    document.addEventListener('livewire:navigated', bindSettingsHub);
    bindSettingsHub();
  })();
</script>
@endsection
