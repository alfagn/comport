<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Delivers a newly submitted Contact Us project brief to the studio inbox,
 * with Reply-To pointed directly at the prospective client.
 */
class ContactBriefSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $contactMessage,
        public string $siteName = 'Empetix Solution',
    ) {}

    /**
     * Configure subject and set Reply-To to the client's address so clicking
     * Reply in Outlook responds directly to the sender.
     */
    public function envelope(): Envelope
    {
        $companySuffix = $this->contactMessage->company
            ? ' ('.$this->contactMessage->company.')'
            : '';

        return new Envelope(
            replyTo: [
                new Address($this->contactMessage->email, $this->contactMessage->name),
            ],
            subject: 'New Project Brief: '.$this->contactMessage->name.$companySuffix,
        );
    }

    /**
     * Render the Markdown brief summary with a direct link to the Admin Inbox.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.contact.brief-submitted',
            with: [
                'inboxUrl' => route('admin.inbox.show', $this->contactMessage->id),
                'servicesList' => ! empty($this->contactMessage->services)
                    ? implode(', ', $this->contactMessage->services)
                    : 'Not specified',
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
