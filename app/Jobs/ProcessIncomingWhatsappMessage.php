<?php

namespace App\Jobs;

use App\Services\WhatsappCustomerSupportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Processes an inbound Meta WhatsApp webhook message (either synchronously or
 * on the background queue depending on `assistant.whatsapp.queue_webhooks`).
 */
class ProcessIncomingWhatsappMessage implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function __construct(
        public string $waId,
        public ?string $profileName,
        public string $messageId,
        public string $text,
    ) {}

    public function handle(WhatsappCustomerSupportService $support): void
    {
        $support->handleIncomingMessage(
            waId: $this->waId,
            profileName: $this->profileName,
            messageId: $this->messageId,
            text: $this->text,
        );
    }
}
