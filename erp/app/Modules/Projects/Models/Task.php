<?php

declare(strict_types=1);

namespace App\Modules\Projects\Models;

use App\Modules\TenantCore\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Task
 *
 * Software task item with Kanban board states, priorities, and assigned engineers.
 *
 * @property int $id
 * @property int $project_id
 * @property int|null $milestone_id
 * @property string $title
 * @property string|null $description
 * @property string $priority low|medium|high|urgent
 * @property string $status backlog|todo|in_progress|in_review|completed
 * @property Carbon|null $due_date
 * @property int|null $assigned_to
 * @property float $estimated_hours
 * @property float $logged_hours
 */
class Task extends Model
{
    protected $connection = 'tenant';

    protected $table = 'tasks';

    protected $fillable = [
        'project_id',
        'milestone_id',
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'assigned_to',
        'estimated_hours',
        'logged_hours',
    ];

    protected $casts = [
        'due_date' => 'date',
        'estimated_hours' => 'float',
        'logged_hours' => 'float',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class, 'milestone_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class, 'task_id');
    }

    public function timesheets(): HasMany
    {
        return $this->hasMany(Timesheet::class, 'task_id');
    }
}
