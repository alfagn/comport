<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Models;

use App\Modules\Billing\Models\Subscription;
use App\Modules\Billing\Models\SubscriptionInvoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Multitenancy\Models\Tenant as SpatieTenant;

/**
 * Class Tenant
 *
 * Domain Entity representing a Company / Tenant in the SaaS system.
 * Stored on the 'landlord' database connection.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $email
 * @property string|null $phone
 * @property string $database_name
 * @property bool $is_active
 * @property bool $is_internal
 * @property string $locale
 * @property string $currency
 * @property string $timezone
 * @property array<string, mixed>|null $settings
 * @property Carbon|null $trial_ends_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Subscription|null $currentSubscription
 */
class Tenant extends SpatieTenant
{
    /**
     * Tenant model always resides in the landlord database.
     *
     * @var string
     */
    protected $connection = 'landlord';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tenants';

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'database_name',
        'is_active',
        'is_internal',
        'locale',
        'currency',
        'timezone',
        'settings',
        'trial_ends_at',
    ];

    /**
     * Attribute casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_internal' => 'boolean',
        'settings' => 'array',
        'trial_ends_at' => 'datetime',
    ];

    public function domains(): HasMany
    {
        return $this->hasMany(TenantDomain::class, 'tenant_id');
    }

    public function primaryDomain(): HasOne
    {
        return $this->hasOne(TenantDomain::class, 'tenant_id')->where('is_primary', true);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'tenant_id');
    }

    /**
     * @return HasOne<Subscription, $this>
     */
    public function currentSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'tenant_id')
            ->whereIn('status', ['active', 'trialing'])
            ->latestOfMany();
    }

    public function subscriptionInvoices(): HasMany
    {
        return $this->hasMany(SubscriptionInvoice::class, 'tenant_id');
    }

    /**
     * Get the tenant specific database name.
     */
    public function getDatabaseName(): string
    {
        return $this->database_name ?: ('tenant_'.$this->slug);
    }

    /**
     * Check if tenant is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if tenant is internal (the parent holding company / your own business).
     */
    public function isInternal(): bool
    {
        return $this->is_internal;
    }

    /**
     * Check if a specific module/feature is unlocked for this tenant.
     */
    public function hasFeature(string $feature): bool
    {
        // Internal holding company has access to all features unconditionally
        if ($this->isInternal()) {
            return true;
        }

        /** @var Subscription|null $subscription */
        $subscription = $this->currentSubscription;
        if (! $subscription) {
            return false;
        }

        return $subscription->hasFeature($feature);
    }

    /**
     * Check resource quota limits (e.g. max_users, max_employees).
     */
    public function canUseResource(string $resourceKey, int $currentCount): bool
    {
        if ($this->isInternal()) {
            return true;
        }

        /** @var Subscription|null $subscription */
        $subscription = $this->currentSubscription;
        if (! $subscription) {
            return false;
        }

        return $subscription->canUseResource($resourceKey, $currentCount);
    }
}
