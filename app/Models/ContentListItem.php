<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One card inside a named repeating content list (principle, timeline
 * milestone, benefit, hiring step, career track, culture card).
 */
class ContentListItem extends Model
{
    protected $fillable = ['group', 'title', 'body', 'meta', 'badge', 'cta', 'url', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
