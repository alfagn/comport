<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One editable navigation link inside a named menu (utility bar, mobile drawer).
 */
class NavItem extends Model
{
    protected $fillable = ['menu', 'parent_id', 'label', 'url', 'sub', 'meta', 'image', 'sort_order', 'is_active'];

    /** Child links under a mega-menu column. */
    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('id');
    }

    /** The mega-menu column this link belongs to. */
    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
