<?php

declare(strict_types=1);

namespace App\Modules\Projects\Models;

use App\Modules\TenantCore\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TaskComment
 *
 * Team collaboration comment thread on a task.
 */
class TaskComment extends Model
{
    protected $connection = 'tenant';

    protected $table = 'task_comments';

    protected $fillable = [
        'task_id',
        'user_id',
        'comment',
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
