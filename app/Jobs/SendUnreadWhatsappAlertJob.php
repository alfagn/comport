<?php

namespace App\Jobs;

use App\Services\WhatsappCustomerSupportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Checks whether an inbound WhatsApp message is still unseen after the grace
 * delay and dispatches an email alert to the admin team if so.
 */
class SendUnreadWhatsappAlertJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function __construct(public int $sessionId) {}

    public function handle(WhatsappCustomerSupportService $support): void
    {
        $support->notifyUnreadForSession($this->sessionId);
    }
}
