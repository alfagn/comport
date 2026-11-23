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
        'title', 'slug', 'ref_code', 'department', 'location', 'type', 'summary',
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

    /** Scope: open positions in display order. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
