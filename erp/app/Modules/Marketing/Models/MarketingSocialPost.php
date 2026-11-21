<?php

declare(strict_types=1);

namespace App\Modules\Marketing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class MarketingSocialPost
 *
 * Content calendar and scheduled posts for client social channels.
 */
class MarketingSocialPost extends Model
{
    protected $connection = 'tenant';

    protected $table = 'marketing_social_posts';

    protected $fillable = [
        'marketing_client_id',
        'platform', // facebook, instagram, linkedin, x, tiktok
        'caption',
        'media_url',
        'scheduled_at',
        'published_at',
        'status', // draft, scheduled, published, failed
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(MarketingClient::class, 'marketing_client_id');
    }
}
