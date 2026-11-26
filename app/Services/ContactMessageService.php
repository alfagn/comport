<?php

namespace App\Services;

use App\Core\Services\BaseService;
use App\DTOs\ContactMessageData;
use App\Mail\ContactBriefConfirmation;
use App\Mail\ContactBriefSubmitted;
use App\Models\ContactMessage;
use App\Models\Setting;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Business logic for the contact inbox: storing public briefs,
 * dispatching email notifications, and tracking read state for the dashboard.
 */
class ContactMessageService extends BaseService
{
    public function __construct(ContactMessageRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /** Store a brief submitted through the public contact form and notify the studio. */
    public function storeFromPublic(ContactMessageData $data): Model
    {
        $message = $this->repository->create($data->toArray());

        if ($message instanceof ContactMessage) {
            try {
                $this->sendBriefNotifications($message);
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        return $message;
    }

    /** Resolve the studio inbox email address receiving Contact Us briefs. */
    public function resolveNotificationRecipient(): ?string
    {
        $configuredRecipient = trim((string) config('mail.contact.to', ''));
        if ($configuredRecipient !== '') {
            return $configuredRecipient;
        }

        $studioEmail = trim((string) Setting::getValue('email', ''));
        if ($studioEmail !== '') {
            return $studioEmail;
        }

        $fallbackFrom = trim((string) config('mail.from.address', ''));

        return $fallbackFrom !== '' ? $fallbackFrom : null;
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

    /** Dispatch studio alert and optional client confirmation via send or queue. */
    private function sendBriefNotifications(ContactMessage $message): void
    {
        $siteName = Setting::getValue('site_name') ?: (string) config('mail.from.name', config('app.name', 'Empetix Solution'));
        $recipient = $this->resolveNotificationRecipient();

        if ($recipient !== null) {
            $this->deliverMail($recipient, new ContactBriefSubmitted($message, $siteName));
        }

        if ((bool) config('mail.contact.send_confirmation', true) && filled($message->email)) {
            $this->deliverMail(
                $message->email,
                new ContactBriefConfirmation($message, $siteName, $recipient),
            );
        }
    }

    /** Send immediately by default, or push onto the queue after DB commit when enabled. */
    private function deliverMail(string $recipient, Mailable $mailable): void
    {
        if ((bool) config('mail.contact.queue', false)) {
            Mail::to($recipient)->queue($mailable->afterCommit());

            return;
        }

        Mail::to($recipient)->send($mailable);
    }
}
