@extends('layouts.admin')

@section('title', 'Website Chat')
@section('page_header', 'hide')

@section('content')
<div
  class="wa-app"
  id="web-chat-app"
  data-webchat-root
  data-url-poll="{{ route('admin.website-chat.poll') }}"
  data-url-show-tpl="{{ route('admin.website-chat.show', '__ID__') }}"
  data-url-send-tpl="{{ route('admin.website-chat.send', '__ID__') }}"
  data-url-typing-tpl="{{ route('admin.website-chat.typing', '__ID__') }}"
  data-url-visitor-tpl="{{ route('admin.website-chat.visitor.update', '__ID__') }}"
  data-url-delete-tpl="{{ route('admin.website-chat.destroy', '__ID__') }}"
>
  <script type="application/json" data-wc-initial>{!! json_encode([
      'contacts' => $contacts,
      'active_chat' => $activeChat,
      'total_unread' => $totalUnread,
  ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) !!}</script>

  {{-- ==================== 1. LEFT VISITORS RAIL ==================== --}}
  <aside class="wa-sidebar" data-wc-sidebar>
    <header class="wa-sidebar__head">
      <div class="wa-sidebar__brand">
        <span class="wa-brand-logo" style="background:linear-gradient(145deg,#4F46E5,#7C3AED);box-shadow:0 4px 12px rgba(79,70,229,0.32);" aria-hidden="true">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 10h.01"/><path d="M12 10h.01"/><path d="M16 10h.01"/></svg>
        </span>
        <div>
          <h2 class="wa-sidebar__title">Website Chat</h2>
          <span class="wa-sidebar__status">
            <span class="wa-dot {{ $chatbotEnabled ? 'is-live' : 'is-warn' }}"></span>
            {{ $chatbotEnabled ? 'AI Concierge Live' : 'Widget Paused' }}
          </span>
        </div>
      </div>

      <div class="wa-sidebar__actions">
        <a href="{{ route('admin.settings.edit', ['section' => 'chatbot']) }}" wire:navigate class="wa-icon-btn" title="Chatbot &amp; AI Settings" aria-label="Chatbot Settings">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        </a>
      </div>
    </header>

    <div class="wa-search-box">
      <div class="wa-search-input-wrap">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="search" class="wa-search-input" data-wc-search placeholder="Search visitors, email, page, or chat…"/>
      </div>
    </div>

    <div class="wa-filters" role="tablist" aria-label="Filter website chats">
      <button type="button" class="wa-filter-chip is-active" data-wc-filter="all">All</button>
      <button type="button" class="wa-filter-chip" data-wc-filter="unread">
        Unread <span class="wa-filter-chip__count" data-wc-unread-total {{ $totalUnread > 0 ? '' : 'hidden' }}>{{ $totalUnread }}</span>
      </button>
      <button type="button" class="wa-filter-chip" data-wc-filter="live">Live</button>
      <button type="button" class="wa-filter-chip" data-wc-filter="pinned">Pinned</button>
      <button type="button" class="wa-filter-chip" data-wc-filter="human">Human</button>
    </div>

    <div class="wa-contact-list" data-wc-contacts></div>
  </aside>

  {{-- ==================== 2. CENTER CHAT STAGE ==================== --}}
  <section class="wa-stage" data-wc-stage>
    <div class="wa-welcome" data-wc-welcome @if($activeChat) hidden @endif>
      <div class="wa-welcome__icon" style="background:rgba(79,70,229,0.14);color:#4F46E5;" aria-hidden="true">
        <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 10h.01"/><path d="M12 10h.01"/><path d="M16 10h.01"/></svg>
      </div>
      <h3>Empetix Website Live Chat Studio</h3>
      <p>Monitor live conversations between website visitors and your AI Concierge in real time. Jump in anytime with Human Takeover to reply directly into the visitor's browser widget.</p>
    </div>

    <div class="wa-conversation" data-wc-conversation @if(! $activeChat) hidden @endif>
      <header class="wa-chat-head">
        <div class="wa-chat-head__left">
          <button type="button" class="wa-icon-btn wa-mobile-back" data-wc-back aria-label="Back to visitors">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <div class="wa-avatar" data-wc-head-avatar data-wc-toggle-drawer style="cursor:pointer;"></div>
          <div class="wa-chat-head__meta" data-wc-toggle-drawer style="cursor:pointer;">
            <div class="wa-chat-head__title-row">
              <h3 class="wa-chat-head__name" data-wc-head-name>Visitor</h3>
              <span class="wa-username-pill" data-wc-head-status>Open</span>
              <span class="wa-enc-pill" title="Messages &amp; notes encrypted at rest">🔒 Live Sync</span>
            </div>
            <p class="wa-chat-head__sub" data-wc-head-sub>/</p>
          </div>
        </div>

        <div class="wa-chat-head__tools">
          <button type="button" class="wa-ai-switch" data-wc-toggle-ai title="Switch between AI Auto-Reply and Human Takeover">
            <span class="wa-ai-switch__dot"></span>
            <span data-wc-toggle-ai-label>AI Auto</span>
          </button>

          <button type="button" class="wa-icon-btn" data-wc-toggle-msg-search title="Search in conversation" aria-label="Search messages">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          </button>

          <button type="button" class="wa-icon-btn" data-wc-toggle-pin title="Pin / Unpin conversation" aria-label="Pin conversation">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="17" x2="12" y2="22"/><path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a2 2 0 0 0 0-4H8a2 2 0 0 0 0 4h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"/></svg>
          </button>

          <button type="button" class="wa-icon-btn" data-wc-toggle-drawer title="Visitor Details &amp; Handoff Info" aria-label="Visitor info">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          </button>
        </div>
      </header>

      <div class="wa-inchat-search" data-wc-inchat-search hidden>
        <input type="search" class="wa-search-input" data-wc-inchat-input placeholder="Filter messages in this chat…"/>
        <button type="button" class="wa-icon-btn" data-wc-close-msg-search aria-label="Close search">&times;</button>
      </div>

      <div class="wa-stream" data-wc-stream>
        <div class="wa-stream__inner" data-wc-messages></div>
      </div>

      <footer class="wa-composer-zone">
        <div class="wa-emoji-popover" data-wc-emoji-popover hidden></div>

        <form class="wa-composer-bar" data-wc-form>
          <button type="button" class="wa-icon-btn" data-wc-btn-emoji title="Emoji" aria-label="Emoji">
            <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
          </button>

          <div class="wa-composer-input-shell">
            <textarea
              class="wa-composer-input"
              data-wc-input
              rows="1"
              dir="auto"
              placeholder="Reply live to website visitor (pauses AI automatically)…"
            ></textarea>
          </div>

          <button type="submit" class="wa-action-fab wa-action-fab--send" data-wc-btn-send title="Send live reply" aria-label="Send live reply">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21 23 12 2.01 3 2 10l15 2-15 2z"/></svg>
          </button>
        </form>
      </footer>
    </div>
  </section>

  {{-- ==================== 3. RIGHT VISITOR INFO & CRM DRAWER ==================== --}}
  <aside class="wa-info-drawer" data-wc-drawer hidden>
    <header class="wa-info-drawer__head">
      <h3>Visitor Info</h3>
      <button type="button" class="wa-icon-btn" data-wc-close-drawer aria-label="Close visitor info">&times;</button>
    </header>

    <div class="wa-info-drawer__body">
      <div class="wa-info-profile">
        <div class="wa-avatar-upload-wrap">
          <div class="wa-avatar wa-avatar--lg" data-wc-drawer-avatar></div>
          <label class="wa-avatar-cam-btn" title="Upload visitor avatar">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            <input type="file" accept="image/*" hidden data-wc-avatar-input/>
          </label>
        </div>
        <h4 data-wc-drawer-title>Visitor</h4>
        <p data-wc-drawer-sub>/</p>
        <div class="wa-info-kpis">
          <div><strong data-wc-drawer-msg-count>0</strong><span>Turns</span></div>
          <div><strong data-wc-drawer-presence>Offline</strong><span>Presence</span></div>
        </div>
        <div data-wc-brief-wrap style="margin-top:10px;" hidden>
          <a href="#" class="btn btn--ghost btn--sm" data-wc-brief-link style="width:100%;justify-content:center;">📬 Open Linked Inbox Brief &rarr;</a>
        </div>
      </div>

      <form class="wa-crm-form" data-wc-crm-form>
        <label class="wa-field">
          <span>Visitor Name</span>
          <input type="text" name="visitor_name" class="input" data-wc-crm-name placeholder="Visitor name"/>
        </label>
        <label class="wa-field">
          <span>Email</span>
          <input type="email" name="visitor_email" class="input" data-wc-crm-email placeholder="visitor@company.com"/>
        </label>
        <label class="wa-field">
          <span>Phone</span>
          <input type="text" name="visitor_phone" class="input" data-wc-crm-phone placeholder="+20…"/>
        </label>
        <label class="wa-field">
          <span>Conversation Status</span>
          <select name="status" class="input" data-wc-crm-status>
            <option value="open">Open</option>
            <option value="handed_off">Handed off</option>
            <option value="closed">Closed</option>
          </select>
        </label>
        <label class="wa-field">
          <span>Team Notes (AES-256 Encrypted)</span>
          <textarea name="contact_notes" rows="3" class="input" data-wc-crm-notes placeholder="Internal notes about this visitor…"></textarea>
        </label>
        <button type="submit" class="btn btn--primary btn--sm" style="width:100%;justify-content:center;">Save Visitor Details</button>
      </form>

      <div class="wa-shared-section" data-wc-summary-box hidden>
        <h5>AI Handoff Summary</h5>
        <p style="font-size:0.82rem;color:var(--wa-bubble-text);line-height:1.55;margin:0;" data-wc-handoff-summary></p>
      </div>

      <div style="padding:14px 18px 22px;">
        <button type="button" class="btn btn--danger btn--sm" style="width:100%;justify-content:center;" data-wc-delete-chat>
          Delete Conversation
        </button>
      </div>
    </div>
  </aside>
</div>
@endsection
