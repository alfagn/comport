<?php

namespace App\Jobs;

use App\Services\WhatsappCustomerSupportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Processes an inbound Meta WhatsApp webhook message (text, media, voice note,
 * location, contact card, or emoji reaction).
 */
class ProcessIncomingWhatsappMessage implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    /**
     * @param  array<string, mixed>|null  $media
     * @param  array<string, mixed>|null  $location
     * @param  array<int, mixed>|null  $contacts
     * @param  array<string, string>|null  $reaction
     */
    public function __construct(
        public string $waId,
        public ?string $profileName,
        public string $messageId,
        public string $text,
        public string $type = 'text',
        public ?string $replyToWamid = null,
        public ?array $media = null,
        public ?array $location = null,
        public ?array $contacts = null,
        public ?array $reaction = null,
    ) {}

    public function handle(WhatsappCustomerSupportService $support): void
    {
        $support->handleIncomingMessage(
            waId: $this->waId,
            profileName: $this->profileName,
            messageId: $this->messageId,
            text: $this->text,
            type: $this->type,
            replyToWamid: $this->replyToWamid,
            media: $this->media,
            location: $this->location,
            contacts: $this->contacts,
            reaction: $this->reaction,
        );
    }
}
