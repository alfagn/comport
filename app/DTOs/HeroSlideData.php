<?php

namespace App\DTOs;

use App\Core\DTOs\BaseData;

/**
 * Typed input carrier for the HeroSlide entity between the HTTP layer and
 * HeroSlideService. Only the declared fields survive, which keeps mass
 * assignment explicit.
 */
class HeroSlideData extends BaseData
{
    protected static array $fields = ['tag', 'title', 'description', 'cta_label', 'cta_url', 'image', 'meta', 'tab_label', 'tab_title', 'tab_sub', 'sort_order', 'is_active'];

    public mixed $tag = null;
    public mixed $title = null;
    public mixed $description = null;
    public mixed $cta_label = null;
    public mixed $cta_url = null;
    public mixed $image = null;
    public mixed $meta = null;
    public mixed $tab_label = null;
    public mixed $tab_title = null;
    public mixed $tab_sub = null;
    public mixed $sort_order = null;
    public mixed $is_active = null;
}
