<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A partner/alliance logo shown on the home and partners pages.
 */
class Partner extends Model
{
    protected $fillable = ['name', 'logo', 'url', 'tier', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /** Scope: visible partners in display order. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
