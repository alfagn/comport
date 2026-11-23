<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A career opening. Named JobPost so the table never collides with
 * Laravel's queue `jobs` table.
 */
class JobPost extends Model
{
    protected $fillable = [
        'title', 'slug', 'ref_code', 'hiring_manager_id', 'department', 'location', 'type', 'summary',
        'intro', 'pills', 'tags', 'responsibilities', 'requirements', 'sections',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'pills' => 'array',
        'tags' => 'array',
        'responsibilities' => 'array',
        'requirements' => 'array',
        'sections' => 'array',
        'is_active' => 'boolean',
    ];

    /** The team member who reviews this opening's applications. */
    public function hiringManager(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Member::class, 'hiring_manager_id');
    }

    /** Applications received for this opening. */
    public function applications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /** Scope: open positions in display order. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
