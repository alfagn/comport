<?php

declare(strict_types=1);

namespace App\Modules\Billing\Models;

use App\Modules\Tenancy\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Subscription
 *
 * Entity tracking a tenant's subscription lifecycle, renewals, and feature limits.
 * Stored in the landlord database.
 *
 * @property int $id
 * @property int $tenant_id
 * @property int $plan_id
 * @property string $status trialing|active|past_due|canceled|expired
 * @property string $billing_interval monthly|yearly
 * @property Carbon|null $trial_ends_at
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property Carbon|null $canceled_at
 * @property-read Tenant $tenant
 * @property-read Plan $plan
 */
class Subscription extends Model
{
    protected $connection = 'landlord';

    protected $table = 'subscriptions';

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'status',
        'billing_interval',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'canceled_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SubscriptionInvoice::class, 'subscription_id');
    }

    /**
     * Check if subscription is actively valid.
     */
    public function isActive(): bool
    {
        if ($this->status === 'active') {
            return $this->ends_at === null || $this->ends_at->isFuture();
        }

        if ($this->status === 'trialing') {
            return $this->trial_ends_at !== null && $this->trial_ends_at->isFuture();
        }

        return false;
    }

    /**
     * Check if a specific module feature is unlocked.
     */
    public function hasFeature(string $feature): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        return $this->plan?->hasFeature($feature) ?? false;
    }

    /**
     * Check if tenant is within resource limits.
     */
    public function canUseResource(string $resourceKey, int $currentCount): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        $limit = $this->plan?->getLimit($resourceKey, 0) ?? 0;

        // 0 represents unlimited quota
        if ($limit === 0) {
            return true;
        }

        return $currentCount < $limit;
    }
}
