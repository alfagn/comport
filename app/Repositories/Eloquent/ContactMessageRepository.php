<?php

namespace App\Repositories\Eloquent;

use App\Core\Repositories\EloquentRepository;
use App\Models\ContactMessage;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Eloquent-backed repository for contact-form messages (the admin inbox).
 */
class ContactMessageRepository extends EloquentRepository implements ContactMessageRepositoryInterface
{
    protected string $model = ContactMessage::class;

    /** Number of unread messages, shown as the inbox badge. */
    public function unreadCount(): int
    {
        return $this->query()->unread()->count();
    }

    /** The most recent messages for the dashboard home widget. */
    public function latest(int $limit = 5): Collection
    {
        return $this->query()->latest()->limit($limit)->get();
    }
}
