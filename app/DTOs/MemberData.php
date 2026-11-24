<?php

namespace App\DTOs;

use App\Core\DTOs\BaseData;

/**
 * Typed input carrier for the Member entity between the HTTP layer and
 * MemberService. Only the declared fields survive, which keeps mass
 * assignment explicit.
 */
class MemberData extends BaseData
{
    protected static array $fields = ['member_code', 'name', 'slug', 'role', 'kind', 'department', 'focus', 'lead', 'bio', 'expertise', 'photo', 'email', 'linkedin', 'twitter', 'sort_order', 'is_active'];

    public mixed $member_code = null;
    public mixed $name = null;
    public mixed $slug = null;
    public mixed $role = null;
    public mixed $kind = null;
    public mixed $department = null;
    public mixed $focus = null;
    public mixed $lead = null;
    public mixed $bio = null;
    public mixed $expertise = null;
    public mixed $photo = null;
    public mixed $email = null;
    public mixed $linkedin = null;
    public mixed $twitter = null;
    public mixed $sort_order = null;
    public mixed $is_active = null;
}
