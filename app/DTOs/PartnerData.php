<?php

namespace App\DTOs;

use App\Core\DTOs\BaseData;

/**
 * Typed input carrier for the Partner entity between the HTTP layer and
 * PartnerService. Only the declared fields survive, which keeps mass
 * assignment explicit.
 */
class PartnerData extends BaseData
{
    protected static array $fields = ['name', 'logo', 'url', 'tier', 'sort_order', 'is_active'];

    public mixed $name = null;
    public mixed $logo = null;
    public mixed $url = null;
    public mixed $tier = null;
    public mixed $sort_order = null;
    public mixed $is_active = null;
}
