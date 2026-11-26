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
 * Sends an automatic acknowledgement receipt to a prospective client
 * after submitting the public Contact Us form.
 */
class ContactBriefConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $contactMessage,
        public string $siteName = 'Empetix Solution',
        public ?string $replyToEmail = null,
    ) {}

    /**
     * Configure confirmation subject and optional Reply-To pointing at the studio inbox.
     */
    public function envelope(): Envelope
    {
        $replyTo = $this->replyToEmail
            ? [new Address($this->replyToEmail, $this->siteName)]
            : [];

        return new Envelope(
            replyTo: $replyTo,
            subject: 'We received your project brief — '.$this->siteName,
        );
    }

    /**
     * Render the client confirmation Markdown view.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.contact.brief-confirmation',
            with: [
                'websiteUrl' => url('/work'),
                'servicesList' => ! empty($this->contactMessage->services)
                    ? implode(', ', $this->contactMessage->services)
                    : null,
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
