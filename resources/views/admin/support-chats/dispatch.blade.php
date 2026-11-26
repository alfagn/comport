@extends('layouts.admin')

@section('title', 'Send WhatsApp Message')
@section('crumb', 'Support Chats')

@section('actions')
  <a class="btn btn--ghost btn--sm" href="{{ route('admin.support-chats.index') }}" wire:navigate>&larr; All Conversations</a>
@endsection

@section('content')
@if(! $whatsappConfigured)
  <div class="ai-alert" style="margin:0 0 20px;" role="status">
    <strong>WhatsApp Cloud API Not Configured:</strong>
    Add your Meta Phone Number ID &amp; Access Token in
    <a href="{{ route('admin.support-chats.whatsapp') }}" wire:navigate style="color:var(--accent);font-weight:700;">WhatsApp API Setup</a>
    before sending outbound messages.
  </div>
@endif

<div class="support-dispatch-grid">
  <div class="card card--form">
    <h2 class="card__title">Outbound WhatsApp Customer Dispatch</h2>
    <p class="card__sub">Start a new 24h conversation using an approved Meta template, or send a custom message to an active customer thread.</p>

    <form method="post" action="{{ route('admin.support-chats.whatsapp.send') }}" id="wa-dispatch-form">
      @csrf
      <div class="form-grid">
        <x-admin.input
          name="phone"
          label="Customer WhatsApp Number"
          placeholder="+201150949593 or 01150949593"
          :required="true"
        />
        <x-admin.input
          name="customer_name"
          label="Customer Name (Optional)"
          placeholder="e.g. Mohanad Ahmed"
        />
        <x-admin.select
          name="mode"
          label="Message Mode"
          :options="[
            'text' => 'Standard Text (active 24h customer window)',
            'template' => 'Approved Meta Template (initiate new 24h window)',
          ]"
          value="text"
        />
        <x-admin.input
          name="template_language"
          label="Template Language Code (Template mode)"
          value="en_US"
          placeholder="en_US or ar"
        />
        <div class="full">
          <x-admin.textarea
            name="content"
            label="Message Text (or Template Name e.g. hello_world)"
            rows="4"
            placeholder="اكتب رسالتك هنا للعميل… (أو اكتب hello_world في وضع القالب)"
            hint="Use Standard Text if the customer messaged you within the last 24 hours; use Approved Meta Template (e.g. hello_world) to initiate contact first."
            :required="true"
          />
        </div>
      </div>

      <div class="form-actions">
        <button class="btn btn--primary" type="submit" @disabled(! $whatsappConfigured)>Send via WhatsApp Cloud API</button>
      </div>
    </form>
  </div>

  <div class="card">
    <h2 class="card__title">Recent WhatsApp Customers</h2>
    <p class="card__sub">Click any customer to fill their number automatically.</p>

    @if($recentContacts->isEmpty())
      <p style="font-size:0.84rem;color:var(--text-muted);margin:0;">No WhatsApp threads recorded yet.</p>
    @else
      <div style="display:flex;flex-direction:column;gap:8px;">
        @foreach($recentContacts as $contact)
          <button
            type="button"
            class="wa-contact-item"
            onclick="var f=document.getElementById('wa-dispatch-form'); f.querySelector('[name=phone]').value=@js($contact->visitor_phone); f.querySelector('[name=customer_name]').value=@js($contact->visitor_name ?? ''); f.querySelector('[name=content]').focus();"
          >
            <div>
              <div style="font-weight:700;font-size:0.88rem;">{{ $contact->visitorLabel() }}</div>
              <div style="font-size:0.76rem;color:var(--text-muted);">{{ $contact->visitor_phone }}</div>
            </div>
            <span class="pill" style="background:rgba(37,211,102,0.14);color:#25D366;border:1px solid rgba(37,211,102,0.3);">Select</span>
          </button>
        @endforeach
      </div>
    @endif
  </div>
</div>
@endsection
