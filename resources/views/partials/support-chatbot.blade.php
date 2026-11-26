@php
  $hasSettingsTable = \Illuminate\Support\Facades\Schema::hasTable('settings');
  $supportChatService = app(\App\Services\CustomerSupportChatService::class);
  $whatsappService = app(\App\Services\WhatsappCloudApiService::class);

  $supportChatEnabled = $hasSettingsTable && $supportChatService->isEnabled();
  $whatsappChatUrl = $hasSettingsTable ? $whatsappService->publicClickToChatUrl() : null;
@endphp
@if($supportChatEnabled || $whatsappChatUrl)
  @php($chatGreeting = $supportChatEnabled ? $supportChatService->greeting() : '')
  <div
    @if($supportChatEnabled)
      id="empetix-support-widget"
      data-session-url="{{ route('support-chat.session') }}"
      data-message-url="{{ route('support-chat.message') }}"
      data-csrf="{{ csrf_token() }}"
      data-greeting="{{ $chatGreeting }}"
    @else
      id="empetix-whatsapp-dock"
    @endif
    class="emp-floating-dock"
  >
    @if($supportChatEnabled)
      <section class="emp-chat__panel" role="dialog" aria-label="Empetix AI Support Concierge" aria-hidden="true">
        <header class="emp-chat__head">
          <div class="emp-chat__brand">
            <span class="emp-chat__mark" aria-hidden="true">E</span>
            <div>
              <h2 class="emp-chat__title">{{ setting('site_name', 'Empetix') }} Concierge</h2>
              <p class="emp-chat__status">AI Support &amp; Project Desk</p>
            </div>
          </div>
          <div class="emp-chat__head-actions">
            <button type="button" class="emp-chat__icon-btn" data-chat-reset title="Start new conversation" aria-label="Start new conversation">&#8635;</button>
            <button type="button" class="emp-chat__icon-btn" data-chat-close title="Minimize chat" aria-label="Close chat">&times;</button>
          </div>
        </header>

        <div class="emp-chat__handoff-banner" data-chat-handoff hidden>
          &#10003; Delivered to our engineering leads — we reply within 24h.
        </div>

        <div class="emp-chat__body" data-chat-body aria-live="polite">
          <div class="emp-chat__msg emp-chat__msg--bot">
            <div class="emp-chat__bubble" dir="auto" data-chat-greeting>{{ $chatGreeting }}</div>
          </div>

          <div class="emp-chat__chips" data-chat-chips>
            <button type="button" class="emp-chat__chip" data-chat-prompt="What software, cloud, and AI services do you offer?">Our Services</button>
            <button type="button" class="emp-chat__chip" data-chat-prompt="Can you share some featured case studies and platforms you built?">Case Studies</button>
            <button type="button" class="emp-chat__chip" data-chat-prompt="I want to discuss a new project with your team.">Start a Project</button>
            <button type="button" class="emp-chat__chip" data-chat-prompt="Are there any open engineering or product roles right now?">Careers</button>
          </div>
        </div>

        <div class="emp-chat__foot-wrap">
          <form class="emp-chat__foot" data-chat-form>
            <label for="emp-chat-input" class="emp-chat-sr-only">Write your message</label>
            <textarea
              id="emp-chat-input"
              class="emp-chat__input"
              data-chat-input
              rows="1"
              dir="auto"
              maxlength="{{ (int) config('assistant.support.max_message_length', 1500) }}"
              placeholder="Ask in English or العربية…"
            ></textarea>
            <button type="submit" class="emp-chat__send" data-chat-send title="Send" aria-label="Send">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
            </button>
          </form>
        </div>
      </section>

      {{-- Top Icon: Animated AI Chatbot Button --}}
      <button type="button" class="emp-dock-btn emp-dock-btn--ai" data-chat-toggle aria-expanded="false" aria-label="Open AI Chatbot">
        <span class="emp-dock-btn__online" aria-hidden="true"></span>
        <span class="emp-dock-btn__tip" aria-hidden="true">AI Concierge</span>
        {{-- Sleek AI Sparkle Bubble Icon --}}
        <svg class="emp-dock-btn__ico-bot" width="27" height="27" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round" fill="rgba(255,255,255,0.08)"/>
          <path d="M12.2 7.2L13.4 10.3L16.5 11.5L13.4 12.7L12.2 15.8L11 12.7L7.9 11.5L11 10.3L12.2 7.2Z" fill="#FF6B3D" stroke="#FFFFFF" stroke-width="0.9" stroke-linejoin="round"/>
          <circle cx="16.4" cy="7.6" r="1.1" fill="#FFFFFF"/>
        </svg>
        {{-- Close X Icon when open --}}
        <svg class="emp-dock-btn__ico-close" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    @endif

    {{-- Bottom Icon: Official WhatsApp Customer Chat Button --}}
    @if($whatsappChatUrl)
      <a
        href="{{ $whatsappChatUrl }}"
        target="_blank"
        rel="noopener noreferrer"
        class="emp-dock-btn emp-dock-btn--whatsapp"
        aria-label="Chat with us on WhatsApp"
      >
        <span class="emp-dock-btn__tip" aria-hidden="true">WhatsApp Support</span>
        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M20.52 3.48A11.82 11.82 0 0 0 12.06 0C5.52 0 .2 5.32.2 11.86c0 2.09.55 4.13 1.59 5.93L0 24l6.38-1.67a11.83 11.83 0 0 0 5.68 1.45h.01c6.54 0 11.86-5.32 11.86-11.86 0-3.17-1.23-6.15-3.41-8.44ZM12.07 21.78h-.01a9.84 9.84 0 0 1-5.02-1.38l-.36-.21-3.79.99 1.01-3.69-.24-.38a9.83 9.83 0 0 1-1.51-5.25c0-5.44 4.43-9.87 9.88-9.87 2.64 0 5.12 1.03 6.98 2.9a9.81 9.81 0 0 1 2.89 6.98c0 5.44-4.43 9.91-9.83 9.91Zm5.41-7.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.65-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35Z"/>
        </svg>
      </a>
    @endif
  </div>
@endif
