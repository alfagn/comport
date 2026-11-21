<?php

declare(strict_types=1);

namespace App\Modules\Billing\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Coupon
 *
 * Promotional and discount coupons for subscription billing.
 *
 * @property int $id
 * @property string $code
 * @property int|null $discount_percent
 * @property int|null $discount_amount_minor
 * @property Carbon|null $valid_until
 * @property int|null $max_uses
 * @property int $times_used
 * @property bool $is_active
 */
class Coupon extends Model
{
    protected $connection = 'landlord';

    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'discount_percent',
        'discount_amount_minor',
        'valid_until',
        'max_uses',
        'times_used',
        'is_active',
    ];

    protected $casts = [
        'discount_percent' => 'integer',
        'discount_amount_minor' => 'integer',
        'valid_until' => 'datetime',
        'max_uses' => 'integer',
        'times_used' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Check if coupon is currently valid.
     */
    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->valid_until !== null && $this->valid_until->isPast()) {
            return false;
        }

        if ($this->max_uses !== null && $this->times_used >= $this->max_uses) {
            return false;
        }

        return true;
    }
}
