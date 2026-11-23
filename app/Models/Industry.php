<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * An industry sector shown in the numbered grid on the home page.
 */
class Industry extends Model
{
    protected $fillable = ['title', 'subtitle', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /** Scope: active industries in display order. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
