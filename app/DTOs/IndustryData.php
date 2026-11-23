<?php

namespace App\DTOs;

use App\Core\DTOs\BaseData;

/**
 * Typed input carrier for the Industry entity between the HTTP layer and
 * IndustryService. Only the declared fields survive, which keeps mass
 * assignment explicit.
 */
class IndustryData extends BaseData
{
    protected static array $fields = ['num', 'title', 'subtitle', 'sort_order', 'is_active'];

    public mixed $num = null;
    public mixed $title = null;
    public mixed $subtitle = null;
    public mixed $sort_order = null;
    public mixed $is_active = null;
}
