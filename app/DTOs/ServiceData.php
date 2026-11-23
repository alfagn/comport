<?php

namespace App\DTOs;

use App\Core\DTOs\BaseData;

/**
 * Typed input carrier for the Service entity between the HTTP layer and
 * ServiceService. Only the declared fields survive, which keeps mass
 * assignment explicit.
 */
class ServiceData extends BaseData
{
    protected static array $fields = ['title', 'slug', 'tag', 'kicker', 'heading', 'description', 'deliverables', 'metric_num', 'metric_label', 'cta_label', 'cta_url', 'image', 'sort_order', 'is_active'];

    public mixed $title = null;
    public mixed $slug = null;
    public mixed $tag = null;
    public mixed $kicker = null;
    public mixed $heading = null;
    public mixed $description = null;
    public mixed $deliverables = null;
    public mixed $metric_num = null;
    public mixed $metric_label = null;
    public mixed $cta_label = null;
    public mixed $cta_url = null;
    public mixed $image = null;
    public mixed $sort_order = null;
    public mixed $is_active = null;
}
