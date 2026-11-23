<?php

namespace App\DTOs;

use App\Core\DTOs\BaseData;

/**
 * Typed input carrier for the JobPost entity between the HTTP layer and
 * JobPostService. Only the declared fields survive, which keeps mass
 * assignment explicit.
 */
class JobPostData extends BaseData
{
    protected static array $fields = ['title', 'slug', 'department', 'location', 'type', 'summary', 'intro', 'pills', 'responsibilities', 'requirements', 'is_active', 'sort_order'];

    public mixed $title = null;
    public mixed $slug = null;
    public mixed $department = null;
    public mixed $location = null;
    public mixed $type = null;
    public mixed $summary = null;
    public mixed $intro = null;
    public mixed $pills = null;
    public mixed $responsibilities = null;
    public mixed $requirements = null;
    public mixed $is_active = null;
    public mixed $sort_order = null;
}
