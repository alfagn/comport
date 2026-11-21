<?php

declare(strict_types=1);

namespace App\Modules\HRM\Models;

use App\Core\ValueObjects\Money;
use App\Modules\TenantCore\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Employee
 *
 * Employee profile entity in the tenant database.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $employee_code
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string $gender male|female
 * @property Carbon|null $date_of_birth
 * @property Carbon|null $joining_date
 * @property int|null $department_id
 * @property int|null $designation_id
 * @property string $employment_status active|probation|resigned|terminated
 * @property int $basic_salary_minor
 * @property string $currency
 * @property string|null $bank_name
 * @property string|null $bank_account_number
 * @property string|null $iban
 */
class Employee extends Model
{
    protected $connection = 'tenant';

    protected $table = 'employees';

    protected $fillable = [
        'user_id',
        'employee_code',
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'joining_date',
        'department_id',
        'designation_id',
        'employment_status',
        'basic_salary_minor',
        'currency',
        'bank_name',
        'bank_account_number',
        'iban',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'basic_salary_minor' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'employee_id');
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class, 'employee_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getBasicSalaryAttribute(): Money
    {
        return new Money($this->basic_salary_minor, $this->currency ?: 'EGP');
    }
}
