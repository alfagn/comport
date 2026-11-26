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
        return (bool) $this->user()?->can('manage support chats');
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
        ];
    }
}
