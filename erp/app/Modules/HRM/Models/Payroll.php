<?php

declare(strict_types=1);

namespace App\Modules\HRM\Models;

use App\Core\ValueObjects\Money;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Payroll
 *
 * Monthly salary calculation record with allowances, deductions, and net salary.
 *
 * @property int $id
 * @property int $employee_id
 * @property int $month
 * @property int $year
 * @property int $basic_salary_minor
 * @property int $allowances_total_minor
 * @property int $deductions_total_minor
 * @property int $net_salary_minor
 * @property string $currency
 * @property string $status draft|approved|paid
 * @property string|null $payment_method
 * @property Carbon|null $paid_at
 * @property string|null $notes
 */
class Payroll extends Model
{
    protected $connection = 'tenant';

    protected $table = 'payrolls';

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'basic_salary_minor',
        'allowances_total_minor',
        'deductions_total_minor',
        'net_salary_minor',
        'currency',
        'status',
        'payment_method',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'basic_salary_minor' => 'integer',
        'allowances_total_minor' => 'integer',
        'deductions_total_minor' => 'integer',
        'net_salary_minor' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class, 'payroll_id');
    }

    public function getNetSalaryAttribute(): Money
    {
        return new Money($this->net_salary_minor, $this->currency ?: 'EGP');
    }

    public function getBasicSalaryAttribute(): Money
    {
        return new Money($this->basic_salary_minor, $this->currency ?: 'EGP');
    }
}
