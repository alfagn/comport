<?php

declare(strict_types=1);

namespace App\Modules\Projects\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Milestone
 *
 * Project deliverable milestone.
 */
class Milestone extends Model
{
    protected $connection = 'tenant';

    protected $table = 'milestones';

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'due_date',
        'status', // pending, completed
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'milestone_id');
    }
}
