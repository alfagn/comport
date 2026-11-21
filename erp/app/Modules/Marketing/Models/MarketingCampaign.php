<?php

declare(strict_types=1);

namespace App\Modules\Marketing\Models;

use App\Core\ValueObjects\Money;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class MarketingCampaign
 *
 * Paid advertising campaign across channels (Meta Ads, Google, TikTok, SEO, Influencers).
 *
 * @property int $id
 * @property string $name
 * @property int|null $marketing_client_id
 * @property string $channel meta|google_ads|tiktok|seo|email|linkedin|influencer
 * @property int $budget_minor
 * @property int $spent_minor
 * @property string $currency
 * @property string|null $target_audience
 * @property string $status planning|active|paused|completed
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 */
class MarketingCampaign extends Model
{
    protected $connection = 'tenant';

    protected $table = 'marketing_campaigns';

    protected $fillable = [
        'name',
        'marketing_client_id',
        'channel',
        'budget_minor',
        'spent_minor',
        'currency',
        'target_audience',
        'status',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'budget_minor' => 'integer',
        'spent_minor' => 'integer',
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(MarketingClient::class, 'marketing_client_id');
    }

    public function kpiLogs(): HasMany
    {
        return $this->hasMany(MarketingKpiLog::class, 'marketing_campaign_id');
    }

    public function getBudgetAttribute(): Money
    {
        return new Money($this->budget_minor, $this->currency ?: 'EGP');
    }

    public function getSpentAttribute(): Money
    {
        return new Money($this->spent_minor, $this->currency ?: 'EGP');
    }
}
