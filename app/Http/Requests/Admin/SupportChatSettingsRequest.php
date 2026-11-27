<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupportChatSettingsRequest extends FormRequest
{
    private const PROVIDERS = [
        'openai', 'anthropic', 'gemini', 'groq', 'deepseek',
        'mistral', 'openrouter', 'xai', 'ollama', 'azure', 'openai-compatible',
    ];

    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage support chats')
            || (bool) $this->user()?->can('manage settings');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'ai_support_enabled' => ['nullable', 'boolean'],
            'ai_support_greeting' => ['required', 'string', 'max:500'],
            'ai_support_knowledge' => ['nullable', 'string', 'max:8000'],
            'ai_support_provider' => ['nullable', 'string', Rule::in(self::PROVIDERS)],
            'ai_support_model' => ['nullable', 'string', 'max:120'],
            'ai_dashboard_provider' => ['nullable', 'string', Rule::in(self::PROVIDERS)],
            'ai_dashboard_model' => ['nullable', 'string', 'max:120'],
            'ai_failover_enabled' => ['nullable', 'boolean'],
            'ai_failover_chain' => ['nullable', 'string', 'max:250'],
            'ai_key_gemini_support' => ['nullable', 'string', 'max:500'],
            'ai_key_gemini_copilot' => ['nullable', 'string', 'max:500'],
            'ai_key_gemini_fallback' => ['nullable', 'string', 'max:500'],
            'ai_key_groq' => ['nullable', 'string', 'max:500'],
            'ai_key_deepseek' => ['nullable', 'string', 'max:500'],
            'ai_key_openai' => ['nullable', 'string', 'max:500'],
            'ai_key_anthropic' => ['nullable', 'string', 'max:500'],
            'ai_key_xai' => ['nullable', 'string', 'max:500'],
        ];
    }
}
