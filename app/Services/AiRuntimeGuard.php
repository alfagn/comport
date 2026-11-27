<?php

namespace App\Services;

use App\Models\AiUsageLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Manages isolated AI provider keys (Customer Support vs Dashboard Copilot),
 * encrypted database key overrides, readiness checks, and dynamic multi-provider
 * emergency failover chains (Secondary Gemini -> DeepSeek -> OpenAI -> Claude -> Grok).
 */
final class AiRuntimeGuard
{
    private const ENCRYPTED_PREFIX = 'enc:v1:';

    public const DEFAULT_FAILOVER_CHAIN = 'gemini-fallback,gemini,deepseek,openai,anthropic,xai';

    /**
     * Isolated & emergency API key slots configurable via `.env` or System Settings.
     *
     * @var array<string, array{provider: string, env: string, label: string, role: string}>
     */
    public const KEY_SLOTS = [
        'ai_key_gemini_support' => [
            'provider' => 'gemini-support',
            'env' => 'GEMINI_SUPPORT_API_KEY',
            'label' => 'Gemini — Customer Support Key (Website & WhatsApp)',
            'role' => 'Primary Support',
        ],
        'ai_key_gemini_copilot' => [
            'provider' => 'gemini-copilot',
            'env' => 'GEMINI_COPILOT_API_KEY',
            'label' => 'Gemini — Admin Dashboard Copilot Key',
            'role' => 'Primary Copilot',
        ],
        'ai_key_gemini_fallback' => [
            'provider' => 'gemini-fallback',
            'env' => 'GEMINI_FALLBACK_API_KEY',
            'label' => 'Gemini — Secondary Backup Key (Project #2)',
            'role' => 'Backup #1',
        ],
        'ai_key_deepseek' => [
            'provider' => 'deepseek',
            'env' => 'DEEPSEEK_API_KEY',
            'label' => 'DeepSeek API Key (Emergency Failover)',
            'role' => 'Emergency #2',
        ],
        'ai_key_openai' => [
            'provider' => 'openai',
            'env' => 'OPENAI_API_KEY',
            'label' => 'OpenAI API Key (Emergency Failover)',
            'role' => 'Emergency #3',
        ],
        'ai_key_anthropic' => [
            'provider' => 'anthropic',
            'env' => 'ANTHROPIC_API_KEY',
            'label' => 'Anthropic Claude API Key (Emergency Failover)',
            'role' => 'Emergency #4',
        ],
        'ai_key_xai' => [
            'provider' => 'xai',
            'env' => 'XAI_API_KEY',
            'label' => 'xAI Grok API Key (Emergency Failover)',
            'role' => 'Emergency #5',
        ],
    ];

    /**
     * Hydrate runtime `config('ai.providers.*.key')` from any encrypted keys saved in System Settings.
     */
    public function syncRuntimeKeys(): void
    {
        try {
            $rows = Setting::query()
                ->where('key', 'like', 'ai_key_%')
                ->pluck('value', 'key');
        } catch (Throwable) {
            return;
        }

        foreach (self::KEY_SLOTS as $settingKey => $meta) {
            $stored = $rows[$settingKey] ?? null;
            if (! is_string($stored) || trim($stored) === '') {
                continue;
            }

            $plain = $this->decryptValue($stored);
            if ($plain === '') {
                continue;
            }

            $provider = $meta['provider'];
            config(["ai.providers.{$provider}.key" => $plain]);

            if ($settingKey === 'ai_key_gemini_support' && ! filled(config('ai.providers.gemini.key'))) {
                config(['ai.providers.gemini.key' => $plain]);
            }
        }
    }

    /**
     * Persist AI chatbot settings and any newly provided encrypted API keys.
     *
     * @param  array<string, mixed>  $validated
     */
    public function saveChatbotAndKeySettings(array $validated): void
    {
        Setting::setValue('ai_support_enabled', ! empty($validated['ai_support_enabled']) ? '1' : '0', 'ai');
        Setting::setValue('ai_support_greeting', (string) ($validated['ai_support_greeting'] ?? ''), 'ai');
        Setting::setValue('ai_support_knowledge', (string) ($validated['ai_support_knowledge'] ?? ''), 'ai');
        Setting::setValue('ai_support_provider', $validated['ai_support_provider'] ?? null, 'ai');
        Setting::setValue('ai_support_model', $validated['ai_support_model'] ?? null, 'ai');
        Setting::setValue('ai_dashboard_provider', $validated['ai_dashboard_provider'] ?? null, 'ai');
        Setting::setValue('ai_dashboard_model', $validated['ai_dashboard_model'] ?? null, 'ai');

        if (array_key_exists('ai_failover_enabled', $validated)) {
            Setting::setValue('ai_failover_enabled', ! empty($validated['ai_failover_enabled']) ? '1' : '0', 'ai');
        }

        if (array_key_exists('ai_failover_chain', $validated)) {
            $cleanChain = $this->sanitizeFailoverChain((string) ($validated['ai_failover_chain'] ?? ''));
            Setting::setValue('ai_failover_chain', $cleanChain !== '' ? $cleanChain : self::DEFAULT_FAILOVER_CHAIN, 'ai');
        }

        foreach (array_keys(self::KEY_SLOTS) as $settingKey) {
            $raw = isset($validated[$settingKey]) ? trim((string) $validated[$settingKey]) : '';
            if ($raw !== '') {
                Setting::setValue($settingKey, self::ENCRYPTED_PREFIX.Crypt::encryptString($raw), 'ai');
            }
        }

        $this->syncRuntimeKeys();
    }

    /**
     * Build the ordered provider -> model failover map for a specific role (`support` or `dashboard`).
     *
     * Maps `gemini` to its dedicated isolated provider (`gemini-support` or `gemini-copilot`),
     * then appends only emergency providers whose API keys are currently configured.
     *
     * @return array<string, string|null>|string
     */
    public function providerChainFor(
        string $role,
        ?string $configuredProvider = null,
        ?string $configuredModel = null,
    ): array|string {
        $this->syncRuntimeKeys();

        $base = $this->resolvedProvider($configuredProvider ?: (string) config("assistant.{$role}.provider", ''));

        $primary = match (true) {
            $base === 'gemini' && $role === 'support' => 'gemini-support',
            $base === 'gemini' && $role === 'dashboard' => 'gemini-copilot',
            default => $base,
        };

        $primaryModel = filled($configuredModel) ? trim((string) $configuredModel) : null;

        $candidates = [
            $primary => $primaryModel,
        ];

        if ($this->isFailoverEnabled()) {
            $quarantined = $this->quarantinedProviders();

            foreach ($this->configuredFailoverOrder() as $fallbackProvider) {
                if ($fallbackProvider === $primary || array_key_exists($fallbackProvider, $candidates)) {
                    continue;
                }

                if (! $this->isReady($fallbackProvider)) {
                    continue;
                }

                // Skip emergency providers currently quarantined due to 401 Invalid Key or 402 Zero Balance
                if (isset($quarantined[$fallbackProvider])) {
                    continue;
                }

                // Avoid adding shared `gemini` fallback if its API key is identical to an already-queued Gemini slot
                if ($fallbackProvider === 'gemini') {
                    $sharedKey = (string) config('ai.providers.gemini.key', '');
                    $primaryKey = (string) config("ai.providers.{$primary}.key", '');
                    $backupKey = (string) config('ai.providers.gemini-fallback.key', '');
                    if ($sharedKey === '' || $sharedKey === $primaryKey || (isset($candidates['gemini-fallback']) && $sharedKey === $backupKey)) {
                        continue;
                    }
                }

                $candidates[$fallbackProvider] = in_array($fallbackProvider, ['gemini-fallback', 'gemini'], true)
                    ? (string) config("ai.providers.{$fallbackProvider}.models.text.default", 'gemini-3.1-flash-lite')
                    : null;
            }
        }

        $readyCandidates = [];
        foreach ($candidates as $providerName => $modelName) {
            if ($this->isReady($providerName)) {
                $readyCandidates[$providerName] = $modelName;
            }
        }

        if ($readyCandidates !== []) {
            if (count($readyCandidates) === 1 && reset($readyCandidates) === null) {
                return (string) array_key_first($readyCandidates);
            }

            return $readyCandidates;
        }

        return $primaryModel !== null ? [$primary => $primaryModel] : $primary;
    }

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

        $this->syncRuntimeKeys();

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
     * Return diagnostic status for every isolated & emergency key slot for System Settings UI.
     *
     * @return list<array{setting_key: string, provider: string, env: string, label: string, role: string, ready: bool, source: string, masked: ?string}>
     */
    public function keyStatusMatrix(): array
    {
        $this->syncRuntimeKeys();

        $vaultRows = collect();
        try {
            $vaultRows = Setting::query()
                ->where('key', 'like', 'ai_key_%')
                ->pluck('value', 'key');
        } catch (Throwable) {
            // Ignore when database is not yet initialized.
        }

        $matrix = [];

        foreach (self::KEY_SLOTS as $settingKey => $meta) {
            $provider = $meta['provider'];
            $hasVaultKey = is_string($vaultRows[$settingKey] ?? null) && trim((string) $vaultRows[$settingKey]) !== '';
            $activeKey = (string) (config("ai.providers.{$provider}.key") ?? '');
            $ready = $activeKey !== '';

            $dedicatedEnvVal = env($meta['env']);
            $hasDedicatedEnv = is_string($dedicatedEnvVal) && trim($dedicatedEnvVal) !== '';

            $source = match (true) {
                $hasVaultKey => 'vault',
                $hasDedicatedEnv => 'env',
                $ready && str_starts_with($provider, 'gemini-') => 'shared_gemini_env',
                $ready => 'env',
                default => 'none',
            };

            $matrix[] = [
                'setting_key' => $settingKey,
                'provider' => $provider,
                'env' => $meta['env'],
                'label' => $meta['label'],
                'role' => $meta['role'],
                'ready' => $ready,
                'source' => $source,
                'masked' => $ready ? $this->maskSecret($activeKey) : null,
            ];
        }

        return $matrix;
    }

    /**
     * Check whether automatic multi-provider failover is enabled.
     */
    public function isFailoverEnabled(): bool
    {
        try {
            $settingToggle = Setting::getValue('ai_failover_enabled', '1');
        } catch (Throwable) {
            $settingToggle = '1';
        }

        return (bool) config('assistant.failover.enabled', true) && $settingToggle !== '0';
    }

    /**
     * Ordered list of emergency failover provider names.
     *
     * @return list<string>
     */
    public function configuredFailoverOrder(): array
    {
        $raw = null;
        try {
            $raw = Setting::getValue('ai_failover_chain');
        } catch (Throwable) {
            $raw = null;
        }

        $chain = filled($raw)
            ? (string) $raw
            : (string) config('assistant.failover.chain', self::DEFAULT_FAILOVER_CHAIN);

        $sanitized = $this->sanitizeFailoverChain($chain);

        return $sanitized !== '' ? explode(',', $sanitized) : [];
    }

    /**
     * Human-readable setup hint for administrators when credentials are missing.
     */
    public function missingCredentialsHint(?string $override = null): string
    {
        $name = $this->resolvedProvider($override);

        $envVar = match ($name) {
            'anthropic' => 'ANTHROPIC_API_KEY',
            'gemini-support' => 'GEMINI_SUPPORT_API_KEY (or GEMINI_API_KEY)',
            'gemini-copilot' => 'GEMINI_COPILOT_API_KEY (or GEMINI_API_KEY)',
            'gemini-fallback' => 'GEMINI_FALLBACK_API_KEY',
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

        return "AI provider [{$name}] is not configured yet. Add {$envVar} to your .env file or paste the key in System Settings → Chatbot & AI.";
    }

    /**
     * Identify providers whose most recent telemetry event in the last 60 minutes
     * was a hard `401 Invalid API Key` or `402 Insufficient Credits`, so live traffic
     * skips broken emergency keys until credits/keys are fixed or re-probed (`200 OK`).
     *
     * @return array<string, string>
     */
    public function quarantinedProviders(): array
    {
        try {
            if (! Schema::hasTable('ai_usage_logs')) {
                return [];
            }

            $recent = AiUsageLog::query()
                ->where('created_at', '>=', now()->subMinutes(60))
                ->orderByDesc('id')
                ->limit(100)
                ->get(['provider', 'status', 'error_category']);

            $seen = [];
            $quarantined = [];

            foreach ($recent as $row) {
                $prov = (string) $row->provider;
                if (isset($seen[$prov])) {
                    continue;
                }
                $seen[$prov] = true;

                if (
                    in_array($row->status, [AiUsageLog::STATUS_ERROR, AiUsageLog::STATUS_FAILOVER], true)
                    && in_array($row->error_category, ['invalid_key_401', 'quota_credits_402'], true)
                ) {
                    $quarantined[$prov] = (string) $row->error_category;
                }
            }

            return $quarantined;
        } catch (Throwable) {
            return [];
        }
    }

    private function sanitizeFailoverChain(string $chain): string
    {
        $allowed = [
            'gemini-fallback', 'gemini-support', 'gemini-copilot', 'gemini',
            'deepseek', 'openai', 'anthropic', 'xai', 'groq', 'mistral', 'openrouter',
        ];

        $parts = array_values(array_unique(array_filter(
            array_map(fn (string $item): string => strtolower(trim($item)), explode(',', $chain)),
            fn (string $item): bool => in_array($item, $allowed, true),
        )));

        return implode(',', $parts);
    }

    private function decryptValue(string $stored): string
    {
        if (! str_starts_with($stored, self::ENCRYPTED_PREFIX)) {
            return trim($stored);
        }

        try {
            return trim(Crypt::decryptString(substr($stored, strlen(self::ENCRYPTED_PREFIX))));
        } catch (Throwable) {
            return '';
        }
    }

    private function maskSecret(string $secret): string
    {
        $len = strlen($secret);
        if ($len <= 8) {
            return str_repeat('•', max(4, $len));
        }

        return substr($secret, 0, 5).'••••••••'.substr($secret, -4);
    }
}
