@extends('layouts.admin')

@section('title', ($session->isWhatsapp() ? 'WhatsApp' : 'Web').' Chat #'.$session->id.' — '.$session->visitorLabel())
@section('crumb', 'AI & WhatsApp Support · Live Runtime')

@section('actions')
  @if($session->contactMessage)
    <a class="btn btn--primary btn--sm" href="{{ route('admin.inbox.show', $session->contactMessage->id) }}" wire:navigate data-live-inbox-link>Open Linked Inbox Brief &rarr;</a>
  @endif
  <a class="btn btn--ghost btn--sm" href="{{ route('admin.support-chats.index') }}" wire:navigate>&larr; All Support Chats</a>
@endsection

@section('content')
<div
  id="live-support-session-root"
  data-live-url="{{ route('admin.support-chats.live', $session->id) }}"
  data-typing-url="{{ route('admin.support-chats.typing', $session->id) }}"
  data-reply-url="{{ route('admin.support-chats.reply', $session->id) }}"
  data-toggle-ai-url="{{ route('admin.support-chats.ai.toggle', $session->id) }}"
  data-visitor-label="{{ $session->visitorLabel() }}"
  data-visitor-initial="{{ strtoupper(substr($session->visitorLabel(), 0, 1)) }}"
  data-is-whatsapp="{{ $session->isWhatsapp() ? '1' : '0' }}"
>
  <div class="card" style="margin-bottom:20px;">
    <div class="meta-grid">
      <div class="meta"><b>Customer</b><span data-live-visitor-label>{{ $session->visitorLabel() }}</span></div>
      <div class="meta">
        <b>Channel</b>
        @if($session->isWhatsapp())
          <span style="color:#25D366;font-weight:700;">WhatsApp Cloud API</span>
        @else
          <span style="color:var(--accent);font-weight:700;">Website Live Widget</span>
        @endif
      </div>
      @if($session->visitor_phone)
        <div class="meta"><b>WhatsApp Phone</b>{{ $session->visitor_phone }}</div>
      @endif
      <div class="meta">
        <b>Email</b>
        @if($session->visitor_email)
          <a href="mailto:{{ $session->visitor_email }}" style="color:var(--accent)">{{ $session->visitor_email }}</a>
        @else
          —
        @endif
      </div>
      <div class="meta"><b>Status</b><span data-live-status-label>{{ $session->statusLabel() }}</span></div>
      <div class="meta"><b>Turns</b><span data-live-turns>{{ $session->message_count }}</span></div>
    </div>

    <div
      data-live-handoff-box
      @if(! $session->handoff_summary) hidden @endif
      style="margin:14px 0;padding:13px 17px;border-radius:14px;background:var(--accent-soft);border:1px solid rgba(255,77,28,0.32);"
    >
      <strong style="display:block;font-size:0.74rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--accent);margin-bottom:4px;">AI Handoff Summary</strong>
      <div dir="auto" data-live-handoff-text style="font-size:0.9rem;color:var(--text);line-height:1.55;">{{ $session->handoff_summary }}</div>
    </div>

    <div class="form-actions" style="justify-content:space-between;flex-wrap:wrap;gap:10px;">
      <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <form method="post" action="{{ route('admin.support-chats.status.update', $session->id) }}" style="display:inline-flex;gap:8px;align-items:center;">
          @csrf @method('PATCH')
          <select name="status" class="input" style="width:auto;min-width:145px;height:38px;padding:0 12px;">
            @foreach(\App\Models\SupportChatSession::STATUS_LABELS as $value => $label)
              <option value="{{ $value }}" @selected($session->status === $value)>{{ $label }}</option>
            @endforeach
          </select>
          <button type="submit" class="btn btn--ghost btn--sm">Save Status</button>
        </form>

        <form method="post" action="{{ route('admin.support-chats.ai.toggle', $session->id) }}" data-live-toggle-ai-form>
          @csrf @method('PATCH')
          <button type="submit" class="btn btn--ghost btn--sm" data-live-toggle-ai-btn>
            {{ $session->ai_paused ? '▶ Resume AI Auto-Reply' : '⏸ Pause AI (Human Takeover)' }}
          </button>
        </form>
      </div>

      <form method="post" action="{{ route('admin.support-chats.destroy', $session->id) }}" data-confirm="Delete this support chat and its transcript?" data-confirm-title="Delete Support Chat">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn--danger btn--sm">Delete Chat</button>
      </form>
    </div>
  </div>

  {{-- Live WhatsApp-Style Transcript & Real-Time Presence Studio --}}
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--line);">
      <div>
        <h2 class="card__title" style="margin:0;">Conversation Transcript</h2>
        <p style="font-size:0.78rem;color:var(--text-muted);margin:3px 0 0;">Real-time runtime stream — incoming messages and customer typing appear automatically.</p>
      </div>

      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        {{-- Live Customer / AI Typing Indicator Badge --}}
        <span
          data-live-typing-pill
          @if(empty($presence['customer_typing']) && empty($presence['ai_typing'])) hidden @endif
          class="ai-status-pill"
          style="background:rgba(16,185,129,0.14);border-color:rgba(16,185,129,0.38);color:#10B981;"
        >
          <span class="ai-status-pill__dot" style="background:#10B981;"></span>
          <span data-live-typing-pill-text>
            @if(!empty($presence['customer_typing']))
              {{ $session->visitorLabel() }} is typing right now…
            @elseif(!empty($presence['ai_typing']))
              AI Concierge is generating a reply…
            @endif
          </span>
        </span>

        {{-- Live Connection Pill --}}
        <span class="ai-status-pill" style="background:var(--surface-2);border-color:var(--line-strong);color:var(--text);">
          <span class="ai-status-pill__dot"></span>
          <span data-live-connection-label>Live Runtime Connected</span>
        </span>
      </div>
    </div>

    <div
      data-live-transcript-stream
      style="display:flex;flex-direction:column;gap:18px;max-height:560px;overflow-y:auto;padding:6px 4px;"
    >
      @if(empty($transcript))
        <p data-live-empty style="color:var(--text-muted);margin:0;">No messages recorded in this session yet — waiting for customer input…</p>
      @else
        @foreach($transcript as $msg)
          @php
            $isVisitor = $msg['role'] === 'user';
            $isHumanAgent = ($msg['source'] ?? null) === 'human_agent';
            $authorLabel = $isVisitor
                ? $session->visitorLabel()
                : ($isHumanAgent ? 'Human Specialist Reply' : 'Empetix AI Concierge');
          @endphp
          <div class="ai-row {{ $isVisitor ? 'ai-row--user' : 'ai-row--assistant' }}" data-msg-id="{{ $msg['id'] }}">
            @if($isVisitor)
              <div class="ai-avatar ai-avatar--user">{{ strtoupper(substr($session->visitorLabel(), 0, 1)) }}</div>
            @else
              <div class="ai-avatar ai-avatar--bot" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M12 2.5L14.35 8.65L20.5 11L14.35 13.35L12 19.5L9.65 13.35L3.5 11L9.65 8.65L12 2.5Z" fill="#FF5E36" stroke="#FFA082" stroke-width="1.2" stroke-linejoin="round"/>
                  <path d="M19 3L19.75 5.25L22 6L19.75 6.75L19 9L18.25 6.75L16 6L18.25 5.25L19 3Z" fill="#FFFFFF"/>
                </svg>
              </div>
            @endif

            <div class="ai-msg">
              <div class="ai-msg__meta">
                <span class="ai-msg__author">{{ $authorLabel }}</span>
                <span>&middot; {{ $msg['timestamp'] }}</span>
              </div>
              @if(!empty($msg['tools']))
                <div class="ai-msg__tools">
                  @foreach($msg['tools'] as $toolName)
                    <span class="ai-tool-exec">✓ {{ trim((string) preg_replace('/(?<!^)[A-Z]/', ' $0', $toolName)) }}</span>
                  @endforeach
                </div>
              @endif
              <div class="ai-msg__bubble" dir="auto">{!! $msg['html'] !!}</div>
            </div>
          </div>
        @endforeach
      @endif

      {{-- Live Customer Typing Bubble inside Chat Stream (WhatsApp Style) --}}
      <div
        class="ai-row ai-row--user"
        data-live-customer-typing-row
        @if(empty($presence['customer_typing'])) hidden @endif
      >
        <div class="ai-avatar ai-avatar--user">{{ strtoupper(substr($session->visitorLabel(), 0, 1)) }}</div>
        <div class="ai-msg">
          <div class="ai-msg__meta">
            <span class="ai-msg__author" style="color:#10B981;">{{ $session->visitorLabel() }} &middot; typing live…</span>
          </div>
          <div class="ai-typing" style="display:inline-flex;align-items:center;gap:8px;">
            <span></span><span></span><span></span>
            <em data-live-customer-preview style="font-size:0.78rem;color:var(--text-muted);font-style:normal;max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
              {{ $presence['customer_preview'] ?? '' }}
            </em>
          </div>
        </div>
      </div>

      {{-- Live AI Generating Bubble inside Chat Stream --}}
      <div
        class="ai-row ai-row--assistant"
        data-live-ai-typing-row
        @if(empty($presence['ai_typing'])) hidden @endif
      >
        <div class="ai-avatar ai-avatar--bot" aria-hidden="true">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M12 2.5L14.35 8.65L20.5 11L14.35 13.35L12 19.5L9.65 13.35L3.5 11L9.65 8.65L12 2.5Z" fill="#FF5E36" stroke="#FFA082" stroke-width="1.2" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="ai-msg">
          <div class="ai-msg__meta">
            <span class="ai-msg__author">Empetix AI Concierge &middot; generating reply…</span>
          </div>
          <div class="ai-typing"><span></span><span></span><span></span></div>
        </div>
      </div>
    </div>

    {{-- Unified Real-Time Human Reply Composer (Works for both WhatsApp and Website Live Chats) --}}
    <div style="margin-top:24px;padding-top:20px;border-top:1px solid var(--line);">
      <h3 style="font-family:var(--font-display);font-size:0.95rem;margin:0 0 10px;">
        @if($session->isWhatsapp())
          Send Live WhatsApp Reply to {{ $session->visitor_phone }}
        @else
          Send Live Human Reply to Website Visitor ({{ $session->visitorLabel() }})
        @endif
      </h3>
      @if($session->isWhatsapp() && ! $whatsappConfigured)
        <p style="font-size:0.84rem;color:var(--warning,#fbbf24);margin:0;">Configure your Meta Phone Number ID &amp; Access Token in WhatsApp Settings to send live outbound WhatsApp replies.</p>
      @else
        <form method="post" action="{{ route('admin.support-chats.reply', $session->id) }}" data-live-reply-form>
          @csrf
          <div class="ai-composer" style="margin-bottom:10px;">
            <label for="live-human-reply" class="ai-sr-only">Direct reply</label>
            <textarea
              id="live-human-reply"
              name="message"
              data-live-reply-input
              class="ai-composer__input"
              rows="2"
              dir="auto"
              required
              placeholder="Write a real-time message to {{ $session->visitorLabel() }}… (Press Enter to send)"
            ></textarea>
            <button type="submit" class="ai-composer__send" data-live-reply-send title="Send live reply" aria-label="Send live reply">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
            </button>
          </div>
          <label style="display:inline-flex;align-items:center;gap:8px;font-size:0.8rem;color:var(--text-muted);cursor:pointer;">
            <input type="checkbox" name="pause_ai" value="1" data-live-pause-checkbox @checked($session->ai_paused || true)/>
            <span>Pause AI auto-reply on this thread while I chat live with the customer</span>
          </label>
        </form>
      @endif
    </div>
  </div>
</div>

<script>
(function () {
  var root = document.getElementById('live-support-session-root');
  if (!root || root.__liveBound) return;
  root.__liveBound = true;

  var liveUrl = root.getAttribute('data-live-url');
  var typingUrl = root.getAttribute('data-typing-url');
  var replyUrl = root.getAttribute('data-reply-url');
  var visitorLabel = root.getAttribute('data-visitor-label') || 'Visitor';
  var visitorInitial = root.getAttribute('data-visitor-initial') || 'V';
  var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

  var streamEl = root.querySelector('[data-live-transcript-stream]');
  var customerTypingRow = root.querySelector('[data-live-customer-typing-row]');
  var customerPreviewEl = root.querySelector('[data-live-customer-preview]');
  var aiTypingRow = root.querySelector('[data-live-ai-typing-row]');
  var typingPill = root.querySelector('[data-live-typing-pill]');
  var typingPillText = root.querySelector('[data-live-typing-pill-text]');
  var turnsEl = root.querySelector('[data-live-turns]');
  var statusLabelEl = root.querySelector('[data-live-status-label]');
  var handoffBox = root.querySelector('[data-live-handoff-box]');
  var handoffText = root.querySelector('[data-live-handoff-text]');
  var toggleAiBtn = root.querySelector('[data-live-toggle-ai-btn]');
  var replyForm = root.querySelector('[data-live-reply-form]');
  var replyInput = root.querySelector('[data-live-reply-input]');
  var replySend = root.querySelector('[data-live-reply-send]');
  var pauseCheckbox = root.querySelector('[data-live-pause-checkbox]');

  var knownIds = {};
  Array.prototype.forEach.call(root.querySelectorAll('[data-msg-id]'), function (el) {
    knownIds[el.getAttribute('data-msg-id')] = true;
  });

  var lastTypingSent = 0;

  function escapeHtml(str) {
    var d = document.createElement('div');
    d.textContent = str == null ? '' : String(str);
    return d.innerHTML;
  }

  function botAvatarHtml() {
    return (
      '<div class="ai-avatar ai-avatar--bot" aria-hidden="true">' +
        '<svg width="16" height="16" viewBox="0 0 24 24" fill="none">' +
          '<path d="M12 2.5L14.35 8.65L20.5 11L14.35 13.35L12 19.5L9.65 13.35L3.5 11L9.65 8.65L12 2.5Z" fill="#FF5E36" stroke="#FFA082" stroke-width="1.2" stroke-linejoin="round"/>' +
          '<path d="M19 3L19.75 5.25L22 6L19.75 6.75L19 9L18.25 6.75L16 6L18.25 5.25L19 3Z" fill="#FFFFFF"/>' +
        '</svg>' +
      '</div>'
    );
  }

  function scrollStreamBottom() {
    if (streamEl) streamEl.scrollTop = streamEl.scrollHeight;
  }

  function buildMessageHtml(msg, currentVisitorLabel, currentVisitorInitial) {
    var isVisitor = msg.role === 'user';
    var isHuman = msg.source === 'human_agent';
    var author = isVisitor ? currentVisitorLabel : (isHuman ? 'Human Specialist Reply' : 'Empetix AI Concierge');
    var avatar = isVisitor
      ? '<div class="ai-avatar ai-avatar--user">' + escapeHtml(currentVisitorInitial) + '</div>'
      : botAvatarHtml();

    var toolsHtml = '';
    if (Array.isArray(msg.tools) && msg.tools.length) {
      toolsHtml = '<div class="ai-msg__tools">' + msg.tools.map(function (t) {
        var readable = String(t).replace(/([A-Z])/g, ' $1').trim();
        return '<span class="ai-tool-exec">✓ ' + escapeHtml(readable) + '</span>';
      }).join('') + '</div>';
    }

    return (
      '<div class="ai-row ' + (isVisitor ? 'ai-row--user' : 'ai-row--assistant') + '" data-msg-id="' + escapeHtml(msg.id) + '">' +
        avatar +
        '<div class="ai-msg">' +
          '<div class="ai-msg__meta">' +
            '<span class="ai-msg__author">' + escapeHtml(author) + '</span>' +
            '<span>&middot; ' + escapeHtml(msg.timestamp || msg.time || '') + '</span>' +
          '</div>' +
          toolsHtml +
          '<div class="ai-msg__bubble" dir="auto">' + (msg.html || escapeHtml(msg.content || '')) + '</div>' +
        '</div>' +
      '</div>'
    );
  }

  function applyLivePayload(data) {
    if (!data || !data.ok) return;

    if (data.session) {
      visitorLabel = data.session.visitor_label || visitorLabel;
      visitorInitial = data.session.visitor_initial || visitorInitial;
      if (turnsEl) turnsEl.textContent = data.session.message_count;
      if (statusLabelEl) statusLabelEl.textContent = data.session.status_label;
      if (handoffBox && handoffText) {
        handoffBox.hidden = !data.session.handoff_summary;
        handoffText.textContent = data.session.handoff_summary || '';
      }
      if (toggleAiBtn) {
        toggleAiBtn.textContent = data.session.ai_paused ? '▶ Resume AI Auto-Reply' : '⏸ Pause AI (Human Takeover)';
      }
    }

    var addedAny = false;
    var messages = Array.isArray(data.messages) ? data.messages : [];
    messages.forEach(function (m) {
      if (!m.id || knownIds[m.id]) return;
      knownIds[m.id] = true;
      addedAny = true;

      var emptyNode = streamEl && streamEl.querySelector('[data-live-empty]');
      if (emptyNode) emptyNode.remove();

      var temp = document.createElement('div');
      temp.innerHTML = buildMessageHtml(m, visitorLabel, visitorInitial);
      var rowNode = temp.firstElementChild;
      if (streamEl && rowNode) {
        streamEl.insertBefore(rowNode, customerTypingRow);
      }
    });

    var p = data.presence || {};
    if (customerTypingRow) {
      customerTypingRow.hidden = !p.customer_typing;
      if (customerPreviewEl) {
        customerPreviewEl.textContent = p.customer_preview ? '“' + p.customer_preview + '”' : '';
      }
    }
    if (aiTypingRow) {
      aiTypingRow.hidden = !p.ai_typing;
    }
    if (typingPill && typingPillText) {
      if (p.customer_typing) {
        typingPill.hidden = false;
        typingPillText.textContent = visitorLabel + ' is typing right now…';
      } else if (p.ai_typing) {
        typingPill.hidden = false;
        typingPillText.textContent = 'AI Concierge is generating a reply…';
      } else {
        typingPill.hidden = true;
      }
    }

    if (addedAny || p.customer_typing || p.ai_typing) {
      scrollStreamBottom();
    }
  }

  function pollLive() {
    if (document.hidden || !liveUrl) return;
    fetch(liveUrl, {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    })
      .then(function (r) { return r.ok ? r.json() : null; })
      .then(applyLivePayload)
      .catch(function () {});
  }

  function sendAdminTyping(isTyping) {
    if (!typingUrl) return;
    var now = Date.now();
    if (isTyping && now - lastTypingSent < 2200) return;
    lastTypingSent = isTyping ? now : 0;

    fetch(typingUrl, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrf
      },
      credentials: 'same-origin',
      body: JSON.stringify({ is_typing: !!isTyping })
    }).catch(function () {});
  }

  if (replyForm && replyInput) {
    replyInput.addEventListener('input', function () {
      var hasText = (replyInput.value || '').trim().length > 0;
      sendAdminTyping(hasText);
    });

    replyInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        replyForm.dispatchEvent(new Event('submit', { cancelable: true }));
      }
    });

    replyForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var msg = (replyInput.value || '').trim();
      if (!msg) return;

      if (replySend) replySend.disabled = true;
      replyInput.disabled = true;

      fetch(replyUrl, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrf
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          message: msg,
          pause_ai: pauseCheckbox ? pauseCheckbox.checked : true
        })
      })
        .then(function (r) { return r.json(); })
        .then(function (body) {
          if (body && body.ok) {
            replyInput.value = '';
            applyLivePayload(body);
            if (window.empetixToast) window.empetixToast('Reply delivered live.', 'success');
          } else {
            var err = (body && body.message) || 'Could not send reply.';
            if (window.empetixToast) window.empetixToast(err, 'error');
          }
        })
        .catch(function () {
          if (window.empetixToast) window.empetixToast('Network error while sending reply.', 'error');
        })
        .finally(function () {
          if (replySend) replySend.disabled = false;
          replyInput.disabled = false;
          replyInput.focus();
        });
    });
  }

  scrollStreamBottom();
  var timer = setInterval(pollLive, 1500);
  document.addEventListener('livewire:navigating', function () { clearInterval(timer); }, { once: true });
})();
</script>
@endsection
