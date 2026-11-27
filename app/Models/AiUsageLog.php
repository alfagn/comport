<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Stores per-invocation AI token usage, latency, rate-limit headers, failovers,
 * and categorized upstream model errors across Website, WhatsApp, and Copilot.
 */
class AiUsageLog extends Model
{
    public const STATUS_SUCCESS = 'success';

    public const STATUS_FAILOVER = 'failover';

    public const STATUS_ERROR = 'error';

    protected $fillable = [
        'invocation_id',
        'channel',
        'agent',
        'provider',
        'driver',
        'model',
        'status',
        'http_code',
        'error_category',
        'error_message',
        'input_tokens',
        'output_tokens',
        'reasoning_tokens',
        'cached_tokens',
        'total_tokens',
        'latency_ms',
        'rate_limits',
        'created_at',
        'updated_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'http_code' => 'integer',
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
            'reasoning_tokens' => 'integer',
            'cached_tokens' => 'integer',
            'total_tokens' => 'integer',
            'latency_ms' => 'integer',
            'rate_limits' => 'array',
        ];
    }
}
