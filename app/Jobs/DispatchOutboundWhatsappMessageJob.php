<?php

namespace App\Jobs;

use App\Models\WhatsappMessage;
use App\Services\WhatsappCustomerSupportService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatches an outbound WhatsApp message (`status = sending`) to Meta Cloud API
 * immediately after the HTTP response flushes to the browser (`dispatchAfterResponse`),
 * so the Admin UI experiences zero send latency and shows the WhatsApp clock icon (`🕒`)
 * transitioning seamlessly to `✓` -> `✓✓` -> blue `✓✓`.
 */
class DispatchOutboundWhatsappMessageJob
{
    use Dispatchable, SerializesModels;

    public function __construct(public int $whatsappMessageId) {}

    public function handle(WhatsappCustomerSupportService $supportService): void
    {
        $message = WhatsappMessage::query()->with('session')->find($this->whatsappMessageId);

        if (! $message || ! $message->session) {
            return;
        }

        $supportService->deliverPendingOutboundMessage($message);
    }
}
