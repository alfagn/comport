<?php

namespace App\DTOs;

use App\Core\DTOs\BaseData;

/**
 * Typed input carrier for the Project entity between the HTTP layer and
 * ProjectService. Only the declared fields survive, which keeps mass
 * assignment explicit.
 */
class ProjectData extends BaseData
{
    protected static array $fields = ['title', 'slug', 'client', 'kicker', 'location', 'year', 'excerpt', 'body', 'facts', 'stats', 'sections', 'quote', 'quote_cite', 'video_poster', 'video_src', 'image', 'is_featured', 'is_active', 'sort_order', 'tags'];

    public mixed $title = null;
    public mixed $slug = null;
    public mixed $client = null;
    public mixed $kicker = null;
    public mixed $location = null;
    public mixed $year = null;
    public mixed $excerpt = null;
    public mixed $body = null;
    public mixed $facts = null;
    public mixed $stats = null;
    public mixed $sections = null;
    public mixed $quote = null;
    public mixed $quote_cite = null;
    public mixed $video_poster = null;
    public mixed $video_src = null;
    public mixed $image = null;
    public mixed $is_featured = null;
    public mixed $is_active = null;
    public mixed $sort_order = null;
    public mixed $tags = null;
}
