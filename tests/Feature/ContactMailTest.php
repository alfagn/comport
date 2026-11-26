<?php

namespace Tests\Feature;

use App\Mail\ContactBriefConfirmation;
use App\Mail\ContactBriefSubmitted;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

/**
 * Contact Us email notifications: ensures public brief submissions notify
 * the studio inbox (with Reply-To pointing at the client), optionally send
 * a confirmation receipt, support queued delivery, and survive SMTP outages.
 */
class ContactMailTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /**
     * Valid Contact Us form payload.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Sara Ahmed',
            'email' => 'sara@acme.io',
            'company' => 'Acme FinTech',
            'service' => ['SaaS Engineering', 'Product Design'],
            'budget_tier' => '25k-50k',
            'timeline' => 'Q2',
            'description' => 'We need a multi-currency B2B settlement portal.',
        ], $overrides);
    }

    public function test_contact_submission_sends_brief_to_studio_setting_email_and_confirmation_to_client(): void
    {
        Mail::fake();
        Setting::setValue('email', 'hello@empetix.com');
        config([
            'mail.contact.to' => null,
            'mail.contact.send_confirmation' => true,
            'mail.contact.queue' => false,
        ]);

        $response = $this->post('/contact', $this->validPayload());

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contact_messages', [
            'email' => 'sara@acme.io',
            'company' => 'Acme FinTech',
        ]);

        Mail::assertSent(ContactBriefSubmitted::class, function (ContactBriefSubmitted $mail): bool {
            return $mail->hasTo('hello@empetix.com')
                && $mail->hasReplyTo('sara@acme.io')
                && $mail->hasSubject('New Project Brief: Sara Ahmed (Acme FinTech)');
        });

        Mail::assertSent(ContactBriefConfirmation::class, function (ContactBriefConfirmation $mail): bool {
            return $mail->hasTo('sara@acme.io')
                && $mail->hasReplyTo('hello@empetix.com');
        });
    }

    public function test_configured_contact_recipient_overrides_public_site_setting_email(): void
    {
        Mail::fake();
        Setting::setValue('email', 'public@empetix.com');
        config([
            'mail.contact.to' => 'leads@empetix.com',
            'mail.contact.send_confirmation' => false,
            'mail.contact.queue' => false,
        ]);

        $response = $this->post('/contact', $this->validPayload());

        $response->assertRedirect();
        Mail::assertSent(ContactBriefSubmitted::class, 'leads@empetix.com');
        Mail::assertNotSent(ContactBriefConfirmation::class);
    }

    public function test_contact_mailables_are_queued_when_queue_mode_is_enabled(): void
    {
        Mail::fake();
        Setting::setValue('email', 'hello@empetix.com');
        config([
            'mail.contact.to' => null,
            'mail.contact.send_confirmation' => true,
            'mail.contact.queue' => true,
        ]);

        $response = $this->post('/contact', $this->validPayload());

        $response->assertRedirect();
        Mail::assertQueued(ContactBriefSubmitted::class, 'hello@empetix.com');
        Mail::assertQueued(ContactBriefConfirmation::class, 'sara@acme.io');
    }

    public function test_invalid_contact_submission_sends_no_outgoing_mail(): void
    {
        Mail::fake();

        $response = $this->post('/contact', []);

        $response->assertSessionHasErrors(['name', 'email', 'description']);
        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingOutgoing();
    }

    public function test_mailables_render_brief_summary_and_dashboard_link(): void
    {
        $contactMessage = ContactMessage::create([
            'name' => 'Omar Khaled',
            'email' => 'omar@venture.eg',
            'company' => 'Venture Labs',
            'services' => ['Web Engineering', 'Growth'],
            'budget_tier' => '50k+',
            'timeline' => 'Immediate',
            'brief' => 'Architecting an enterprise logistics dashboard.',
        ]);

        $studioMail = new ContactBriefSubmitted($contactMessage, 'Empetix Solution');
        $confirmationMail = new ContactBriefConfirmation($contactMessage, 'Empetix Solution', 'hello@empetix.com');

        $studioMail->assertSeeInHtml('Omar Khaled');
        $studioMail->assertSeeInHtml('Venture Labs');
        $studioMail->assertSeeInHtml('Web Engineering, Growth');
        $studioMail->assertSeeInHtml('Architecting an enterprise logistics dashboard.');
        $studioMail->assertSeeInHtml(route('admin.inbox.show', $contactMessage->id));

        $confirmationMail->assertSeeInHtml('Thank you, Omar Khaled.');
        $confirmationMail->assertSeeInHtml('24 business hours');
        $confirmationMail->assertSeeInText('Web Engineering, Growth');
    }

    public function test_smtp_transport_exception_still_persists_brief_and_reports_error(): void
    {
        Exceptions::fake();
        Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('SMTP connection failed'));

        $response = $this->post('/contact', $this->validPayload());

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contact_messages', [
            'email' => 'sara@acme.io',
        ]);
        Exceptions::assertReported(RuntimeException::class);
    }
}
