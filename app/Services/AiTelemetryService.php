<?php

namespace App\Services;

use App\Ai\Agents\CustomerSupportAgent;
use App\Ai\Agents\DashboardAssistantAgent;
use App\Models\AiUsageLog;
use App\Models\Setting;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Events\AgentFailed;
use Laravel\Ai\Events\AgentFailedOver;
use Laravel\Ai\Events\AgentPrompted;
use Laravel\Ai\Events\PromptingAgent;
use Laravel\Ai\Exceptions\InsufficientCreditsException;
use Laravel\Ai\Exceptions\InvalidApiKeyException;
use Laravel\Ai\Exceptions\ProviderConnectionException;
use Laravel\Ai\Exceptions\ProviderOverloadedException;
use Laravel\Ai\Exceptions\RateLimitedException;
use Throwable;

use function Laravel\Ai\agent;

/**
 * Real-time AI Quota, Token Usage & Model Error Diagnostics engine.
 *
 * Captures every live AI prompt, failover, rate-limit header, and upstream error
 * across Gemini (Support / Copilot / Backup), DeepSeek, OpenAI, Claude & Grok.
 */
final class AiTelemetryService
{
    /**
     * Soft daily request & RPM reference budgets per provider driver for visual quota gauges.
     *
     * @var array<string, array{rpm: int, rpd: int, tpm: int, tier_label: string}>
     */
    private const DEFAULT_BUDGETS = [
        'gemini' => ['rpm' => 15, 'rpd' => 1500, 'tpm' => 250000, 'tier_label' => 'Google AI Studio Quota'],
        'deepseek' => ['rpm' => 60, 'rpd' => 10000, 'tpm' => 500000, 'tier_label' => 'DeepSeek Pay-As-You-Go'],
        'openai' => ['rpm' => 60, 'rpd' => 10000, 'tpm' => 200000, 'tier_label' => 'OpenAI Tier 1+'],
        'anthropic' => ['rpm' => 50, 'rpd' => 5000, 'tpm' => 100000, 'tier_label' => 'Anthropic Tier 1+'],
        'xai' => ['rpm' => 60, 'rpd' => 10000, 'tpm' => 200000, 'tier_label' => 'xAI Grok API'],
    ];

    /**
     * Human-readable problem catalog mapped by `error_category`.
     *
     * @var array<string, array{badge: string, http: int, severity: string, title_ar: string, fix_ar: string}>
     */
    public const ERROR_CATALOG = [
        'overloaded_503' => [
            'badge' => '503 Overloaded',
            'http' => 503,
            'severity' => 'warn',
            'title_ar' => '503 ServiceUnavailable — ضغط عالٍ مؤقت على سيرفرات الموديل',
            'fix_ar' => 'مشكلة من سيرفر المزود نفسه وليست من الكود؛ سلسلة الطوارئ تحوّل الطلب تلقائياً للمفتاح الاحتياطي التالي.',
        ],
        'rate_limit_429' => [
            'badge' => '429 Rate / Quota Limit',
            'http' => 429,
            'severity' => 'warn',
            'title_ar' => '429 Too Many Requests — استهلاك حد الطلبات في الدقيقة (RPM) أو اليوم (RPD)',
            'fix_ar' => 'افصل مفاتيح Gemini لكل خدمة (Support / Copilot / Backup) أو ارفع حساب المزود إلى Paid Tier 1.',
        ],
        'quota_credits_402' => [
            'badge' => '402 Insufficient Credits',
            'http' => 402,
            'severity' => 'danger',
            'title_ar' => '402 Insufficient Balance — نفاد رصيد المحفظة أو سقف الفوترة',
            'fix_ar' => 'اشحن رصيد الحساب (Top-up Credits) في لوحة تحكم المزود ليعود للعمل فوراً.',
        ],
        'invalid_key_401' => [
            'badge' => '401 Invalid API Key',
            'http' => 401,
            'severity' => 'danger',
            'title_ar' => '401 / 403 Invalid Key — مفتاح API غير صالح أو موقوف أو غير مصرّح له بالموديل',
            'fix_ar' => 'استبدل المفتاح من صفحة System Settings → Chatbot & AI ثم اضغط فحص مباشر (Probe).',
        ],
        'timeout_network' => [
            'badge' => '504 Network / Timeout',
            'http' => 504,
            'severity' => 'warn',
            'title_ar' => '504 Timeout — تأخر استجابة الموديل أو انقطاع الاتصال المؤقت',
            'fix_ar' => 'استخدم موديلات سريعة (مثل flash-lite) لخدمة العملاء وتأكد من تفعيل مفاتيح الطوارئ.',
        ],
        'provider_error' => [
            'badge' => '500 Upstream Error',
            'http' => 500,
            'severity' => 'danger',
            'title_ar' => 'خطأ من واجهة المزود الخارجية أو اسم الموديل غير مدعوم',
            'fix_ar' => 'راجع نص الخطأ التفصيلي بالأسفل وتأكد من مطابقة اسم الموديل (Model Override) للمزود المختار.',
        ],
    ];

    /**
     * In-memory high-resolution start timestamps keyed by `invocationId:provider`.
     *
     * @var array<string, int>
     */
    private array $startTimes = [];

    /**
     * Track invocations already recorded via `AgentFailedOver` so `AgentFailed` doesn't duplicate them.
     *
     * @var array<string, bool>
     */
    private array $recordedFailures = [];

    private ?string $channelOverride = null;

    public function __construct(private AiRuntimeGuard $guard) {}

    /**
     * Record high-resolution start time when a provider attempt begins.
     */
    public function recordPrompting(PromptingAgent $event): void
    {
        $key = $event->invocationId.':'.$event->prompt->provider()->name();
        $this->startTimes[$key] = hrtime(true);
    }

    /**
     * Record a completed prompt turn with token usage, latency, and upstream rate-limit headers.
     */
    public function recordSuccess(AgentPrompted $event): void
    {
        $agent = $event->prompt->agent;
        if (method_exists($agent, 'isFaked') && $agent::isFaked() && ! config('assistant.telemetry.record_fakes', false)) {
            return;
        }

        if (! $this->tableReady()) {
            return;
        }

        $providerName = $event->prompt->provider()->name();
        $driver = $event->prompt->provider()->driver();
        $model = $event->response->meta->model ?: $event->prompt->model;
        $latencyMs = $this->consumeLatencyMs($event->invocationId, $providerName);

        $usage = $event->response->usage;
        $inputTokens = (int) ($usage->inputTokens ?? 0);
        $outputTokens = (int) ($usage->outputTokens ?? 0);
        $reasoningTokens = (int) ($usage->reasoningTokens ?? 0);
        $cachedTokens = (int) (($usage->cacheReadInputTokens ?? 0) + ($usage->cacheWriteInputTokens ?? 0));

        $rawResponse = property_exists($event->response, 'raw') ? $event->response->raw : null;
        $rateLimits = $rawResponse instanceof Response ? $this->extractRateLimitHeaders($rawResponse) : null;

        try {
            AiUsageLog::query()->create([
                'invocation_id' => $event->invocationId,
                'channel' => $this->resolveChannel($agent),
                'agent' => class_basename($agent),
                'provider' => $providerName,
                'driver' => $driver,
                'model' => $model ?: 'default',
                'status' => AiUsageLog::STATUS_SUCCESS,
                'http_code' => 200,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'reasoning_tokens' => $reasoningTokens,
                'cached_tokens' => $cachedTokens,
                'total_tokens' => $inputTokens + $outputTokens,
                'latency_ms' => $latencyMs,
                'rate_limits' => $rateLimits !== [] ? $rateLimits : null,
            ]);
        } catch (Throwable) {
            // Never disrupt live customer/admin traffic if telemetry storage fails.
        }
    }

    /**
     * Record an upstream provider failure that automatically failed over to the next emergency provider.
     */
    public function recordFailover(AgentFailedOver $event): void
    {
        if (! $this->tableReady()) {
            return;
        }

        $providerName = $event->provider->name();
        $dedupeKey = $event->invocationId.':'.$providerName.':'.$event->model;
        $this->recordedFailures[$dedupeKey] = true;

        $latencyMs = $this->consumeLatencyMs($event->invocationId, $providerName);
        $classified = $this->classifyException($event->exception);

        try {
            AiUsageLog::query()->create([
                'invocation_id' => $event->invocationId,
                'channel' => $this->resolveChannel($event->agent),
                'agent' => class_basename($event->agent),
                'provider' => $providerName,
                'driver' => $event->provider->driver(),
                'model' => $event->model ?: 'default',
                'status' => AiUsageLog::STATUS_FAILOVER,
                'http_code' => $classified['http_code'],
                'error_category' => $classified['category'],
                'error_message' => Str::limit($classified['clean_message'], 900),
                'latency_ms' => $latencyMs,
                'rate_limits' => $classified['rate_limits'],
            ]);
        } catch (Throwable) {
            // Safe no-op.
        }
    }

    /**
     * Record a terminal AI failure (either non-failoverable 401/403 or final failover hop exhausted).
     */
    public function recordFailure(AgentFailed $event): void
    {
        $agent = $event->prompt->agent;
        if (method_exists($agent, 'isFaked') && $agent::isFaked() && ! config('assistant.telemetry.record_fakes', false)) {
            return;
        }

        if (! $this->tableReady()) {
            return;
        }

        // If this hop will immediately trigger AgentFailedOver, let recordFailover log it as `failover` instead.
        if ($event->prompt->willRetry($event->exception)) {
            return;
        }

        $providerName = $event->prompt->provider()->name();
        $model = $event->prompt->model ?: 'default';
        $dedupeKey = $event->invocationId.':'.$providerName.':'.$model;

        if (! empty($this->recordedFailures[$dedupeKey])) {
            return;
        }
        $this->recordedFailures[$dedupeKey] = true;

        $latencyMs = $this->consumeLatencyMs($event->invocationId, $providerName);
        $classified = $this->classifyException($event->exception);

        try {
            AiUsageLog::query()->create([
                'invocation_id' => $event->invocationId,
                'channel' => $this->resolveChannel($agent),
                'agent' => class_basename($agent),
                'provider' => $providerName,
                'driver' => $event->prompt->provider()->driver(),
                'model' => $model,
                'status' => AiUsageLog::STATUS_ERROR,
                'http_code' => $classified['http_code'],
                'error_category' => $classified['category'],
                'error_message' => Str::limit($classified['clean_message'], 900),
                'latency_ms' => $latencyMs,
                'rate_limits' => $classified['rate_limits'],
            ]);
        } catch (Throwable) {
            // Safe no-op.
        }
    }

    /**
     * Run a live lightweight diagnostic probe against one configured provider slot (or all armed slots).
     *
     * @return list<array{provider: string, model: string, ok: bool, status_code: int, latency_ms: int, message: string}>
     */
    public function runLiveProbe(?string $targetProvider = null): array
    {
        $this->guard->syncRuntimeKeys();

        $slotsToProbe = [];
        if ($targetProvider !== null && $targetProvider !== '' && $targetProvider !== 'all') {
            $slotsToProbe[] = $targetProvider;
        } else {
            foreach ($this->guard->keyStatusMatrix() as $slot) {
                if ($slot['ready']) {
                    $slotsToProbe[] = $slot['provider'];
                }
            }
        }

        $results = [];
        $this->channelOverride = 'probe';

        try {
            foreach (array_unique($slotsToProbe) as $providerSlot) {
                if (! $this->guard->isReady($providerSlot)) {
                    $results[] = [
                        'provider' => $providerSlot,
                        'model' => 'unconfigured',
                        'ok' => false,
                        'status_code' => 0,
                        'latency_ms' => 0,
                        'message' => 'API key is not configured yet.',
                    ];

                    continue;
                }

                $startedAt = hrtime(true);
                try {
                    $response = agent(instructions: 'System health probe. Reply strictly with the single word OK.')
                        ->prompt('Ping', provider: $providerSlot, timeout: 15);

                    $elapsedMs = (int) round((hrtime(true) - $startedAt) / 1_000_000);
                    $resolvedModel = $response->meta->model ?: 'default';

                    $results[] = [
                        'provider' => $providerSlot,
                        'model' => $resolvedModel,
                        'ok' => true,
                        'status_code' => 200,
                        'latency_ms' => $elapsedMs,
                        'message' => "200 OK ({$elapsedMs} ms) — Model [{$resolvedModel}] responded healthy.",
                    ];
                } catch (Throwable $e) {
                    $elapsedMs = (int) round((hrtime(true) - $startedAt) / 1_000_000);
                    $classified = $this->classifyException($e);

                    $results[] = [
                        'provider' => $providerSlot,
                        'model' => (string) (config("ai.providers.{$providerSlot}.models.text.default") ?: 'default'),
                        'ok' => false,
                        'status_code' => $classified['http_code'],
                        'latency_ms' => $elapsedMs,
                        'message' => "[{$classified['badge']}] {$classified['clean_message']}",
                    ];
                }
            }
        } finally {
            $this->channelOverride = null;
        }

        return $results;
    }

    /**
     * Build the complete dashboard analytics & diagnostics payload for `/admin/ai-monitor`.
     *
     * @return array<string, mixed>
     */
    public function dashboardReport(int $days = 28): array
    {
        $days = in_array($days, [1, 7, 28], true) ? $days : 28;
        $this->backfillHistoricalConversationsOnce();
        $this->guard->syncRuntimeKeys();

        $since = $days === 1 ? now()->subHours(24) : now()->subDays($days)->startOfDay();
        $oneMinuteAgo = now()->subMinute();
        $todayStart = now()->startOfDay();

        $rows = $this->tableReady()
            ? AiUsageLog::query()->where('created_at', '>=', $since)->orderByDesc('id')->get()
            : collect();

        $totalRequests = $rows->count();
        $successCount = $rows->where('status', AiUsageLog::STATUS_SUCCESS)->count();
        $failoverCount = $rows->where('status', AiUsageLog::STATUS_FAILOVER)->count();
        $errorCount = $rows->where('status', AiUsageLog::STATUS_ERROR)->count();
        $totalFailures = $failoverCount + $errorCount;

        $inputTokens = (int) $rows->sum('input_tokens');
        $outputTokens = (int) $rows->sum('output_tokens');
        $cachedTokens = (int) $rows->sum('cached_tokens');
        $reasoningTokens = (int) $rows->sum('reasoning_tokens');
        $avgLatency = (int) round((float) ($rows->whereNotNull('latency_ms')->avg('latency_ms') ?? 0));

        $successRate = $totalRequests > 0
            ? round(($successCount / $totalRequests) * 100, 1)
            : 100.0;

        // Live 60-second & Today counters for RPM / TPM / RPD Quota bars
        $recentMinuteRows = $this->tableReady()
            ? AiUsageLog::query()->where('created_at', '>=', $oneMinuteAgo)->get(['provider', 'total_tokens'])
            : collect();
        $todayRows = $this->tableReady()
            ? AiUsageLog::query()->where('created_at', '>=', $todayStart)->get(['provider', 'status', 'total_tokens'])
            : collect();

        // 1. Key Slot Quota Cards (all 7 isolated & emergency slots)
        $slotQuotas = [];
        foreach ($this->guard->keyStatusMatrix() as $slot) {
            $prov = $slot['provider'];
            $driver = (string) config("ai.providers.{$prov}.driver", 'openai');
            $budget = self::DEFAULT_BUDGETS[$driver] ?? self::DEFAULT_BUDGETS['openai'];

            $provWindowRows = $rows->where('provider', $prov);
            $provTodayRows = $todayRows->where('provider', $prov);
            $provMinRows = $recentMinuteRows->where('provider', $prov);

            $rpmUsed = $provMinRows->count();
            $tpmUsed = (int) $provMinRows->sum('total_tokens');
            $rpdUsed = $provTodayRows->count();
            $tokensToday = (int) $provTodayRows->sum('total_tokens');

            // Prefer live upstream rate-limit header if captured on latest response
            $latestWithHeaders = $provWindowRows->first(fn (AiUsageLog $r): bool => is_array($r->rate_limits) && $r->rate_limits !== []);
            $liveHeaders = $latestWithHeaders?->rate_limits ?? null;

            $rpdLimit = isset($liveHeaders['limit_requests']) && (int) $liveHeaders['limit_requests'] > 0
                ? (int) $liveHeaders['limit_requests']
                : $budget['rpd'];
            $rpdRemaining = isset($liveHeaders['remaining_requests'])
                ? (int) $liveHeaders['remaining_requests']
                : max(0, $rpdLimit - $rpdUsed);

            $provFailures = $provWindowRows->whereIn('status', [AiUsageLog::STATUS_FAILOVER, AiUsageLog::STATUS_ERROR]);
            $latestIssue = $provFailures->first();
            $latestSuccess = $provWindowRows->firstWhere('status', AiUsageLog::STATUS_SUCCESS);

            $healthState = 'unconfigured';
            if ($slot['ready']) {
                if ($latestIssue && (! $latestSuccess || $latestIssue->id > $latestSuccess->id)) {
                    $healthState = in_array($latestIssue->error_category, ['invalid_key_401', 'quota_credits_402'], true)
                        ? 'critical'
                        : 'degraded';
                } elseif ($provFailures->isNotEmpty()) {
                    $healthState = 'recovered';
                } else {
                    $healthState = 'healthy';
                }
            }

            $slotQuotas[] = [
                ...$slot,
                'driver' => $driver,
                'tier_label' => $budget['tier_label'],
                'default_model' => (string) (config("ai.providers.{$prov}.models.text.default") ?: $this->defaultModelLabel($driver)),
                'rpm_used' => $rpmUsed,
                'rpm_limit' => $budget['rpm'],
                'rpm_pct' => min(100, (int) round(($rpmUsed / max(1, $budget['rpm'])) * 100)),
                'tpm_used' => $tpmUsed,
                'rpd_used' => $rpdUsed,
                'rpd_limit' => $rpdLimit,
                'rpd_remaining' => $rpdRemaining,
                'rpd_pct' => min(100, (int) round(($rpdUsed / max(1, $rpdLimit)) * 100)),
                'tokens_today' => $tokensToday,
                'window_requests' => $provWindowRows->count(),
                'window_errors' => $provFailures->count(),
                'avg_latency_ms' => (int) round((float) ($provWindowRows->whereNotNull('latency_ms')->avg('latency_ms') ?? 0)),
                'health' => $healthState,
                'latest_issue' => $latestIssue ? $this->formatIssueSummary($latestIssue) : null,
            ];
        }

        // 2. Per-Model Health & Problems Breakdown
        $modelDiagnostics = $this->buildModelDiagnostics($rows, $slotQuotas);

        // 3. Error Breakdown by Category (503 vs 429 vs 402 vs 401 vs Timeout)
        $errorBreakdown = [];
        foreach (self::ERROR_CATALOG as $catKey => $meta) {
            $count = $rows->where('error_category', $catKey)->count();
            if ($count > 0) {
                $errorBreakdown[] = [
                    'category' => $catKey,
                    'badge' => $meta['badge'],
                    'http' => $meta['http'],
                    'severity' => $meta['severity'],
                    'title_ar' => $meta['title_ar'],
                    'fix_ar' => $meta['fix_ar'],
                    'count' => $count,
                ];
            }
        }

        // 4. Timeline Series (Google AI Studio style: Requests + Success Rate % + Errors per bucket)
        $timeline = $this->buildTimelineSeries($rows, $days);

        // 5. Recent Incidents & Failovers Feed
        $recentIncidents = $rows
            ->whereIn('status', [AiUsageLog::STATUS_FAILOVER, AiUsageLog::STATUS_ERROR])
            ->take(25)
            ->map(fn (AiUsageLog $log): array => [
                'id' => $log->id,
                'time' => $log->created_at?->format('M d, H:i:s') ?? '',
                'ago' => $log->created_at?->diffForHumans() ?? '',
                'channel' => $log->channel,
                'provider' => $log->provider,
                'model' => $log->model,
                'status' => $log->status,
                'http_code' => $log->http_code,
                'badge' => self::ERROR_CATALOG[$log->error_category ?? '']['badge'] ?? ('HTTP '.($log->http_code ?: 500)),
                'title_ar' => self::ERROR_CATALOG[$log->error_category ?? '']['title_ar'] ?? 'خطأ غير مصنف من المزود',
                'fix_ar' => self::ERROR_CATALOG[$log->error_category ?? '']['fix_ar'] ?? '',
                'message' => $log->error_message,
                'latency_ms' => $log->latency_ms,
            ])
            ->values()
            ->all();

        return [
            'days' => $days,
            'kpis' => [
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'total_tokens' => $inputTokens + $outputTokens,
                'cached_tokens' => $cachedTokens,
                'reasoning_tokens' => $reasoningTokens,
                'total_requests' => $totalRequests,
                'success_count' => $successCount,
                'failover_count' => $failoverCount,
                'error_count' => $errorCount,
                'total_failures' => $totalFailures,
                'success_rate' => $successRate,
                'avg_latency_ms' => $avgLatency,
            ],
            'slot_quotas' => $slotQuotas,
            'models' => $modelDiagnostics,
            'error_breakdown' => $errorBreakdown,
            'timeline' => $timeline,
            'recent_incidents' => $recentIncidents,
        ];
    }

    /**
     * Count unresolved errors in the last 24h for the admin sidebar badge.
     */
    public function recentIssueCount24h(): int
    {
        if (! $this->tableReady()) {
            return 0;
        }

        try {
            return AiUsageLog::query()
                ->where('created_at', '>=', now()->subHours(24))
                ->whereIn('status', [AiUsageLog::STATUS_FAILOVER, AiUsageLog::STATUS_ERROR])
                ->count();
        } catch (Throwable) {
            return 0;
        }
    }

    /**
     * Classify any upstream exception into HTTP status, structured category, and clean message.
     *
     * @return array{http_code: int, category: string, badge: string, clean_message: string, rate_limits: ?array<string, string>}
     */
    public function classifyException(Throwable $exception): array
    {
        $rawMessage = trim($exception->getMessage());
        $lower = strtolower($rawMessage);
        $rateLimits = null;
        $httpCode = (int) $exception->getCode();

        if ($exception instanceof RequestException && $exception->response instanceof Response) {
            $httpCode = $exception->response->status();
            $rateLimits = $this->extractRateLimitHeaders($exception->response);
        } elseif ($exception->getPrevious() instanceof RequestException && $exception->getPrevious()->response instanceof Response) {
            $httpCode = $exception->getPrevious()->response->status();
            $rateLimits = $this->extractRateLimitHeaders($exception->getPrevious()->response);
        }

        $category = match (true) {
            $exception instanceof ProviderOverloadedException,
            in_array($httpCode, [503, 529], true),
            str_contains($lower, 'overloaded'),
            str_contains($lower, 'serviceunavailable'),
            str_contains($lower, '503') => 'overloaded_503',

            $exception instanceof InsufficientCreditsException,
            $httpCode === 402,
            str_contains($lower, 'insufficient'),
            str_contains($lower, 'credit balance'),
            str_contains($lower, 'billing') => 'quota_credits_402',

            $exception instanceof RateLimitedException,
            $httpCode === 429,
            str_contains($lower, 'rate limit'),
            str_contains($lower, 'resource_exhausted'),
            str_contains($lower, 'quota'),
            str_contains($lower, 'too many requests') => 'rate_limit_429',

            $exception instanceof InvalidApiKeyException,
            in_array($httpCode, [401, 403], true),
            str_contains($lower, 'invalid api key'),
            str_contains($lower, 'api_key_invalid'),
            str_contains($lower, 'api key not valid'),
            str_contains($lower, 'unauthorized') => 'invalid_key_401',

            $exception instanceof ProviderConnectionException,
            in_array($httpCode, [408, 504], true),
            str_contains($lower, 'timed out'),
            str_contains($lower, 'curl error 28'),
            str_contains($lower, 'could not connect') => 'timeout_network',

            default => 'provider_error',
        };

        $meta = self::ERROR_CATALOG[$category];
        $resolvedHttp = $httpCode >= 400 && $httpCode < 600 ? $httpCode : $meta['http'];

        return [
            'http_code' => $resolvedHttp,
            'category' => $category,
            'badge' => $meta['badge'],
            'clean_message' => $rawMessage !== '' ? $rawMessage : $meta['title_ar'],
            'rate_limits' => $rateLimits !== [] ? $rateLimits : null,
        ];
    }

    /**
     * One-time idempotent import of existing assistant messages from `agent_conversation_messages`
     * so historical token & request totals appear immediately on first load.
     */
    private function backfillHistoricalConversationsOnce(): void
    {
        if (! $this->tableReady() || ! Schema::hasTable('agent_conversation_messages')) {
            return;
        }

        try {
            if (Setting::getValue('ai_telemetry_backfilled') === '1') {
                return;
            }

            $earliestLiveLog = AiUsageLog::query()->min('created_at');

            $query = DB::table('agent_conversation_messages')
                ->where('role', 'assistant');

            if ($earliestLiveLog !== null) {
                $query->where('created_at', '<', $earliestLiveLog);
            }

            $existingMessages = $query
                ->orderBy('created_at')
                ->limit(500)
                ->get(['id', 'agent', 'usage', 'meta', 'created_at']);

            foreach ($existingMessages as $row) {
                $meta = json_decode((string) ($row->meta ?? '{}'), true) ?: [];
                // Skip manual human replies stored in transcript
                if (isset($meta['source']) && in_array($meta['source'], ['human_agent', 'fallback'], true)) {
                    continue;
                }

                $usage = json_decode((string) ($row->usage ?? '{}'), true) ?: [];
                $inTok = (int) ($usage['input_tokens'] ?? 0);
                $outTok = (int) ($usage['output_tokens'] ?? 0);

                $agentClass = (string) ($row->agent ?? '');
                $isCopilot = str_contains($agentClass, 'DashboardAssistantAgent');
                $provider = (string) ($meta['provider'] ?? ($isCopilot ? 'gemini-copilot' : 'gemini-support'));
                $model = (string) ($meta['model'] ?? config('ai.providers.gemini.models.text.default', 'gemini-3.1-flash-lite'));
                $ts = $row->created_at ? Carbon::parse($row->created_at) : now();

                AiUsageLog::query()->create([
                    'invocation_id' => 'hist-'.$row->id,
                    'channel' => $isCopilot ? 'copilot' : 'support',
                    'agent' => class_basename($agentClass ?: 'CustomerSupportAgent'),
                    'provider' => $provider,
                    'driver' => str_starts_with($provider, 'gemini') ? 'gemini' : $provider,
                    'model' => $model,
                    'status' => AiUsageLog::STATUS_SUCCESS,
                    'http_code' => 200,
                    'input_tokens' => $inTok,
                    'output_tokens' => $outTok,
                    'reasoning_tokens' => (int) ($usage['reasoning_tokens'] ?? 0),
                    'cached_tokens' => (int) ($usage['cache_read_input_tokens'] ?? 0),
                    'total_tokens' => $inTok + $outTok,
                    'created_at' => $ts,
                    'updated_at' => $ts,
                ]);
            }

            Setting::setValue('ai_telemetry_backfilled', '1', 'ai');
        } catch (Throwable) {
            // Safe ignore.
        }
    }

    /**
     * Build per-model health, token totals, error counts, and exact problem diagnosis.
     *
     * @param  Collection<int, AiUsageLog>  $rows
     * @param  list<array<string, mixed>>  $slotQuotas
     * @return list<array<string, mixed>>
     */
    private function buildModelDiagnostics($rows, array $slotQuotas): array
    {
        $grouped = [];

        // Seed every configured/armed slot's default model so the admin sees all models even before first call
        foreach ($slotQuotas as $slot) {
            $key = $slot['provider'].'|'.$slot['default_model'];
            $grouped[$key] = [
                'provider' => $slot['provider'],
                'role' => $slot['role'],
                'model' => $slot['default_model'],
                'ready' => $slot['ready'],
                'requests' => 0,
                'success' => 0,
                'failovers' => 0,
                'errors' => 0,
                'input_tokens' => 0,
                'output_tokens' => 0,
                'latencies' => [],
                'counts_by_cat' => [],
                'latest_error_cat' => null,
                'latest_error_msg' => null,
                'latest_error_at' => null,
                'latest_status' => null,
            ];
        }

        // Merge observed traffic (rows are ordered newest-first)
        foreach ($rows as $row) {
            $key = $row->provider.'|'.$row->model;
            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'provider' => $row->provider,
                    'role' => str_contains($row->provider, 'copilot') ? 'Copilot' : (str_contains($row->provider, 'support') ? 'Support' : 'Active'),
                    'model' => $row->model,
                    'ready' => $this->guard->isReady($row->provider),
                    'requests' => 0,
                    'success' => 0,
                    'failovers' => 0,
                    'errors' => 0,
                    'input_tokens' => 0,
                    'output_tokens' => 0,
                    'latencies' => [],
                    'counts_by_cat' => [],
                    'latest_error_cat' => null,
                    'latest_error_msg' => null,
                    'latest_error_at' => null,
                    'latest_status' => null,
                ];
            }

            $g = &$grouped[$key];
            if ($g['latest_status'] === null) {
                $g['latest_status'] = $row->status;
            }

            $g['requests']++;
            $g['input_tokens'] += $row->input_tokens;
            $g['output_tokens'] += $row->output_tokens;

            if ($row->latency_ms !== null) {
                $g['latencies'][] = $row->latency_ms;
            }

            if ($row->status === AiUsageLog::STATUS_SUCCESS) {
                $g['success']++;
            } elseif ($row->status === AiUsageLog::STATUS_FAILOVER) {
                $g['failovers']++;
            } else {
                $g['errors']++;
            }

            if ($row->error_category) {
                $g['counts_by_cat'][$row->error_category] = ($g['counts_by_cat'][$row->error_category] ?? 0) + 1;
                if ($g['latest_error_cat'] === null) {
                    $g['latest_error_cat'] = $row->error_category;
                    $g['latest_error_msg'] = $row->error_message;
                    $g['latest_error_at'] = $row->created_at?->diffForHumans();
                }
            }
            unset($g);
        }

        $result = [];
        foreach ($grouped as $item) {
            $reqs = $item['requests'];
            $failures = $item['failovers'] + $item['errors'];
            $successRate = $reqs > 0 ? round(($item['success'] / $reqs) * 100, 1) : 100.0;
            $avgLat = $item['latencies'] !== [] ? (int) round(array_sum($item['latencies']) / count($item['latencies'])) : 0;

            $catMeta = $item['latest_error_cat'] ? (self::ERROR_CATALOG[$item['latest_error_cat']] ?? null) : null;

            $statusBadge = match (true) {
                ! $item['ready'] => ['state' => 'unconfigured', 'label' => '○ Key Not Set'],
                $item['latest_status'] === AiUsageLog::STATUS_ERROR => ['state' => 'critical', 'label' => '✖ Failing'],
                $item['latest_status'] === AiUsageLog::STATUS_FAILOVER => ['state' => 'degraded', 'label' => '⚡ Auto-Failover'],
                $failures > 0 => ['state' => 'recovered', 'label' => '● Healthy (Recovered)'],
                $reqs > 0 => ['state' => 'healthy', 'label' => '● Healthy (100%)'],
                default => ['state' => 'armed', 'label' => '● Armed & Ready'],
            };

            $errorPills = [];
            foreach ($item['counts_by_cat'] as $cat => $cnt) {
                $errorPills[] = [
                    'badge' => self::ERROR_CATALOG[$cat]['badge'] ?? $cat,
                    'severity' => self::ERROR_CATALOG[$cat]['severity'] ?? 'warn',
                    'count' => $cnt,
                ];
            }

            $result[] = [
                'provider' => $item['provider'],
                'role' => $item['role'],
                'model' => $item['model'],
                'ready' => $item['ready'],
                'status_state' => $statusBadge['state'],
                'status_label' => $statusBadge['label'],
                'requests' => $reqs,
                'success_rate' => $successRate,
                'failovers' => $item['failovers'],
                'errors' => $item['errors'],
                'input_tokens' => $item['input_tokens'],
                'output_tokens' => $item['output_tokens'],
                'total_tokens' => $item['input_tokens'] + $item['output_tokens'],
                'avg_latency_ms' => $avgLat,
                'error_pills' => $errorPills,
                'diagnosis_title' => $catMeta['title_ar'] ?? ($item['ready'] ? 'يعمل بكفاءة بدون أخطاء مسجلة' : 'لم يتم إدخال مفتاح API بعد'),
                'diagnosis_fix' => $catMeta['fix_ar'] ?? null,
                'latest_error_msg' => $item['latest_error_msg'],
                'latest_error_at' => $item['latest_error_at'],
            ];
        }

        // Put models with active issues or traffic at the top
        usort($result, function (array $a, array $b): int {
            $scoreA = ($a['errors'] * 1000) + ($a['failovers'] * 200) + $a['requests'] + ($a['ready'] ? 1 : 0);
            $scoreB = ($b['errors'] * 1000) + ($b['failovers'] * 200) + $b['requests'] + ($b['ready'] ? 1 : 0);

            return $scoreB <=> $scoreA;
        });

        return $result;
    }

    /**
     * Build bucketed chart series for Requests, Success Rate %, and Errors.
     *
     * @param  Collection<int, AiUsageLog>  $rows
     * @return list<array{label: string, requests: int, success: int, errors: int, success_rate: float}>
     */
    private function buildTimelineSeries($rows, int $days): array
    {
        $buckets = [];

        if ($days === 1) {
            for ($i = 23; $i >= 0; $i--) {
                $dt = now()->subHours($i);
                $key = $dt->format('Y-m-d H');
                $buckets[$key] = ['label' => $dt->format('H:00'), 'requests' => 0, 'success' => 0, 'errors' => 0];
            }
            foreach ($rows as $r) {
                $k = $r->created_at?->format('Y-m-d H') ?? '';
                if (isset($buckets[$k])) {
                    $buckets[$k]['requests']++;
                    if ($r->status === AiUsageLog::STATUS_SUCCESS) {
                        $buckets[$k]['success']++;
                    } else {
                        $buckets[$k]['errors']++;
                    }
                }
            }
        } else {
            for ($i = $days - 1; $i >= 0; $i--) {
                $dt = now()->subDays($i);
                $key = $dt->format('Y-m-d');
                $buckets[$key] = ['label' => $dt->format('M j'), 'requests' => 0, 'success' => 0, 'errors' => 0];
            }
            foreach ($rows as $r) {
                $k = $r->created_at?->format('Y-m-d') ?? '';
                if (isset($buckets[$k])) {
                    $buckets[$k]['requests']++;
                    if ($r->status === AiUsageLog::STATUS_SUCCESS) {
                        $buckets[$k]['success']++;
                    } else {
                        $buckets[$k]['errors']++;
                    }
                }
            }
        }

        $series = [];
        foreach ($buckets as $b) {
            $b['success_rate'] = $b['requests'] > 0
                ? round(($b['success'] / $b['requests']) * 100, 1)
                : 100.0;
            $series[] = $b;
        }

        return $series;
    }

    /**
     * @return array{badge: string, title_ar: string, ago: string, message: ?string}
     */
    private function formatIssueSummary(AiUsageLog $issue): array
    {
        $meta = self::ERROR_CATALOG[$issue->error_category ?? ''] ?? self::ERROR_CATALOG['provider_error'];

        return [
            'badge' => $meta['badge'],
            'title_ar' => $meta['title_ar'],
            'ago' => $issue->created_at?->diffForHumans() ?? '',
            'message' => $issue->error_message,
        ];
    }

    /**
     * Extract standard upstream rate-limit headers when present on the HTTP response.
     *
     * @return array<string, string>
     */
    private function extractRateLimitHeaders(Response $response): array
    {
        $candidates = [
            'limit_requests' => ['x-ratelimit-limit-requests', 'anthropic-ratelimit-requests-limit'],
            'remaining_requests' => ['x-ratelimit-remaining-requests', 'anthropic-ratelimit-requests-remaining'],
            'limit_tokens' => ['x-ratelimit-limit-tokens', 'anthropic-ratelimit-tokens-limit'],
            'remaining_tokens' => ['x-ratelimit-remaining-tokens', 'anthropic-ratelimit-tokens-remaining'],
            'reset_requests' => ['x-ratelimit-reset-requests', 'retry-after'],
        ];

        $extracted = [];
        foreach ($candidates as $normKey => $headerNames) {
            foreach ($headerNames as $header) {
                $val = $response->header($header);
                if (is_string($val) && trim($val) !== '') {
                    $extracted[$normKey] = trim($val);
                    break;
                }
            }
        }

        return $extracted;
    }

    private function resolveChannel(Agent $agent): string
    {
        if ($this->channelOverride !== null) {
            return $this->channelOverride;
        }

        if ($agent instanceof CustomerSupportAgent) {
            return $agent->session->isWhatsapp() ? 'whatsapp' : 'website';
        }

        if ($agent instanceof DashboardAssistantAgent) {
            return 'copilot';
        }

        return 'system';
    }

    private function consumeLatencyMs(?string $invocationId, string $providerName): ?int
    {
        if (! $invocationId) {
            return null;
        }

        $key = $invocationId.':'.$providerName;
        if (! isset($this->startTimes[$key])) {
            return null;
        }

        $started = $this->startTimes[$key];
        unset($this->startTimes[$key]);

        return max(1, (int) round((hrtime(true) - $started) / 1_000_000));
    }

    private function defaultModelLabel(string $driver): string
    {
        return match ($driver) {
            'gemini' => 'gemini-3.1-flash-lite',
            'deepseek' => 'deepseek-flash',
            'openai' => 'gpt-6.1-sol',
            'anthropic' => 'claude-sonnet-5-5',
            'xai' => 'grok-4.7',
            default => 'default',
        };
    }

    private function tableReady(): bool
    {
        try {
            return Schema::hasTable('ai_usage_logs');
        } catch (Throwable) {
            return false;
        }
    }
}
