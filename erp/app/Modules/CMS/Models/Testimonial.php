<?php

declare(strict_types=1);

namespace App\Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Testimonial
 *
 * Client feedback and reviews featured across the website.
 */
class Testimonial extends Model
{
    protected $connection = 'landlord';

    protected $table = 'testimonials';

    protected $fillable = [
        'client_name',
        'client_position',
        'client_company',
        'content',
        'avatar',
        'rating',
        'is_published',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_published' => 'boolean',
    ];
}
