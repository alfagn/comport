<?php

namespace App\Repositories\Contracts;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Persistence contract for contact-form messages.
 */
interface ContactMessageRepositoryInterface extends RepositoryInterface
{
    /** Number of unread messages, shown as the inbox badge. */
    public function unreadCount(): int;

    /** The most recent messages for the dashboard home widget. */
    public function latest(int $limit = 5): Collection;
}
