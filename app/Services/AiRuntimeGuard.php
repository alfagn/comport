<?php

namespace App\Services;

/**
 * Checks whether the configured Laravel AI provider has credentials ready for
 * live requests, while allowing faked agents in automated tests to run freely.
 */
final class AiRuntimeGuard
{
    /**
     * Resolve the active provider key.
     */
    public function resolvedProvider(?string $override = null): string
    {
        $provider = $override ?: (string) config('ai.default', 'openai');

        return trim($provider) !== '' ? trim($provider) : 'openai';
    }

    /**
     * Determine whether the provider is ready to accept prompts.
     */
    public function isReady(?string $override = null, bool $isFaked = false): bool
    {
        if ($isFaked) {
            return true;
        }

        $name = $this->resolvedProvider($override);
        $providerConfig = config("ai.providers.{$name}");

        if (! is_array($providerConfig)) {
            return false;
        }

        $driver = (string) ($providerConfig['driver'] ?? $name);

        if ($driver === 'ollama') {
            return filled($providerConfig['url'] ?? null);
        }

        if ($driver === 'openai-compatible') {
            return filled($providerConfig['url'] ?? null);
        }

        if ($driver === 'bedrock') {
            return filled($providerConfig['key'] ?? null) || filled($providerConfig['access_key_id'] ?? null);
        }

        return filled($providerConfig['key'] ?? null);
    }

    /**
     * Human-readable setup hint for administrators when credentials are missing.
     */
    public function missingCredentialsHint(?string $override = null): string
    {
        $name = $this->resolvedProvider($override);

        $envVar = match ($name) {
            'anthropic' => 'ANTHROPIC_API_KEY',
            'gemini' => 'GEMINI_API_KEY',
            'groq' => 'GROQ_API_KEY',
            'deepseek' => 'DEEPSEEK_API_KEY',
            'mistral' => 'MISTRAL_API_KEY',
            'openrouter' => 'OPENROUTER_API_KEY',
            'xai' => 'XAI_API_KEY',
            'cohere' => 'COHERE_API_KEY',
            'azure' => 'AZURE_OPENAI_API_KEY',
            'openai-compatible' => 'OPENAI_COMPATIBLE_URL',
            default => 'OPENAI_API_KEY',
        };

        return "AI provider [{$name}] is not configured yet. Add {$envVar} to your .env file (or switch AI_PROVIDER in .env / AI Settings) to enable live responses.";
    }
}
