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
    protected static array $fields = ['title', 'slug', 'client', 'kicker', 'year', 'excerpt', 'body', 'image', 'is_featured', 'is_active', 'sort_order', 'tags'];

    public mixed $title = null;
    public mixed $slug = null;
    public mixed $client = null;
    public mixed $kicker = null;
    public mixed $year = null;
    public mixed $excerpt = null;
    public mixed $body = null;
    public mixed $image = null;
    public mixed $is_featured = null;
    public mixed $is_active = null;
    public mixed $sort_order = null;
    public mixed $tags = null;
}
