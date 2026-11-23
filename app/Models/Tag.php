<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Reusable label attached to projects (FinTech, SaaS, Growth...).
 */
class Tag extends Model
{
    protected $fillable = ['name', 'slug'];

    /** Projects carrying this tag. */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }
}
