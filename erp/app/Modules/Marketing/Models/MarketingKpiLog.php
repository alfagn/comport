<?php

declare(strict_types=1);

namespace App\Modules\Marketing\Models;

use App\Core\ValueObjects\Money;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class MarketingKpiLog
 *
 * Daily or weekly performance metrics tracking impressions, clicks, leads, and conversion costs.
 *
 * @property int $id
 * @property int $marketing_campaign_id
 * @property Carbon $date
 * @property int $impressions
 * @property int $clicks
 * @property int $leads_generated
 * @property int $conversions
 * @property int $spend_minor
 * @property string $currency
 */
class MarketingKpiLog extends Model
{
    protected $connection = 'tenant';

    protected $table = 'marketing_kpi_logs';

    protected $fillable = [
        'marketing_campaign_id',
        'date',
        'impressions',
        'clicks',
        'leads_generated',
        'conversions',
        'spend_minor',
        'currency',
    ];

    protected $casts = [
        'date' => 'date',
        'impressions' => 'integer',
        'clicks' => 'integer',
        'leads_generated' => 'integer',
        'conversions' => 'integer',
        'spend_minor' => 'integer',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MarketingCampaign::class, 'marketing_campaign_id');
    }

    public function getSpendAttribute(): Money
    {
        return new Money($this->spend_minor, $this->currency ?: 'EGP');
    }

    /**
     * Calculate Cost Per Click (CPC) in decimal.
     */
    public function getCostPerClickAttribute(): float
    {
        return $this->clicks > 0 ? round(($this->spend_minor / 100) / $this->clicks, 2) : 0.0;
    }

    /**
     * Calculate Cost Per Lead (CPL) in decimal.
     */
    public function getCostPerLeadAttribute(): float
    {
        return $this->leads_generated > 0 ? round(($this->spend_minor / 100) / $this->leads_generated, 2) : 0.0;
    }
}
