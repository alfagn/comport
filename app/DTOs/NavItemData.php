<?php

namespace App\DTOs;

use App\Core\DTOs\BaseData;

/**
 * Typed input carrier for a navigation link between the HTTP layer and
 * NavItemService. Only the declared fields survive mass assignment.
 */
class NavItemData extends BaseData
{
    protected static array $fields = ['menu', 'parent_id', 'label', 'url', 'sub', 'meta', 'image', 'sort_order', 'is_active'];

    public mixed $menu = null;
    public mixed $parent_id = null;
    public mixed $label = null;
    public mixed $url = null;
    public mixed $sub = null;
    public mixed $meta = null;
    public mixed $image = null;
    public mixed $sort_order = null;
    public mixed $is_active = null;
}
