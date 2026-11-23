<?php

namespace App\Services;

use App\Core\Services\BaseService;
use App\DTOs\ContactMessageData;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

/**
 * Business logic for the contact inbox: storing public briefs and
 * tracking read state for the dashboard.
 */
class ContactMessageService extends BaseService
{
    public function __construct(ContactMessageRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /** Store a brief submitted through the public contact form. */
    public function storeFromPublic(ContactMessageData $data): Model
    {
        return $this->repository->create($data->toArray());
    }

    /** Paginated inbox, unread messages first. */
    public function inbox(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    /** Open a message and mark it read in one step. */
    public function openAndMarkRead(int $id): Model
    {
        $message = $this->repository->find($id);
        $message->markAsRead();

        return $message;
    }

    /** Delete a message. */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
