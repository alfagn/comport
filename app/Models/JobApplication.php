<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One application to an opening, including the private path of the
 * uploaded CV. Status is a lightweight review marker for the inbox.
 */
class JobApplication extends Model
{
    protected $fillable = [
        'job_post_id', 'full_name', 'email', 'phone', 'location',
        'linkedin', 'portfolio', 'experience_years', 'notice_period',
        'achievement', 'cv_path', 'status',
    ];

    /** The opening this application answers. */
    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }

    /** Scope: applications nobody has opened yet. */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /** Mark the application as reviewed by the hiring team. */
    public function markReviewed(): void
    {
        if ($this->status !== 'reviewed') {
            $this->forceFill(['status' => 'reviewed'])->save();
        }
    }
}
