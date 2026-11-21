<?php

declare(strict_types=1);

namespace App\Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Partner
 *
 * Strategic partners and technology alliances shown on the website.
 */
class Partner extends Model
{
    protected $connection = 'landlord';

    protected $table = 'partners';

    protected $fillable = [
        'name',
        'logo',
        'website_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
