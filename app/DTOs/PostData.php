<?php

namespace App\DTOs;

use App\Core\DTOs\BaseData;

/**
 * Typed input carrier for the Post entity between the HTTP layer and
 * PostService. Only the declared fields survive, which keeps mass
 * assignment explicit.
 */
class PostData extends BaseData
{
    protected static array $fields = ['title', 'slug', 'type', 'category', 'excerpt', 'body', 'cover_image', 'video_url', 'meta', 'published_at', 'is_published'];

    public mixed $title = null;
    public mixed $slug = null;
    public mixed $type = null;
    public mixed $category = null;
    public mixed $excerpt = null;
    public mixed $body = null;
    public mixed $cover_image = null;
    public mixed $video_url = null;
    public mixed $meta = null;
    public mixed $published_at = null;
    public mixed $is_published = null;
}
