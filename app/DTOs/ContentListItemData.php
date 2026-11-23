<?php

namespace App\DTOs;

use App\Core\DTOs\BaseData;

/**
 * Typed input carrier for a content list card between the HTTP layer and
 * ContentListItemService. Only the declared fields survive mass assignment.
 */
class ContentListItemData extends BaseData
{
    protected static array $fields = ['group', 'title', 'body', 'meta', 'badge', 'cta', 'url', 'sort_order', 'is_active'];

    public mixed $group = null;
    public mixed $title = null;
    public mixed $body = null;
    public mixed $meta = null;
    public mixed $badge = null;
    public mixed $cta = null;
    public mixed $url = null;
    public mixed $sort_order = null;
    public mixed $is_active = null;
}
