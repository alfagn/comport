{{-- Slide-over AI Copilot drawer available from any dashboard screen (Alt+A). --}}
@php($drawerInitial = strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)))
<div class="copilot-drawer-backdrop" id="copilot-drawer-backdrop" onclick="empetixCloseCopilotDrawer()"></div>
<aside
  class="copilot-drawer"
  id="copilot-drawer"
  aria-label="AI Copilot Drawer"
  aria-hidden="true"
  data-copilot-root
  data-user-initial="{{ $drawerInitial }}"
  data-initial-conversation-id=""
  data-url-message="{{ route('admin.assistant.message') }}"
  data-url-conversations="{{ route('admin.assistant.conversations') }}"
  data-url-show-template="{{ route('admin.assistant.show', '__CONV__') }}"
  data-url-approve-template="{{ route('admin.assistant.approve', '__CONV__') }}"
  data-url-delete-template="{{ route('admin.assistant.destroy', '__CONV__') }}"
>
  <header class="ai-stage__head">
    <div class="ai-stage__identity">
      <div class="ai-stage__badge" aria-hidden="true">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 2.5L14.35 8.65L20.5 11L14.35 13.35L12 19.5L9.65 13.35L3.5 11L9.65 8.65L12 2.5Z" fill="#FF5E36" stroke="#FFA082" stroke-width="1.2" stroke-linejoin="round"/>
          <path d="M19 3L19.75 5.25L22 6L19.75 6.75L19 9L18.25 6.75L16 6L18.25 5.25L19 3Z" fill="#FFFFFF"/>
        </svg>
      </div>
      <div style="min-width:0;">
        <h2 class="ai-stage__title" data-ai-active-title>Command Copilot</h2>
        <p class="ai-stage__sub"><a href="{{ route('admin.assistant.index') }}" wire:navigate class="ai-drawer-studio-link">Open full studio &rarr;</a></p>
      </div>
    </div>
    <div style="display:flex;gap:6px;align-items:center;">
      <button type="button" class="ai-head-btn" data-ai-new title="New session">New</button>
      <button type="button" class="ai-head-btn ai-head-btn--icon" onclick="empetixCloseCopilotDrawer()" aria-label="Close Copilot">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
  </header>

  <div class="ai-messages" data-ai-messages style="padding:18px;">
    <div class="ai-empty" data-ai-empty>
      <div class="ai-empty__emblem" style="margin-bottom:12px;" aria-hidden="true">
        <div class="ai-empty__halo" style="width:56px;height:56px;border-radius:17px;">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2.5L14.35 8.65L20.5 11L14.35 13.35L12 19.5L9.65 13.35L3.5 11L9.65 8.65L12 2.5Z" fill="#FF5E36" stroke="#FFA082" stroke-width="1.2" stroke-linejoin="round"/>
            <path d="M19 3L19.75 5.25L22 6L19.75 6.75L19 9L18.25 6.75L16 6L18.25 5.25L19 3Z" fill="#FFFFFF"/>
          </svg>
        </div>
      </div>
      <span class="ai-empty__kicker">Quick Dispatch</span>
      <h3 class="ai-empty__title" style="font-size:1.14rem;">Quick Command Copilot</h3>
      <p class="ai-empty__sub" style="margin-bottom:18px;font-size:0.84rem;">Execute CMS tasks from anywhere in the dashboard without leaving your current screen.</p>
      <div class="ai-starters" style="grid-template-columns:1fr;">
        <button type="button" class="ai-starter-btn" data-ai-prompt="What needs my attention today across inbox briefs, job applications, and support chats?">
          <span class="ai-starter-btn__ico ai-starter-btn__ico--orange" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 3v18h18"/><path d="m7 15 4-4 3 3 5-6"/>
            </svg>
          </span>
          <span class="ai-starter-btn__copy">
            <strong>What needs attention today?</strong>
            <span>Check unread briefs, applications &amp; handoffs</span>
          </span>
          <span class="ai-starter-btn__arrow" aria-hidden="true">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
          </span>
        </button>

        <button type="button" class="ai-starter-btn" data-ai-prompt="List recent customer support and WhatsApp chats that were handed off to the team.">
          <span class="ai-starter-btn__ico ai-starter-btn__ico--emerald" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
              <path d="M8 10h.01"/><path d="M12 10h.01"/><path d="M16 10h.01"/>
            </svg>
          </span>
          <span class="ai-starter-btn__copy">
            <strong>Handed-off customer chats</strong>
            <span>Review Website &amp; WhatsApp leads captured today</span>
          </span>
          <span class="ai-starter-btn__arrow" aria-hidden="true">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
          </span>
        </button>
      </div>
    </div>
  </div>

  <div class="ai-composer-wrap" style="padding:10px 16px 14px;">
    <form class="ai-composer" data-ai-form>
      <span class="ai-composer__spark" aria-hidden="true">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/>
        </svg>
      </span>
      <label for="ai-drawer-input" class="ai-sr-only">Ask Copilot</label>
      <textarea
        id="ai-drawer-input"
        class="ai-composer__input"
        data-ai-input
        rows="1"
        dir="auto"
        placeholder="Ask Copilot… (العربية / English)"
      ></textarea>
      <button type="submit" class="ai-composer__send" data-ai-send title="Send" aria-label="Send">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
      </button>
    </form>
  </div>
</aside>
