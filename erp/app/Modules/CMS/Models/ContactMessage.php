<?php

declare(strict_types=1);

namespace App\Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ContactMessage
 *
 * Inquiries and messages received through the public contact form.
 */
class ContactMessage extends Model
{
    protected $connection = 'landlord';

    protected $table = 'contact_messages';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'is_read',
        'is_replied',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_replied' => 'boolean',
    ];
}
