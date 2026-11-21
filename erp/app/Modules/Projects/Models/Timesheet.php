<?php

declare(strict_types=1);

namespace App\Modules\Projects\Models;

use App\Modules\TenantCore\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Timesheet
 *
 * Billable and non-billable engineering hours logged by team members.
 */
class Timesheet extends Model
{
    protected $connection = 'tenant';

    protected $table = 'timesheets';

    protected $fillable = [
        'task_id',
        'user_id',
        'date',
        'hours',
        'is_billable',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'hours' => 'float',
        'is_billable' => 'boolean',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
