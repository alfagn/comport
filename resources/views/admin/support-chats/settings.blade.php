@extends('layouts.admin')

@section('title', 'Chatbot & AI Settings')
@section('crumb', 'Support Chats')

@section('content')
@if(! $providerReady)
  <div class="ai-alert" style="margin:0 0 20px;" role="status">
    <strong>AI Provider Not Configured Yet:</strong> {{ $providerHint }}
  </div>
@endif

<form method="post" action="{{ route('admin.support-chats.settings.update') }}">
  @csrf @method('PUT')

  <div class="card card--form" style="margin-bottom:22px;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:6px;">
      <h2 class="card__title" style="margin:0;">Website Live Concierge &amp; Shared Knowledge Base</h2>
      <span class="pill">{{ setting('ai_support_enabled', '1') !== '0' ? 'Web Widget Live' : 'Web Widget Hidden' }}</span>
    </div>
    <p class="card__sub">Controls the floating website chat widget and the custom FAQ instructions shared with your WhatsApp AI agent.</p>

    <div class="form-grid">
      <div class="full">
        <x-admin.toggle
          name="ai_support_enabled"
          label="Display floating AI Customer Support Chatbot widget on public website pages"
          :checked="setting('ai_support_enabled', '1') !== '0'"
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

    @php
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
    @endphp

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
@endsection
