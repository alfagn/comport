<?php

declare(strict_types=1);

namespace App\Modules\Marketing\Models;

use App\Core\ValueObjects\Money;
use App\Modules\TenantCore\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class MarketingClient
 *
 * Client brand or marketing company managed by the marketing team / agency.
 *
 * @property int $id
 * @property string $name
 * @property string $company_name
 * @property string|null $industry
 * @property int $monthly_retainer_minor
 * @property string $currency
 * @property int|null $account_manager_id
 * @property string $status active|paused|churned
 * @property string|null $notes
 */
class MarketingClient extends Model
{
    protected $connection = 'tenant';

    protected $table = 'marketing_clients';

    protected $fillable = [
        'name',
        'company_name',
        'industry',
        'monthly_retainer_minor',
        'currency',
        'account_manager_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'monthly_retainer_minor' => 'integer',
    ];

    public function accountManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(MarketingCampaign::class, 'marketing_client_id');
    }

    public function socialPosts(): HasMany
    {
        return $this->hasMany(MarketingSocialPost::class, 'marketing_client_id');
    }

    public function getMonthlyRetainerAttribute(): Money
    {
        return new Money($this->monthly_retainer_minor, $this->currency ?: 'EGP');
    }
}
