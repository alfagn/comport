@extends('layouts.admin')

@section('title', 'System Settings')
@section('page_header', 'hide')

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
  $cleanDomain = preg_replace('#^https?://#i', '', rtrim((string) setting('site_domain', 'empetix.com'), '/')) ?: 'empetix.com';
@endphp

<style>
  [data-settings-hub][hidden],
  [data-settings-backbar][hidden],
  [data-settings-panel][hidden] {
    display: none !important;
  }

  .sys-hub-head {
    margin-bottom: 26px;
  }

  .sys-hub-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 22px;
  }

  @media (max-width: 1080px) {
    .sys-hub-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 700px) {
    .sys-hub-grid {
      grid-template-columns: 1fr;
    }
  }

  .sys-hub-card {
    --card-accent: #FF4D1C;
    --card-glow: rgba(255, 77, 28, 0.24);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 22px;
    padding: 28px 26px 22px;
    border-radius: 22px;
    background: linear-gradient(165deg, #171412 0%, #0D0B0A 100%);
    border: 1px solid rgba(255, 255, 255, 0.09);
    box-shadow: 0 20px 48px rgba(10, 10, 12, 0.18), inset 0 1px 0 rgba(255, 255, 255, 0.08);
    color: #F5F1EC;
    cursor: pointer;
    text-align: left;
    font-family: var(--font-body);
    transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.22s ease, box-shadow 0.22s ease;
  }

  .sys-hub-card::before {
    content: "";
    position: absolute;
    top: -70px;
    right: -60px;
    width: 230px;
    height: 230px;
    border-radius: 50%;
    background: radial-gradient(circle, var(--card-glow) 0%, transparent 68%);
    pointer-events: none;
    transition: opacity 0.25s ease, transform 0.25s ease;
    opacity: 0.75;
  }

  .sys-hub-card--wa {
    --card-accent: #25D366;
    --card-glow: rgba(37, 211, 102, 0.24);
  }

  .sys-hub-card--ai {
    --card-accent: #8B5CF6;
    --card-glow: rgba(139, 92, 246, 0.26);
  }

  .sys-hub-card:hover {
    transform: translateY(-5px);
    border-color: var(--card-accent);
    box-shadow: 0 28px 56px rgba(10, 10, 12, 0.28), 0 0 0 1px var(--card-accent);
  }

  .sys-hub-card:hover::before {
    opacity: 1;
    transform: scale(1.12);
  }

  .sys-hub-card__top {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
  }

  .sys-hub-card__icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #FFFFFF;
    flex-shrink: 0;
    border: 1px solid rgba(255, 255, 255, 0.18);
  }

  .sys-hub-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 0.74rem;
    font-weight: 700;
    letter-spacing: 0.01em;
    background: rgba(255, 255, 255, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: rgba(245, 241, 236, 0.88);
  }

  .sys-hub-badge--ok {
    background: rgba(37, 211, 102, 0.14);
    border-color: rgba(37, 211, 102, 0.36);
    color: #4ADE80;
  }

  .sys-hub-badge--ai {
    background: rgba(139, 92, 246, 0.16);
    border-color: rgba(139, 92, 246, 0.38);
    color: #C4B5FD;
  }

  .sys-hub-badge--warn {
    background: rgba(251, 191, 36, 0.14);
    border-color: rgba(251, 191, 36, 0.36);
    color: #FCD34D;
  }

  .sys-hub-card__kicker {
    position: relative;
    z-index: 1;
    display: block;
    margin: 18px 0 6px;
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: rgba(245, 241, 236, 0.45);
  }

  .sys-hub-card__title {
    position: relative;
    z-index: 1;
    margin: 0 0 8px;
    font-family: var(--font-display);
    font-size: 1.28rem;
    font-weight: 800;
    letter-spacing: -0.01em;
    color: #FFFFFF;
  }

  .sys-hub-card__desc {
    position: relative;
    z-index: 1;
    margin: 0;
    font-size: 0.86rem;
    line-height: 1.6;
    color: rgba(245, 241, 236, 0.66);
  }

  .sys-hub-tags {
    position: relative;
    z-index: 1;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 16px;
  }

  .sys-hub-tags span {
    font-size: 0.71rem;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.055);
    border: 1px solid rgba(255, 255, 255, 0.08);
    color: rgba(245, 241, 236, 0.78);
  }

  .sys-hub-card__foot {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.09);
    font-size: 0.84rem;
    font-weight: 700;
    color: #FFFFFF;
  }

  .sys-hub-card__arrow {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
  }

  .sys-hub-card:hover .sys-hub-card__arrow {
    background: var(--card-accent);
    border-color: var(--card-accent);
    color: #FFFFFF;
    transform: translateX(3px);
  }

  /* Sleek Obsidian Command Bar shown ONLY inside an opened Card */
  .sys-section-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 24px;
    padding: 12px 16px;
    border-radius: 18px;
    background: linear-gradient(165deg, #171412 0%, #0E0C0A 100%);
    border: 1px solid rgba(255, 255, 255, 0.10);
    box-shadow: 0 12px 30px rgba(10, 10, 12, 0.14);
  }

  .sys-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 15px;
    border-radius: 11px;
    border: 1px solid rgba(255, 255, 255, 0.14);
    background: rgba(255, 255, 255, 0.06);
    color: #FFFFFF;
    font-family: var(--font-body);
    font-size: 0.83rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.15s ease, border-color 0.15s ease;
  }

  .sys-back-btn:hover {
    background: rgba(255, 77, 28, 0.2);
    border-color: rgba(255, 77, 28, 0.5);
  }

  .sys-section-tabs {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.08);
    flex-wrap: wrap;
  }

  .sys-tab-pill {
    border: 0;
    background: transparent;
    color: rgba(245, 241, 236, 0.68);
    font-family: var(--font-body);
    font-size: 0.8rem;
    font-weight: 700;
    padding: 7px 14px;
    border-radius: 9px;
    cursor: pointer;
    transition: all 0.16s ease;
  }

  .sys-tab-pill:hover {
    color: #FFFFFF;
  }

  .sys-tab-pill.is-active {
    background: #FF4D1C;
    color: #FFFFFF;
    box-shadow: 0 4px 12px rgba(255, 77, 28, 0.35);
  }
</style>

<div id="empetix-settings-root" data-initial-section="{{ $activeSection ?? '' }}">

  {{-- ==================== CARDS HUB VIEW (Default when clicking System Settings) ==================== --}}
  <div data-settings-hub @if($activeSection !== null) hidden @endif>
    <div class="sys-hub-head">
      <span class="welcome-pill">Control Center &middot; Configuration</span>
      <h1 class="page-title">System Settings</h1>
      <p class="page-sub">Select a module below to configure site identity, Meta WhatsApp Cloud API, or your AI Concierge &amp; Copilot.</p>
    </div>

    <div class="sys-hub-grid">

      {{-- Card 1: General Settings --}}
      <button type="button" class="sys-hub-card" data-open-settings-card="general">
        <div>
          <div class="sys-hub-card__top">
            <span class="sys-hub-card__icon" style="background:linear-gradient(145deg,#FF5E36,#D9380F);box-shadow:0 10px 24px rgba(255,77,28,0.34);">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            </span>
            <span class="sys-hub-badge">🌐 {{ $cleanDomain }}</span>
          </div>
          <span class="sys-hub-card__kicker">01 &middot; Core Platform</span>
          <h2 class="sys-hub-card__title">General Settings</h2>
          <p class="sys-hub-card__desc">Official website domain, light &amp; dark brand logos, contact emails, phone &amp; HQ address, social links, and cookie privacy.</p>
          <div class="sys-hub-tags">
            <span>Brand &amp; Logos</span>
            <span>Contact Desks</span>
            <span>Cookie Consent</span>
          </div>
        </div>
        <div class="sys-hub-card__foot">
          <span>Configure General Settings</span>
          <span class="sys-hub-card__arrow" aria-hidden="true">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </span>
        </div>
      </button>

      {{-- Card 2: WhatsApp Settings --}}
      <button type="button" class="sys-hub-card sys-hub-card--wa" data-open-settings-card="whatsapp">
        <div>
          <div class="sys-hub-card__top">
            <span class="sys-hub-card__icon" style="background:linear-gradient(145deg,#25D366,#0E8A5F);box-shadow:0 10px 24px rgba(37,211,102,0.32);">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20.52 3.48A11.82 11.82 0 0 0 12.06 0C5.52 0 .2 5.32.2 11.86c0 2.09.55 4.13 1.59 5.93L0 24l6.38-1.67a11.83 11.83 0 0 0 5.68 1.45h.01c6.54 0 11.86-5.32 11.86-11.86 0-3.17-1.23-6.15-3.41-8.44ZM12.07 21.78h-.01a9.84 9.84 0 0 1-5.02-1.38l-.36-.21-3.79.99 1.01-3.69-.24-.38a9.83 9.83 0 0 1-1.51-5.25c0-5.44 4.43-9.87 9.88-9.87 2.64 0 5.12 1.03 6.98 2.9a9.81 9.81 0 0 1 2.89 6.98c0 5.44-4.43 9.91-9.83 9.91Zm5.41-7.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.65-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35Z"/></svg>
            </span>
            <span class="sys-hub-badge {{ $whatsappConfigured ? 'sys-hub-badge--ok' : 'sys-hub-badge--warn' }}">
              {{ $whatsappConfigured ? ($whatsappEnabled ? '● Connected' : '⏸ Paused') : '⚠ Setup Needed' }}
            </span>
          </div>
          <span class="sys-hub-card__kicker">02 &middot; Messaging Cloud</span>
          <h2 class="sys-hub-card__title">WhatsApp</h2>
          <p class="sys-hub-card__desc">Meta WhatsApp Business Cloud API credentials, Phone Number ID, WABA ID, permanent System User token, and live webhooks.</p>
          <div class="sys-hub-tags">
            <span>Cloud API v22</span>
            <span>Webhooks</span>
            <span>wa.me Button</span>
          </div>
        </div>
        <div class="sys-hub-card__foot">
          <span>Configure WhatsApp API</span>
          <span class="sys-hub-card__arrow" aria-hidden="true">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </span>
        </div>
      </button>

      {{-- Card 3: Chatbot & AI Settings --}}
      <button type="button" class="sys-hub-card sys-hub-card--ai" data-open-settings-card="chatbot">
        <div>
          <div class="sys-hub-card__top">
            <span class="sys-hub-card__icon" style="background:linear-gradient(145deg,#8B5CF6,#4F46E5);box-shadow:0 10px 24px rgba(124,58,237,0.34);">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.5L14.35 8.65L20.5 11L14.35 13.35L12 19.5L9.65 13.35L3.5 11L9.65 8.65L12 2.5Z"/><path d="M19 3v4"/><path d="M21 5h-4"/></svg>
            </span>
            <span class="sys-hub-badge {{ $webWidgetLive ? 'sys-hub-badge--ai' : 'sys-hub-badge--warn' }}">
              {{ $webWidgetLive ? '● Active: '.strtoupper($providerName) : '⏸ Widget Off' }}
            </span>
          </div>
          <span class="sys-hub-card__kicker">03 &middot; AI Intelligence</span>
          <h2 class="sys-hub-card__title">Chatbot &amp; AI</h2>
          <p class="sys-hub-card__desc">Website live AI Concierge greeting, public floating chat widget, shared studio FAQ knowledge base, and LLM model routing.</p>
          <div class="sys-hub-tags">
            <span>Live Concierge</span>
            <span>Studio FAQ</span>
            <span>Gemini / OpenAI</span>
          </div>
        </div>
        <div class="sys-hub-card__foot">
          <span>Configure Chatbot &amp; AI</span>
          <span class="sys-hub-card__arrow" aria-hidden="true">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </span>
        </div>
      </button>

    </div>
  </div>

  {{-- ==================== SHARED TOP BAR (SHOWN ONLY INSIDE AN OPENED CARD) ==================== --}}
  <div class="sys-section-bar" data-settings-backbar @if($activeSection === null) hidden @endif>
    <button type="button" class="sys-back-btn" data-back-to-cards>
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      <span>All System Settings</span>
    </button>
    <div class="sys-section-tabs">
      <button type="button" class="sys-tab-pill {{ $activeSection === 'general' ? 'is-active' : '' }}" data-open-settings-card="general">General Settings</button>
      <button type="button" class="sys-tab-pill {{ $activeSection === 'whatsapp' ? 'is-active' : '' }}" data-open-settings-card="whatsapp">WhatsApp</button>
      <button type="button" class="sys-tab-pill {{ $activeSection === 'chatbot' ? 'is-active' : '' }}" data-open-settings-card="chatbot">Chatbot &amp; AI</button>
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
