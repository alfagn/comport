<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One slide of the home-page hero rotation.
 */
class HeroSlide extends Model
{
    protected $fillable = [
        'tag', 'title', 'description', 'cta_label', 'cta_url',
        'image', 'meta', 'tab_label', 'tab_title', 'tab_sub', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'meta' => 'array',       // stat chips stored as a JSON array
        'is_active' => 'boolean',
    ];

    /** Scope: active slides in display order. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
