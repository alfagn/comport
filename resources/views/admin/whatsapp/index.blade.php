@extends('layouts.admin')

@section('title', 'WhatsApp Inbox')
@section('page_header', 'hide')

@section('content')
<div
  class="wa-app"
  id="wa-inbox-app"
  data-whatsapp-root
  data-configured="{{ $whatsappConfigured ? '1' : '0' }}"
  data-url-poll="{{ route('admin.whatsapp.poll') }}"
  data-url-start="{{ route('admin.whatsapp.start') }}"
  data-url-show-tpl="{{ route('admin.whatsapp.show', '__ID__') }}"
  data-url-send-tpl="{{ route('admin.whatsapp.send', '__ID__') }}"
  data-url-contact-tpl="{{ route('admin.whatsapp.contact.update', '__ID__') }}"
  data-url-delete-tpl="{{ route('admin.whatsapp.destroy', '__ID__') }}"
  data-url-react-tpl="{{ route('admin.whatsapp.react', ['id' => '__ID__', 'messageId' => '__MSG__']) }}"
  data-url-notifications="{{ route('admin.whatsapp.notifications.update') }}"
  data-url-api-settings="{{ route('admin.support-chats.whatsapp') }}"
>
  <script type="application/json" data-wa-initial>{!! json_encode([
      'contacts' => $contacts,
      'active_chat' => $activeChat,
      'total_unread' => $totalUnread,
      'notifications' => $notificationSettings,
  ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) !!}</script>

  {{-- ==================== 1. LEFT CONTACTS PANE ==================== --}}
  <aside class="wa-sidebar" data-wa-sidebar>
    <header class="wa-sidebar__head">
      <div class="wa-sidebar__brand">
        <span class="wa-brand-logo" aria-hidden="true">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20.52 3.48A11.82 11.82 0 0 0 12.06 0C5.52 0 .2 5.32.2 11.86c0 2.09.55 4.13 1.59 5.93L0 24l6.38-1.67a11.83 11.83 0 0 0 5.68 1.45h.01c6.54 0 11.86-5.32 11.86-11.86 0-3.17-1.23-6.15-3.41-8.44ZM12.07 21.78h-.01a9.84 9.84 0 0 1-5.02-1.38l-.36-.21-3.79.99 1.01-3.69-.24-.38a9.83 9.83 0 0 1-1.51-5.25c0-5.44 4.43-9.87 9.88-9.87 2.64 0 5.12 1.03 6.98 2.9a9.81 9.81 0 0 1 2.89 6.98c0 5.44-4.43 9.91-9.83 9.91Zm5.41-7.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.65-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35Z"/></svg>
        </span>
        <div>
          <h2 class="wa-sidebar__title">WhatsApp</h2>
          <span class="wa-sidebar__status">
            <span class="wa-dot {{ $whatsappConfigured ? 'is-live' : 'is-warn' }}"></span>
            {{ $whatsappConfigured ? 'Cloud API Live' : 'API Setup Needed' }}
          </span>
        </div>
      </div>

      <div class="wa-sidebar__actions">
        <button type="button" class="wa-icon-btn" data-wa-open-modal="new-chat" title="New conversation" aria-label="New conversation">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><line x1="12" y1="7" x2="12" y2="13"/><line x1="9" y1="10" x2="15" y2="10"/></svg>
        </button>
        <button type="button" class="wa-icon-btn" data-wa-open-modal="notifications" title="Unread Email Alert Settings" aria-label="Unread email notification settings">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </button>
        <a href="{{ route('admin.support-chats.whatsapp') }}" wire:navigate class="wa-icon-btn" title="WhatsApp Cloud API Credentials" aria-label="WhatsApp Cloud API Settings">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        </a>
      </div>
    </header>

    <div class="wa-search-box">
      <div class="wa-search-input-wrap">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="search" class="wa-search-input" data-wa-search placeholder="Search contacts, phone, or messages…"/>
      </div>
    </div>

    <div class="wa-filters" role="tablist" aria-label="Filter conversations">
      <button type="button" class="wa-filter-chip is-active" data-wa-filter="all">All</button>
      <button type="button" class="wa-filter-chip" data-wa-filter="unread">
        Unread <span class="wa-filter-chip__count" data-wa-unread-total {{ $totalUnread > 0 ? '' : 'hidden' }}>{{ $totalUnread }}</span>
      </button>
      <button type="button" class="wa-filter-chip" data-wa-filter="pinned">Pinned</button>
      <button type="button" class="wa-filter-chip" data-wa-filter="human">Human</button>
    </div>

    <div class="wa-contact-list" data-wa-contacts></div>
  </aside>

  {{-- ==================== 2. CENTER CHAT STAGE ==================== --}}
  <section class="wa-stage" data-wa-stage>
    {{-- Empty Welcome State --}}
    <div class="wa-welcome" data-wa-welcome @if($activeChat) hidden @endif>
      <div class="wa-welcome__icon" aria-hidden="true">
        <svg width="44" height="44" viewBox="0 0 24 24" fill="currentColor"><path d="M20.52 3.48A11.82 11.82 0 0 0 12.06 0C5.52 0 .2 5.32.2 11.86c0 2.09.55 4.13 1.59 5.93L0 24l6.38-1.67a11.83 11.83 0 0 0 5.68 1.45h.01c6.54 0 11.86-5.32 11.86-11.86 0-3.17-1.23-6.15-3.41-8.44ZM12.07 21.78h-.01a9.84 9.84 0 0 1-5.02-1.38l-.36-.21-3.79.99 1.01-3.69-.24-.38a9.83 9.83 0 0 1-1.51-5.25c0-5.44 4.43-9.87 9.88-9.87 2.64 0 5.12 1.03 6.98 2.9a9.81 9.81 0 0 1 2.89 6.98c0 5.44-4.43 9.91-9.83 9.91Zm5.41-7.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.65-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35Z"/></svg>
      </div>
      <h3>Empetix WhatsApp Business Studio</h3>
      <p>Select a contact on the left or start a new conversation. Send &amp; receive photos, voice notes, documents, locations, reactions, and AI-assisted replies — completely independent from your Email Inbox.</p>
      <button type="button" class="wa-primary-btn" data-wa-open-modal="new-chat">+ Start New WhatsApp Chat</button>
    </div>

    {{-- Active Conversation Pane --}}
    <div class="wa-conversation" data-wa-conversation @if(! $activeChat) hidden @endif>
      <header class="wa-chat-head">
        <div class="wa-chat-head__left">
          <button type="button" class="wa-icon-btn wa-mobile-back" data-wa-back aria-label="Back to contacts">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <div class="wa-avatar" data-wa-head-avatar data-wa-toggle-drawer style="cursor:pointer;"></div>
          <div class="wa-chat-head__meta" data-wa-toggle-drawer style="cursor:pointer;">
            <div class="wa-chat-head__title-row">
              <h3 class="wa-chat-head__name" data-wa-head-name>Contact</h3>
              <span class="wa-username-pill" data-wa-head-username hidden>@username</span>
              <span class="wa-enc-pill" title="Messages &amp; media are encrypted at rest with AES-256">🔒 Encrypted</span>
            </div>
            <p class="wa-chat-head__sub" data-wa-head-sub>+20…</p>
          </div>
        </div>

        {{-- Header Toolbar (All WhatsApp actions EXCEPT Voice/Video Calls) --}}
        <div class="wa-chat-head__tools">
          <button type="button" class="wa-ai-switch" data-wa-toggle-ai title="Switch between AI Auto-Reply and Human Takeover">
            <span class="wa-ai-switch__dot"></span>
            <span data-wa-toggle-ai-label>AI Active</span>
          </button>

          <button type="button" class="wa-icon-btn" data-wa-toggle-msg-search title="Search in conversation" aria-label="Search messages">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          </button>

          <button type="button" class="wa-icon-btn" data-wa-toggle-pin title="Pin / Unpin conversation" aria-label="Pin conversation">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="17" x2="12" y2="22"/><path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a2 2 0 0 0 0-4H8a2 2 0 0 0 0 4h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"/></svg>
          </button>

          <button type="button" class="wa-icon-btn" data-wa-toggle-drawer title="Contact Info &amp; Shared Media" aria-label="Contact info">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          </button>
        </div>
      </header>

      {{-- In-Chat Search Bar --}}
      <div class="wa-inchat-search" data-wa-inchat-search hidden>
        <input type="search" class="wa-search-input" data-wa-inchat-input placeholder="Filter messages in this chat…"/>
        <button type="button" class="wa-icon-btn" data-wa-close-msg-search aria-label="Close search">&times;</button>
      </div>

      {{-- Message Stream with Authentic WhatsApp Pattern --}}
      <div class="wa-stream" data-wa-stream>
        <div class="wa-stream__inner" data-wa-messages></div>
      </div>

      {{-- Bottom Rich WhatsApp Composer --}}
      <footer class="wa-composer-zone">
        {{-- Quoted Reply Banner --}}
        <div class="wa-reply-bar" data-wa-reply-bar hidden>
          <div class="wa-reply-bar__body">
            <strong data-wa-reply-sender>Customer</strong>
            <span data-wa-reply-preview>Message</span>
          </div>
          <button type="button" class="wa-icon-btn" data-wa-reply-cancel aria-label="Cancel reply">&times;</button>
        </div>

        {{-- Staged Attachments Tray --}}
        <div class="wa-staged-files" data-wa-staged-files hidden></div>

        {{-- Popover: Emoji Picker --}}
        <div class="wa-emoji-popover" data-wa-emoji-popover hidden></div>

        {{-- Popover: Attachment Menu --}}
        <div class="wa-attach-menu" data-wa-attach-menu hidden>
          <button type="button" class="wa-attach-item" data-wa-pick="media">
            <span class="wa-attach-item__ico" style="background:#7C3AED;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            </span>
            <span>Photos &amp; Videos</span>
          </button>
          <button type="button" class="wa-attach-item" data-wa-pick="document">
            <span class="wa-attach-item__ico" style="background:#0284C7;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </span>
            <span>Document / File</span>
          </button>
          <button type="button" class="wa-attach-item" data-wa-open-modal="location">
            <span class="wa-attach-item__ico" style="background:#10B981;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </span>
            <span>Location Pin</span>
          </button>
          <button type="button" class="wa-attach-item" data-wa-open-modal="template">
            <span class="wa-attach-item__ico" style="background:#EA580C;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="7" y1="8" x2="17" y2="8"/><line x1="7" y1="12" x2="17" y2="12"/><line x1="7" y1="16" x2="12" y2="16"/></svg>
            </span>
            <span>Meta Template</span>
          </button>
        </div>

        <input type="file" data-wa-file-media multiple hidden accept="image/*,video/*"/>
        <input type="file" data-wa-file-doc multiple hidden accept="*/*"/>

        {{-- Standard Composer Row --}}
        <form class="wa-composer-bar" data-wa-form>
          <button type="button" class="wa-icon-btn" data-wa-btn-emoji title="Emoji" aria-label="Emoji">
            <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
          </button>

          <button type="button" class="wa-icon-btn" data-wa-btn-attach title="Attach photos, files, location, or template" aria-label="Attach">
            <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          </button>

          <div class="wa-composer-input-shell">
            <textarea
              class="wa-composer-input"
              data-wa-input
              rows="1"
              dir="auto"
              placeholder="Type a message (العربية / English)…"
            ></textarea>
          </div>

          {{-- Mic Button (shown when input & files are empty) --}}
          <button type="button" class="wa-action-fab wa-action-fab--mic" data-wa-btn-mic title="Record voice message" aria-label="Record voice message">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>
          </button>

          {{-- Send Button (shown when text or attachments exist) --}}
          <button type="submit" class="wa-action-fab wa-action-fab--send" data-wa-btn-send hidden title="Send message" aria-label="Send message">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21 23 12 2.01 3 2 10l15 2-15 2z"/></svg>
          </button>
        </form>

        {{-- Live Voice Recording Bar (replaces composer while recording) --}}
        <div class="wa-recorder-bar" data-wa-recorder-bar hidden>
          <button type="button" class="wa-icon-btn wa-recorder-bar__cancel" data-wa-rec-cancel title="Cancel recording" aria-label="Cancel recording">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
          </button>

          <div class="wa-recorder-bar__live">
            <span class="wa-rec-dot"></span>
            <span class="wa-rec-timer" data-wa-rec-timer>0:00</span>
            <div class="wa-rec-visualizer" data-wa-rec-bars>
              @for($i = 0; $i < 24; $i++)
                <span></span>
              @endfor
            </div>
          </div>

          <button type="button" class="wa-action-fab wa-action-fab--send" data-wa-rec-send title="Send voice note" aria-label="Send voice note">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21 23 12 2.01 3 2 10l15 2-15 2z"/></svg>
          </button>
        </div>
      </footer>
    </div>
  </section>

  {{-- ==================== 3. RIGHT CONTACT CRM & MEDIA DRAWER ==================== --}}
  <aside class="wa-info-drawer" data-wa-drawer hidden>
    <header class="wa-info-drawer__head">
      <h3>Contact Info</h3>
      <button type="button" class="wa-icon-btn" data-wa-close-drawer aria-label="Close contact info">&times;</button>
    </header>

    <div class="wa-info-drawer__body">
      <div class="wa-info-profile">
        <div class="wa-avatar-upload-wrap">
          <div class="wa-avatar wa-avatar--lg" data-wa-drawer-avatar></div>
          <label class="wa-avatar-cam-btn" title="Upload contact profile photo">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            <input type="file" accept="image/*" hidden data-wa-avatar-input/>
          </label>
        </div>
        <h4 data-wa-drawer-title>Contact</h4>
        <div class="wa-drawer-handle" data-wa-drawer-username hidden>@username</div>
        <p data-wa-drawer-phone>+20…</p>
        <div class="wa-info-kpis">
          <div><strong data-wa-drawer-msg-count>0</strong><span>Messages</span></div>
          <div><strong data-wa-drawer-window>Open</strong><span>24h Window</span></div>
        </div>
      </div>

      <form class="wa-crm-form" data-wa-crm-form>
        <label class="wa-field">
          <span>Contact Name</span>
          <input type="text" name="visitor_name" class="input" data-wa-crm-name placeholder="Client name"/>
        </label>
        <label class="wa-field">
          <span>WhatsApp @username</span>
          <input type="text" name="visitor_username" class="input" data-wa-crm-username placeholder="@username (if available)"/>
        </label>
        <label class="wa-field">
          <span>Email (Optional CRM tag)</span>
          <input type="email" name="visitor_email" class="input" data-wa-crm-email placeholder="client@company.com"/>
        </label>
        <label class="wa-field">
          <span>Conversation Status</span>
          <select name="status" class="input" data-wa-crm-status>
            <option value="open">Open</option>
            <option value="handed_off">Handed off</option>
            <option value="closed">Closed</option>
          </select>
        </label>
        <label class="wa-field">
          <span>Team Notes</span>
          <textarea name="contact_notes" rows="3" class="input" data-wa-crm-notes placeholder="Internal notes about this customer…"></textarea>
        </label>
        <button type="submit" class="btn btn--primary btn--sm" style="width:100%;justify-content:center;">Save Contact Details</button>
      </form>

      <div class="wa-shared-section">
        <h5>Shared Photos, Voice &amp; Files</h5>
        <div class="wa-shared-grid" data-wa-shared-media>
          <p class="wa-muted-note">No media shared yet.</p>
        </div>
      </div>

      <div style="padding:14px 18px 22px;">
        <button type="button" class="btn btn--danger btn--sm" style="width:100%;justify-content:center;" data-wa-delete-chat>
          Delete Conversation &amp; Media
        </button>
      </div>
    </div>
  </aside>

  {{-- ==================== MODALS ==================== --}}
  {{-- 1. Start New Chat Modal --}}
  <div class="wa-modal-backdrop" data-wa-modal="new-chat" hidden>
    <div class="wa-modal">
      <div class="wa-modal__head">
        <h3>New WhatsApp Conversation</h3>
        <button type="button" class="wa-icon-btn" data-wa-close-modal>&times;</button>
      </div>
      <form data-wa-start-form class="wa-modal__body">
        <label class="wa-field">
          <span>WhatsApp Phone Number *</span>
          <input type="text" name="phone" class="input" required placeholder="e.g. +201150949593 or 01012345678"/>
        </label>
        <label class="wa-field">
          <span>Contact Name</span>
          <input type="text" name="customer_name" class="input" placeholder="e.g. Ahmed Tarek"/>
        </label>
        <label class="wa-field">
          <span>WhatsApp @username (Optional)</span>
          <input type="text" name="username" class="input" placeholder="e.g. @ahmedtarek"/>
        </label>
        <label class="wa-field">
          <span>Initial Action</span>
          <select name="mode" class="input" data-wa-start-mode>
            <option value="template">Send Meta Approved Template (24h+ safe)</option>
            <option value="text">Send Direct Text Message</option>
            <option value="contact_only">Add Contact Only (Send media/voice next)</option>
          </select>
        </label>
        <div data-wa-start-content-wrap>
          <label class="wa-field">
            <span data-wa-start-content-label>Template Name</span>
            <input type="text" name="content" class="input" value="hello_world" placeholder="hello_world"/>
          </label>
          <label class="wa-field" data-wa-start-lang-wrap>
            <span>Template Language</span>
            <input type="text" name="template_language" class="input" value="en_US" placeholder="en_US or ar"/>
          </label>
        </div>
        <div class="wa-modal__foot">
          <button type="button" class="btn btn--ghost btn--sm" data-wa-close-modal>Cancel</button>
          <button type="submit" class="wa-primary-btn">Start Conversation</button>
        </div>
      </form>
    </div>
  </div>

  {{-- 2. Unread Email Notification Settings Modal --}}
  <div class="wa-modal-backdrop" data-wa-modal="notifications" hidden>
    <div class="wa-modal">
      <div class="wa-modal__head">
        <h3>Unread WhatsApp → Email Alerts</h3>
        <button type="button" class="wa-icon-btn" data-wa-close-modal>&times;</button>
      </div>
      <form data-wa-notif-form class="wa-modal__body">
        <label style="display:flex;align-items:center;gap:10px;font-weight:600;font-size:0.9rem;cursor:pointer;">
          <input type="checkbox" name="enabled" value="1" @checked($notificationSettings['enabled'])/>
          <span>Email me automatically when a new WhatsApp message isn't seen</span>
        </label>
        <label class="wa-field">
          <span>Notification Email(s)</span>
          <input type="text" name="email" class="input" value="{{ $notificationSettings['email'] }}" placeholder="info@empetix.com, mohanad@gmail.com"/>
          <small style="color:var(--text-muted);font-size:0.75rem;">Separate multiple addresses with commas. Sends immediately if no admin has WhatsApp open, or after the grace delay below.</small>
        </label>
        <label class="wa-field">
          <span>Grace Delay When Dashboard Is Open</span>
          <select name="delay_seconds" class="input">
            <option value="0" @selected($notificationSettings['delay_seconds'] == 0)>Immediate (0s)</option>
            <option value="60" @selected($notificationSettings['delay_seconds'] == 60)>After 1 minute unseen</option>
            <option value="90" @selected($notificationSettings['delay_seconds'] == 90)>After 1.5 minutes unseen (Recommended)</option>
            <option value="180" @selected($notificationSettings['delay_seconds'] == 180)>After 3 minutes unseen</option>
            <option value="300" @selected($notificationSettings['delay_seconds'] == 300)>After 5 minutes unseen</option>
          </select>
        </label>
        <div class="wa-modal__foot">
          <button type="button" class="btn btn--ghost btn--sm" data-wa-close-modal>Cancel</button>
          <button type="submit" class="wa-primary-btn">Save Alert Settings</button>
        </div>
      </form>
    </div>
  </div>

  {{-- 3. Share Location Modal --}}
  <div class="wa-modal-backdrop" data-wa-modal="location" hidden>
    <div class="wa-modal">
      <div class="wa-modal__head">
        <h3>Share Location Pin</h3>
        <button type="button" class="wa-icon-btn" data-wa-close-modal>&times;</button>
      </div>
      <form data-wa-location-form class="wa-modal__body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <label class="wa-field">
            <span>Latitude *</span>
            <input type="number" step="any" name="latitude" class="input" required placeholder="30.0444"/>
          </label>
          <label class="wa-field">
            <span>Longitude *</span>
            <input type="number" step="any" name="longitude" class="input" required placeholder="31.2357"/>
          </label>
        </div>
        <button type="button" class="btn btn--ghost btn--sm" data-wa-geo-detect style="justify-self:start;">📍 Use My Current Location</button>
        <label class="wa-field">
          <span>Location Title</span>
          <input type="text" name="location_name" class="input" placeholder="Empetix Solutions HQ"/>
        </label>
        <label class="wa-field">
          <span>Address</span>
          <input type="text" name="location_address" class="input" placeholder="Cairo, Egypt"/>
        </label>
        <div class="wa-modal__foot">
          <button type="button" class="btn btn--ghost btn--sm" data-wa-close-modal>Cancel</button>
          <button type="submit" class="wa-primary-btn">Send Location</button>
        </div>
      </form>
    </div>
  </div>

  {{-- 4. Send Template Modal --}}
  <div class="wa-modal-backdrop" data-wa-modal="template" hidden>
    <div class="wa-modal">
      <div class="wa-modal__head">
        <h3>Send Approved Meta Template</h3>
        <button type="button" class="wa-icon-btn" data-wa-close-modal>&times;</button>
      </div>
      <form data-wa-template-form class="wa-modal__body">
        <label class="wa-field">
          <span>Template Name *</span>
          <input type="text" name="template_name" class="input" value="hello_world" required placeholder="hello_world"/>
        </label>
        <label class="wa-field">
          <span>Language Code</span>
          <input type="text" name="template_language" class="input" value="en_US" required placeholder="en_US or ar"/>
        </label>
        <div class="wa-modal__foot">
          <button type="button" class="btn btn--ghost btn--sm" data-wa-close-modal>Cancel</button>
          <button type="submit" class="wa-primary-btn">Send Template</button>
        </div>
      </form>
    </div>
  </div>

  {{-- 5. Fullscreen Photo Lightbox --}}
  <div class="wa-lightbox" data-wa-lightbox hidden>
    <div class="wa-lightbox__top">
      <span data-wa-lightbox-caption>Photo</span>
      <div style="display:flex;gap:8px;">
        <a href="#" download class="wa-icon-btn" data-wa-lightbox-download style="color:#fff;" title="Download">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        </a>
        <button type="button" class="wa-icon-btn" data-wa-lightbox-close style="color:#fff;font-size:1.3rem;">&times;</button>
      </div>
    </div>
    <img src="" alt="" data-wa-lightbox-img/>
  </div>
</div>
@endsection
