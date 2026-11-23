<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A person: founder (rich profile + detail page) or team member (grid card).
 */
class Member extends Model
{
    protected $fillable = [
        'name', 'slug', 'role', 'kind', 'focus', 'bio', 'photo',
        'email', 'linkedin', 'twitter', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'bio' => 'array',        // array of paragraphs
        'is_active' => 'boolean',
    ];

    /** Scope: founders only, in display order. */
    public function scopeFounders($query)
    {
        return $query->where('kind', 'founder')->where('is_active', true)->orderBy('sort_order');
    }

    /** Scope: regular team members only, in display order. */
    public function scopeTeam($query)
    {
        return $query->where('kind', 'member')->where('is_active', true)->orderBy('sort_order');
    }
}
