<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One editable navigation link inside a named menu (utility bar, mobile drawer).
 */
class NavItem extends Model
{
    protected $fillable = ['menu', 'label', 'url', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
