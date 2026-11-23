<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A service offering ("solution") shown in the home-page tabbed panel.
 */
class Service extends Model
{
    protected $fillable = [
        'num', 'title', 'slug', 'tag', 'kicker', 'heading', 'description',
        'deliverables', 'metric_num', 'metric_label',
        'cta_label', 'cta_url', 'image', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'deliverables' => 'array',
        'is_active' => 'boolean',
    ];

    /** Scope: active services in display order. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
