<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One editable content block of a public page (see the migration).
 */
class PageSection extends Model
{
    protected $fillable = ['page', 'key', 'label', 'type', 'value', 'sort_order'];
}
