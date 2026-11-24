@php
  $cookieConsentEnabled = setting('cookie_consent_enabled', '1') !== '0';
  $cookieBannerTitle = setting('cookie_banner_title', 'Cookie & Privacy Preferences');
  $cookieBannerMessage = setting('cookie_banner_message', 'We use strictly necessary cookies to keep Empetix secure and reliable, and optional analytics and marketing cookies to understand how visitors use our site. Do you allow us to store optional cookies on your device?');
  $cookieAcceptLabel = setting('cookie_accept_label', 'Accept All Cookies');
  $cookieRejectLabel = setting('cookie_reject_label', 'Essential Only');
  $cookieSettingsLabel = setting('cookie_settings_label', 'Cookie Settings');
@endphp

<!-- Cookie Consent Banner (First-Visit Prompt) -->
<aside
  id="cookie-consent-banner"
  class="cookie-banner"
  role="region"
  aria-label="{{ $cookieBannerTitle }}"
  data-cookie-auto-prompt="{{ $cookieConsentEnabled ? '1' : '0' }}"
  hidden
>
  <div class="cookie-banner__inner">
    <div class="cookie-banner__copy">
      <div class="cookie-banner__eyebrow">
        <span class="cookie-banner__dot" aria-hidden="true"></span>
        <span>PRIVACY &amp; COOKIE NOTICE</span>
      </div>
      <h2 class="cookie-banner__title">{{ $cookieBannerTitle }}</h2>
      <p class="cookie-banner__desc">{{ $cookieBannerMessage }}</p>
    </div>

    <div class="cookie-banner__actions">
      <button type="button" class="cookie-btn cookie-btn--primary" data-cookie-accept-all>
        {{ $cookieAcceptLabel }}
      </button>
      <button type="button" class="cookie-btn cookie-btn--secondary" data-cookie-reject-optional>
        {{ $cookieRejectLabel }}
      </button>
      <button type="button" class="cookie-btn cookie-btn--link" data-cookie-settings>
        {{ $cookieSettingsLabel }}
      </button>
    </div>
  </div>
</aside>

<!-- Granular Cookie Settings Modal (Accessible Anytime from Banner or Footer) -->
<div
  id="cookie-settings-modal"
  class="cookie-modal"
  role="dialog"
  aria-modal="true"
  aria-labelledby="cookie-modal-title"
  hidden
>
  <div class="cookie-modal__backdrop" data-cookie-close aria-hidden="true"></div>

  <div class="cookie-modal__dialog" role="document">
    <header class="cookie-modal__head">
      <div>
        <div class="cookie-modal__status-row">
          <span class="cookie-banner__eyebrow">COOKIE PREFERENCES</span>
          <span class="cookie-modal__status" data-cookie-current-status>Awaiting Choice</span>
        </div>
        <h2 id="cookie-modal-title" class="cookie-modal__title">{{ $cookieBannerTitle }}</h2>
        <p class="cookie-modal__sub">
          Control which cookies Empetix Solution can store on your browser. Choosing &ldquo;{{ $cookieRejectLabel }}&rdquo; prevents optional tracking cookies and clears any existing optional cookies.
        </p>
      </div>
      <button type="button" class="cookie-modal__close" data-cookie-close aria-label="Close cookie settings">
        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true">
          <path d="M3.5 3.5L12.5 12.5M12.5 3.5L3.5 12.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
      </button>
    </header>

    <div class="cookie-modal__body">
      <!-- Category 1: Strictly Necessary -->
      <div class="cookie-cat">
        <div class="cookie-cat__top">
          <div>
            <h3 class="cookie-cat__title">Strictly Necessary Cookies</h3>
            <p class="cookie-cat__desc">Required for core security, CSRF form protection, and remembering your privacy choice. These do not track you across sites.</p>
          </div>
          <span class="cookie-cat__locked">Always Active</span>
        </div>
      </div>

      <!-- Category 2: Analytics & Performance -->
      <label class="cookie-cat cookie-cat--interactive" for="cookie-toggle-analytics">
        <div class="cookie-cat__top">
          <div>
            <h3 class="cookie-cat__title">Analytics &amp; Performance Cookies</h3>
            <p class="cookie-cat__desc">Allow anonymous visit metrics and page-speed diagnostics so our engineering team can improve site reliability.</p>
          </div>
          <span class="cookie-switch">
            <input id="cookie-toggle-analytics" type="checkbox" data-cookie-cat="analytics"/>
            <span class="cookie-switch__track" aria-hidden="true"></span>
          </span>
        </div>
      </label>

      <!-- Category 3: Marketing & Attribution -->
      <label class="cookie-cat cookie-cat--interactive" for="cookie-toggle-marketing">
        <div class="cookie-cat__top">
          <div>
            <h3 class="cookie-cat__title">Marketing &amp; Attribution Cookies</h3>
            <p class="cookie-cat__desc">Measure campaign attribution and relevant communications when visiting from partner or social channels.</p>
          </div>
          <span class="cookie-switch">
            <input id="cookie-toggle-marketing" type="checkbox" data-cookie-cat="marketing"/>
            <span class="cookie-switch__track" aria-hidden="true"></span>
          </span>
        </div>
      </label>
    </div>

    <footer class="cookie-modal__foot">
      <div class="cookie-modal__foot-secondary">
        <button type="button" class="cookie-btn cookie-btn--secondary" data-cookie-reject-optional>
          {{ $cookieRejectLabel }}
        </button>
        <button type="button" class="cookie-btn cookie-btn--secondary" data-cookie-accept-all>
          {{ $cookieAcceptLabel }}
        </button>
      </div>
      <button type="button" class="cookie-btn cookie-btn--primary" data-cookie-save>
        Save Preferences
      </button>
    </footer>
  </div>
</div>
