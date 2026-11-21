<?php

declare(strict_types=1);

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class LeaveType
 *
 * Types of employee leaves (e.g. Annual, Sick, Casual, Unpaid).
 */
class LeaveType extends Model
{
    protected $connection = 'tenant';

    protected $table = 'leave_types';

    protected $fillable = [
        'name',
        'days_allowed',
        'is_paid',
    ];

    protected $casts = [
        'days_allowed' => 'integer',
        'is_paid' => 'boolean',
    ];

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'leave_type_id');
    }
}
