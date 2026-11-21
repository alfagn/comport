<?php

declare(strict_types=1);

namespace App\Modules\HRM\Models;

use App\Core\ValueObjects\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PayrollItem
 *
 * Line item detail for a salary payslip (Allowance, Bonus, Deduction, Tax).
 *
 * @property int $id
 * @property int $payroll_id
 * @property string $type allowance|deduction
 * @property string $title
 * @property int $amount_minor
 */
class PayrollItem extends Model
{
    protected $connection = 'tenant';

    protected $table = 'payroll_items';

    protected $fillable = [
        'payroll_id',
        'type',
        'title',
        'amount_minor',
    ];

    protected $casts = [
        'amount_minor' => 'integer',
    ];

    /**
     * @return BelongsTo<Payroll, $this>
     */
    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class, 'payroll_id');
    }

    public function getAmountAttribute(): Money
    {
        /** @var Payroll|null $payroll */
        $payroll = $this->payroll;

        return new Money($this->amount_minor, $payroll?->currency ?: 'EGP');
    }
}
