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
    protected static array $fields = ['title', 'slug', 'ref_code', 'hiring_manager_id', 'department', 'location', 'type', 'summary', 'intro', 'pills', 'tags', 'responsibilities', 'requirements', 'sections', 'is_active', 'sort_order'];

    public mixed $title = null;
    public mixed $slug = null;
    public mixed $ref_code = null;
    public mixed $hiring_manager_id = null;
    public mixed $department = null;
    public mixed $location = null;
    public mixed $type = null;
    public mixed $summary = null;
    public mixed $intro = null;
    public mixed $pills = null;
    public mixed $tags = null;
    public mixed $responsibilities = null;
    public mixed $requirements = null;
    public mixed $sections = null;
    public mixed $is_active = null;
    public mixed $sort_order = null;
}
