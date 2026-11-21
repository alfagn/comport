<?php

declare(strict_types=1);

namespace App\Modules\Billing\Models;

use App\Core\ValueObjects\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Plan
 *
 * Domain Entity representing a SaaS Subscription Plan stored in the landlord database.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int $price_monthly_minor
 * @property int $price_yearly_minor
 * @property string $currency
 * @property int $trial_days
 * @property array<int, string> $features List of unlocked modules: hr, crm, marketing, projects, finance
 * @property array<string, int> $limits Max quotas: max_users, max_employees, max_projects, storage_mb
 * @property bool $is_active
 * @property bool $is_featured
 * @property int $sort_order
 */
class Plan extends Model
{
    protected $connection = 'landlord';

    protected $table = 'plans';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly_minor',
        'price_yearly_minor',
        'currency',
        'trial_days',
        'features',
        'limits',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'price_monthly_minor' => 'integer',
        'price_yearly_minor' => 'integer',
        'trial_days' => 'integer',
        'features' => 'array',
        'limits' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    /**
     * Get monthly price as Money Value Object.
     */
    public function getMonthlyPriceAttribute(): Money
    {
        return new Money($this->price_monthly_minor, $this->currency);
    }

    /**
     * Get yearly price as Money Value Object.
     */
    public function getYearlyPriceAttribute(): Money
    {
        return new Money($this->price_yearly_minor, $this->currency);
    }

    /**
     * Check if this plan includes a given feature key.
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, (array) ($this->features ?? []), true);
    }

    /**
     * Retrieve quota limit for a resource (0 means unlimited).
     */
    public function getLimit(string $key, int $default = 0): int
    {
        return (int) (($this->limits ?? [])[$key] ?? $default);
    }
}
