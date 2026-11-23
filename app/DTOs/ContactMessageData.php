<?php

namespace App\DTOs;

use App\Core\DTOs\BaseData;

/**
 * Typed input carrier for the ContactMessage entity between the HTTP layer and
 * ContactMessageService. Only the declared fields survive, which keeps mass
 * assignment explicit.
 */
class ContactMessageData extends BaseData
{
    protected static array $fields = ['name', 'email', 'company', 'services', 'budget_tier', 'timeline', 'brief', 'read_at'];

    public mixed $name = null;
    public mixed $email = null;
    public mixed $company = null;
    public mixed $services = null;
    public mixed $budget_tier = null;
    public mixed $timeline = null;
    public mixed $brief = null;
    public mixed $read_at = null;
}
