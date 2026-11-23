<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A blog entry: article, video episode or photo essay.
 */
class Post extends Model
{
    protected $fillable = [
        'title', 'slug', 'type', 'category', 'excerpt', 'body',
        'cover_image', 'video_url', 'meta', 'published_at', 'is_published',
    ];

    protected $casts = [
        'meta' => 'array',
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    /** Scope: published posts, newest first. */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)->latest('published_at');
    }
}
