<?php

namespace App\Ai\Tools\Support;

use App\DTOs\ContactMessageData;
use App\Models\ContactMessage;
use App\Models\SupportChatSession;
use App\Services\ContactMessageService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Validation\ValidationException;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Captures a visitor's project brief or human-support request (from Website or
 * WhatsApp), delivers it to the dashboard Inbox, and marks the session handed off.
 */
class SubmitProjectInquiry implements Tool
{
    public function __construct(protected SupportChatSession $session) {}

    public function description(): Stringable|string
    {
        if ($this->session->isWhatsapp()) {
            return 'Deliver a WhatsApp customer project inquiry, quote request, or human-support handoff directly to the Empetix team Inbox. On WhatsApp we already have the customer phone number, so email is optional if they prefer WhatsApp follow-up.';
        }

        return 'Deliver a project inquiry, quote request, or human-support handoff directly to the Empetix team Inbox. Only call this AFTER the visitor has explicitly shared their name, email address, and what they need help with.';
    }

    public function handle(Request $request): Stringable|string
    {
        $isWhatsapp = $this->session->isWhatsapp();

        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:160'],
                'email' => [$isWhatsapp ? 'nullable' : 'required', 'email', 'max:160'],
                'summary' => ['required', 'string', 'min:10', 'max:4000'],
                'company' => ['nullable', 'string', 'max:160'],
                'services' => ['nullable', 'array', 'max:8'],
                'services.*' => ['string', 'max:120'],
                'budget_tier' => ['nullable', 'string', 'max:80'],
                'timeline' => ['nullable', 'string', 'max:120'],
            ]);
        } catch (ValidationException $exception) {
            return 'Validation failed: '.implode(' ', $exception->validator->errors()->all());
        }

        $channelLabel = $isWhatsapp ? 'WhatsApp Support Handoff' : 'AI Support Handoff';
        $phoneHeader = $this->session->visitor_phone ? "WhatsApp Phone: {$this->session->visitor_phone}\n" : '';
        $briefBody = "[{$channelLabel} — Chat #{$this->session->id}]\n{$phoneHeader}\n".trim($validated['summary']);

        $resolvedEmail = $validated['email'] ?? null;
        if (! filled($resolvedEmail)) {
            $digits = preg_replace('/\D+/', '', (string) $this->session->visitor_phone) ?: (string) $this->session->id;
            $resolvedEmail = "wa-{$digits}@whatsapp.empetix.local";
        }

        $existing = $this->session->contact_message_id
            ? ContactMessage::query()->find($this->session->contact_message_id)
            : null;

        if ($existing) {
            $existing->forceFill([
                'name' => $validated['name'],
                'email' => $resolvedEmail,
                'company' => $validated['company'] ?? $existing->company,
                'services' => $validated['services'] ?? $existing->services ?? [],
                'budget_tier' => $validated['budget_tier'] ?? $existing->budget_tier,
                'timeline' => $validated['timeline'] ?? $existing->timeline,
                'brief' => $briefBody,
                'read_at' => null,
            ])->save();
            $message = $existing;
        } else {
            /** @var ContactMessage $message */
            $message = app(ContactMessageService::class)->storeFromPublic(ContactMessageData::fromArray([
                'name' => $validated['name'],
                'email' => $resolvedEmail,
                'company' => $validated['company'] ?? null,
                'services' => $validated['services'] ?? [],
                'budget_tier' => $validated['budget_tier'] ?? null,
                'timeline' => $validated['timeline'] ?? null,
                'brief' => $briefBody,
            ]));
        }

        $this->session->forceFill([
            'visitor_name' => $validated['name'],
            'visitor_email' => $validated['email'] ?? $this->session->visitor_email,
            'status' => SupportChatSession::STATUS_HANDED_OFF,
            'handoff_summary' => $validated['summary'],
            'contact_message_id' => $message->getKey(),
        ])->save();

        return (string) json_encode([
            'ok' => true,
            'reference_id' => $message->getKey(),
            'message' => 'Inquiry recorded in the Empetix team Inbox and linked to this chat transcript. Our co-founders and engineering leads reply within 24 business hours.',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function schema(JsonSchema $schema): array
    {
        $emailField = $schema->string()->description('Customer email address (required on web; optional on WhatsApp if customer prefers phone follow-up).');

        return [
            'name' => $schema->string()->required()->description('Customer full name.'),
            'email' => $this->session->isWhatsapp() ? $emailField : $emailField->required(),
            'summary' => $schema->string()->required()->description('Clear summary of the project, question, or support request.'),
            'company' => $schema->string()->description('Optional company or organization name.'),
            'services' => $schema->array()->items($schema->string())->description('Optional requested capabilities, e.g. ["Web & SaaS", "AI Automation"].'),
            'budget_tier' => $schema->string()->description('Optional budget range if mentioned.'),
            'timeline' => $schema->string()->description('Optional target launch timeline if mentioned.'),
        ];
    }
}
