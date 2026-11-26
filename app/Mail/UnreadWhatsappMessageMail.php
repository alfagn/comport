<?php

namespace App\Mail;

use App\Models\SupportChatSession;
use App\Models\WhatsappMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email alert sent to administrators when new customer WhatsApp messages arrive
 * and have not yet been viewed in the WhatsApp Inbox dashboard.
 */
class UnreadWhatsappMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<int, WhatsappMessage>  $unreadMessages
     */
    public function __construct(
        public SupportChatSession $session,
        public Collection $unreadMessages,
        public string $inboxUrl,
    ) {}

    public function envelope(): Envelope
    {
        $count = $this->unreadMessages->count();
        $label = $this->session->visitorLabel();
        $badge = $count > 1 ? " ({$count} new messages)" : '';

        return new Envelope(
            subject: "New WhatsApp Message from {$label}{$badge}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.whatsapp-unread',
        );
    }
}
