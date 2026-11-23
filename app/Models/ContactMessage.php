<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A project brief sent through the public contact form.
 */
class ContactMessage extends Model
{
    protected $fillable = [
        'name', 'email', 'company', 'services',
        'budget_tier', 'timeline', 'brief', 'read_at',
    ];

    protected $casts = [
        'services' => 'array',
        'read_at' => 'datetime',
    ];

    /** Scope: unread messages first. */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /** Mark the message as read right now. */
    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }
}
