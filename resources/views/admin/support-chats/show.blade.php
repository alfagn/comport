@extends('layouts.admin')

@section('title', ($session->isWhatsapp() ? 'WhatsApp' : 'Web').' Chat #'.$session->id.' — '.$session->visitorLabel())
@section('crumb', 'AI & WhatsApp Support')

@section('actions')
  @if($session->contactMessage)
    <a class="btn btn--primary btn--sm" href="{{ route('admin.inbox.show', $session->contactMessage->id) }}" wire:navigate>Open Linked Inbox Brief &rarr;</a>
  @endif
  <a class="btn btn--ghost btn--sm" href="{{ route('admin.support-chats.index') }}" wire:navigate>&larr; All Support Chats</a>
@endsection

@section('content')
<div class="card" style="margin-bottom:20px;">
  <div class="meta-grid">
    <div class="meta"><b>Customer</b>{{ $session->visitorLabel() }}</div>
    <div class="meta">
      <b>Channel</b>
      @if($session->isWhatsapp())
        <span style="color:#25D366;font-weight:700;">WhatsApp Cloud API</span>
      @else
        Website Live Widget
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
    <div class="meta"><b>Status</b>{{ $session->statusLabel() }}</div>
    <div class="meta"><b>Turns</b>{{ $session->message_count }}</div>
  </div>

  @if($session->handoff_summary)
    <div style="margin:14px 0;padding:13px 17px;border-radius:14px;background:var(--accent-soft);border:1px solid rgba(255,77,28,0.32);">
      <strong style="display:block;font-size:0.74rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--accent);margin-bottom:4px;">AI Handoff Summary</strong>
      <div dir="auto" style="font-size:0.9rem;color:var(--text);line-height:1.55;">{{ $session->handoff_summary }}</div>
    </div>
  @endif

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

      @if($session->isWhatsapp())
        <form method="post" action="{{ route('admin.support-chats.ai.toggle', $session->id) }}">
          @csrf @method('PATCH')
          <button type="submit" class="btn btn--ghost btn--sm">
            {{ $session->ai_paused ? '▶ Resume AI Auto-Reply' : '⏸ Pause AI (Human Takeover)' }}
          </button>
        </form>
      @endif
    </div>

    <form method="post" action="{{ route('admin.support-chats.destroy', $session->id) }}" data-confirm="Delete this support chat and its transcript?" data-confirm-title="Delete Support Chat">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn--danger btn--sm">Delete Chat</button>
    </form>
  </div>
</div>

<div class="card">
  <h2 class="card__title" style="margin-bottom:18px;">Conversation Transcript</h2>

  @if(empty($transcript))
    <p style="color:var(--text-muted);margin:0;">No messages recorded in this session yet.</p>
  @else
    <div style="display:flex;flex-direction:column;gap:18px;">
      @foreach($transcript as $msg)
        @php
          $isVisitor = $msg['role'] === 'user';
          $isHumanAgent = ($msg['source'] ?? null) === 'human_agent';
          $authorLabel = $isVisitor
              ? $session->visitorLabel()
              : ($isHumanAgent ? 'Human Team Reply (WhatsApp)' : 'Empetix AI Concierge');
        @endphp
        <div class="ai-row {{ $isVisitor ? 'ai-row--user' : 'ai-row--assistant' }}">
          @if($isVisitor)
            <div class="ai-avatar ai-avatar--user">{{ strtoupper(substr($session->visitorLabel(), 0, 1)) }}</div>
          @else
            <div class="ai-avatar ai-avatar--bot"><img src="{{ asset('assets/images/3d/3d-chat-bubbles.png') }}" alt="" width="18" height="18"/></div>
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
    </div>
  @endif

  @if($session->isWhatsapp())
    <div style="margin-top:24px;padding-top:20px;border-top:1px solid var(--line);">
      <h3 style="font-family:var(--font-display);font-size:0.95rem;margin:0 0 10px;">Send Direct WhatsApp Reply to {{ $session->visitor_phone }}</h3>
      @if(! $whatsappConfigured)
        <p style="font-size:0.84rem;color:var(--warning,#fbbf24);margin:0;">Configure your Meta Phone Number ID &amp; Access Token in WhatsApp Settings to send live outbound replies.</p>
      @else
        <form method="post" action="{{ route('admin.support-chats.reply', $session->id) }}">
          @csrf
          <div class="ai-composer" style="margin-bottom:10px;">
            <label for="wa-human-reply" class="ai-sr-only">WhatsApp reply</label>
            <textarea
              id="wa-human-reply"
              name="message"
              class="ai-composer__input"
              rows="2"
              dir="auto"
              required
              placeholder="Write a direct WhatsApp message to {{ $session->visitorLabel() }}…"
            ></textarea>
            <button type="submit" class="ai-composer__send" title="Send to WhatsApp" aria-label="Send to WhatsApp">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
            </button>
          </div>
          <label style="display:inline-flex;align-items:center;gap:8px;font-size:0.8rem;color:var(--text-muted);cursor:pointer;">
            <input type="checkbox" name="pause_ai" value="1" @checked($session->ai_paused || true)/>
            <span>Pause AI auto-reply on this thread while I chat with the customer</span>
          </label>
        </form>
      @endif
    </div>
  @endif
</div>
@endsection
