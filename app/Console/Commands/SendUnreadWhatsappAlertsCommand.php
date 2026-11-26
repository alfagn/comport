<?php

namespace App\Console\Commands;

use App\Services\WhatsappCustomerSupportService;
use Illuminate\Console\Command;

class SendUnreadWhatsappAlertsCommand extends Command
{
    protected $signature = 'whatsapp:send-unread-alerts';

    protected $description = 'Send email notifications for unseen inbound WhatsApp messages past the grace window.';

    public function handle(WhatsappCustomerSupportService $support): int
    {
        $sent = $support->dispatchOverdueUnreadEmailAlerts();
        $this->info("Processed unread WhatsApp email alerts ({$sent} sent).");

        return self::SUCCESS;
    }
}
