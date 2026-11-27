@extends('layouts.admin')

@section('title', 'AI Copilot')
@section('crumb', 'Operational assistant & automated CMS tasks')

@section('actions')
  @can('manage settings')
    <a class="btn btn--ghost btn--sm" href="{{ route('admin.settings.edit', ['section' => 'chatbot']) }}" wire:navigate>
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
      <span>AI &amp; Chatbot Settings</span>
    </a>
  @endcan
@endsection

@section('content')
@php
  $currentAdminName = auth()->user()?->name ?? 'Admin';
  $userInitial = strtoupper(substr($currentAdminName, 0, 1));
  $activeTitle = ($activeConversation['title'] ?? null);
  if (! $activeTitle || $activeTitle === 'Beginning a new conversation') {
      $activeTitle = 'Empetix Command Copilot';
  }
@endphp

<div
  class="ai-workspace"
  data-copilot-root
  data-user-initial="{{ $userInitial }}"
  data-initial-conversation-id="{{ $activeConversation['id'] ?? '' }}"
  data-url-message="{{ route('admin.assistant.message') }}"
  data-url-conversations="{{ route('admin.assistant.conversations') }}"
  data-url-show-template="{{ route('admin.assistant.show', '__CONV__') }}"
  data-url-approve-template="{{ route('admin.assistant.approve', '__CONV__') }}"
  data-url-delete-template="{{ route('admin.assistant.destroy', '__CONV__') }}"
>
  @if($activeConversation)
    <script type="application/json" data-ai-initial-payload>{!! json_encode($activeConversation, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) !!}</script>
  @endif

  {{-- Left Sidebar: Sessions & Permissions Inspector --}}
  <aside class="ai-rail">
    <div class="ai-rail__card">
      <button type="button" class="ai-new-session-btn" data-ai-new>
        <span class="ai-new-session-btn__ico" aria-hidden="true">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        </span>
        <span>New Copilot Session</span>
      </button>

      <div class="ai-rail__label">
        <span>Recent Sessions</span>
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </div>

      <ul class="ai-conv-list" data-ai-conv-list>
        @forelse($conversations as $conv)
          @php($convTitle = (! $conv['title'] || $conv['title'] === 'Beginning a new conversation') ? 'Recent Session' : $conv['title'])
          <li class="ai-conv-item {{ ($activeConversation['id'] ?? '') === $conv['id'] ? 'is-active' : '' }}" data-conv-select="{{ $conv['id'] }}">
            <span class="ai-conv-item__icon" aria-hidden="true">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </span>
            <div class="ai-conv-item__main">
              <div class="ai-conv-item__title" dir="auto">{{ $convTitle }}</div>
              <div class="ai-conv-item__meta">{{ $conv['updated_human'] }}</div>
            </div>
            <button type="button" class="ai-conv-item__del" data-conv-delete="{{ $conv['id'] }}" title="Delete session" aria-label="Delete session">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </li>
        @empty
          <li class="ai-conv-empty">No saved sessions yet. Select a command or ask below.</li>
        @endforelse
      </ul>
    </div>

    <div class="ai-rail__card">
      <details open>
        <summary class="ai-cap-summary">
          <span class="ai-cap-summary__left">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
            <span>Authorized Tools</span>
          </span>
          <span class="ai-cap-count">{{ count($diagnostics['tools']) }} Active</span>
        </summary>
        <p class="ai-cap-desc">
          Scoped to your RBAC permissions. Sensitive actions pause for human confirmation.
        </p>
        <div class="ai-tools-pills">
          @foreach($diagnostics['tools'] as $toolName)
            <span class="ai-tool-tag">{{ trim((string) preg_replace('/(?<!^)[A-Z]/', ' $0', $toolName)) }}</span>
          @endforeach
        </div>
      </details>
    </div>
  </aside>

  {{-- Main Copilot Stage --}}
  <section class="ai-stage" aria-label="Empetix Command Copilot">
    <header class="ai-stage__head">
      <div class="ai-stage__identity">
        <div class="ai-stage__badge" aria-hidden="true">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2.5L14.35 8.65L20.5 11L14.35 13.35L12 19.5L9.65 13.35L3.5 11L9.65 8.65L12 2.5Z" fill="url(#copilotHeadGrad)" stroke="#FF784F" stroke-width="1.2" stroke-linejoin="round"/>
            <path d="M19 3L19.75 5.25L22 6L19.75 6.75L19 9L18.25 6.75L16 6L18.25 5.25L19 3Z" fill="#FFFFFF"/>
            <circle cx="6" cy="18" r="1.35" fill="#FF784F"/>
            <defs>
              <linearGradient id="copilotHeadGrad" x1="4" y1="3" x2="20" y2="19" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FF8661"/>
                <stop offset="1" stop-color="#FF4D1C"/>
              </linearGradient>
            </defs>
          </svg>
        </div>
        <div style="min-width:0;">
          <h2 class="ai-stage__title" data-ai-active-title>{{ $activeTitle }}</h2>
          <p class="ai-stage__sub">Bilingual (العربية &amp; English) &middot; Connected to live CMS &amp; WhatsApp repositories</p>
        </div>
      </div>

      <span class="ai-status-pill {{ $diagnostics['ready'] ? '' : 'is-warn' }}">
        <span class="ai-status-pill__dot"></span>
        <span>{{ strtoupper($diagnostics['provider']) }}{{ $diagnostics['model'] ? ' · '.$diagnostics['model'] : '' }} {{ $diagnostics['ready'] ? 'Online' : 'Needs Key' }}</span>
      </span>
    </header>

    @if(! $diagnostics['ready'] && $diagnostics['hint'])
      <div class="ai-alert" role="status">
        <strong>Setup Note:</strong> {{ $diagnostics['hint'] }}
      </div>
    @endif

    <div class="ai-messages" data-ai-messages>
      <div class="ai-empty" data-ai-empty @if(!empty($activeConversation['messages'])) hidden @endif>
        <div class="ai-empty__emblem" aria-hidden="true">
          <span class="ai-empty__ring"></span>
          <div class="ai-empty__halo">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 2.25L14.55 8.45L20.75 11L14.55 13.55L12 19.75L9.45 13.55L3.25 11L9.45 8.45L12 2.25Z" fill="url(#copilotHeroGrad)" stroke="#FFA082" stroke-width="1.15" stroke-linejoin="round"/>
              <path d="M19.2 2.8L20 5.1L22.3 5.9L20 6.7L19.2 9L18.4 6.7L16.1 5.9L18.4 5.1L19.2 2.8Z" fill="#FFFFFF"/>
              <path d="M5.5 16.2L6.05 17.75L7.6 18.3L6.05 18.85L5.5 20.4L4.95 18.85L3.4 18.3L4.95 17.75L5.5 16.2Z" fill="#FF855E"/>
              <defs>
                <linearGradient id="copilotHeroGrad" x1="4" y1="3" x2="20" y2="20" gradientUnits="userSpaceOnUse">
                  <stop stop-color="#FF9472"/>
                  <stop offset="0.55" stop-color="#FF5726"/>
                  <stop offset="1" stop-color="#E03D10"/>
                </linearGradient>
              </defs>
            </svg>
          </div>
        </div>

        <span class="ai-empty__kicker">Empetix Neural Operations &amp; Technical Architect</span>
        <h3 class="ai-empty__title">Ready when you are, {{ explode(' ', trim($currentAdminName))[0] }}.</h3>
        <p class="ai-empty__sub">Ask any technical question about the platform (routes, database schema, code, logs), upload screenshots/files for analysis, or delegate CMS &amp; WhatsApp operations.</p>

        <div class="ai-starters">
          {{-- 1. Technical Architecture & Logs --}}
          <button type="button" class="ai-starter-btn" data-ai-prompt="Inspect the technical architecture of our website: summarize the backend stack, database tables, AI/WhatsApp integration, and check if there are any recent errors in laravel.log.">
            <span class="ai-starter-btn__ico ai-starter-btn__ico--orange" aria-hidden="true">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="16 18 22 12 16 6"/>
                <polyline points="8 6 2 12 8 18"/>
                <line x1="14" y1="4" x2="10" y2="20"/>
              </svg>
            </span>
            <span class="ai-starter-btn__copy">
              <strong>Technical Architecture &amp; Logs</strong>
              <span>Inspect stack, routes, DB schema, code &amp; laravel.log</span>
            </span>
            <span class="ai-starter-btn__arrow" aria-hidden="true">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
            </span>
          </button>

          {{-- 2. Triage Client Briefs --}}
          <button type="button" class="ai-starter-btn" data-ai-prompt="Give me a complete dashboard overview and summarize the latest unread client briefs and support chats.">
            <span class="ai-starter-btn__ico ai-starter-btn__ico--blue" aria-hidden="true">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/>
                <path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/>
              </svg>
            </span>
            <span class="ai-starter-btn__copy">
              <strong>Executive Overview &amp; Briefs</strong>
              <span>Live KPIs, unread inquiries &amp; active support threads</span>
            </span>
            <span class="ai-starter-btn__arrow" aria-hidden="true">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
            </span>
          </button>

          {{-- 3. Review Candidates --}}
          <button type="button" class="ai-starter-btn" data-ai-prompt="List new unreviewed job applications and highlight candidates' experience.">
            <span class="ai-starter-btn__ico ai-starter-btn__ico--emerald" aria-hidden="true">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <polyline points="16 11 18 13 22 9"/>
              </svg>
            </span>
            <span class="ai-starter-btn__copy">
              <strong>Review Candidates</strong>
              <span>Inspect new job applicants &amp; shortlist talent</span>
            </span>
            <span class="ai-starter-btn__arrow" aria-hidden="true">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
            </span>
          </button>

          {{-- 4. Draft Blog Article --}}
          <button type="button" class="ai-starter-btn" data-ai-prompt="Draft a new technical blog post about building resilient multi-tenant SaaS platforms with Laravel.">
            <span class="ai-starter-btn__ico ai-starter-btn__ico--violet" aria-hidden="true">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 20h9"/>
                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
              </svg>
            </span>
            <span class="ai-starter-btn__copy">
              <strong>Draft Blog Article</strong>
              <span>Generate an unpublished Markdown post for review</span>
            </span>
            <span class="ai-starter-btn__arrow" aria-hidden="true">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
            </span>
          </button>
        </div>
      </div>
    </div>

    <div class="ai-composer-wrap">
      <div class="ai-attachments-tray" data-ai-attachments-preview hidden></div>
      <form class="ai-composer" data-ai-form enctype="multipart/form-data">
        <input
          type="file"
          data-ai-file-input
          multiple
          hidden
          accept="image/*,.pdf,.txt,.md,.log,.json,.csv,.xml,.sql,.php,.js,.ts,.css,.html,.yml,.yaml"
        />
        <button
          type="button"
          class="ai-composer__attach"
          data-ai-attach-btn
          title="Attach images, screenshots, logs, PDFs, or code files (deleted automatically when session is deleted)"
          aria-label="Attach files"
        >
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
        </button>
        <label for="ai-workspace-input" class="ai-sr-only">Message Empetix Copilot</label>
        <textarea
          id="ai-workspace-input"
          class="ai-composer__input"
          data-ai-input
          rows="1"
          dir="auto"
          placeholder="Ask any technical question, attach screenshots/logs/code, or run CMS tasks… (العربية / English)"
        ></textarea>
        <button type="submit" class="ai-composer__send" data-ai-send title="Send message" aria-label="Send message">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
        </button>
      </form>
      <div class="ai-composer-hint">
        <span><kbd>Enter</kbd> to send &middot; Paste or attach up to 5 files (auto-deleted when session is deleted)</span>
        <span class="ai-composer-hint__guard">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          Destructive &amp; public actions always ask for approval
        </span>
      </div>
    </div>
  </section>
</div>
@endsection
