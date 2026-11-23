<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * A portfolio project / case study with normalized tags.
 */
class Project extends Model
{
    protected $fillable = [
        'title', 'slug', 'client', 'kicker', 'year', 'excerpt',
        'body', 'image', 'is_featured', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    /** Tags attached through the project_tag pivot. */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    /** Scope: published projects in display order. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /** Scope: projects pinned to the home page. */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
