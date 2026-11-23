<?php

namespace App\Repositories\Contracts;

use App\Core\Contracts\RepositoryInterface;

/**
 * Persistence contract for the NewsletterSubscriber entity.
 *
 * Extends the base CRUD contract; add NewsletterSubscriber-specific finders here so
 * services never build queries themselves.
 */
interface NewsletterSubscriberRepositoryInterface extends RepositoryInterface
{
    //
}
