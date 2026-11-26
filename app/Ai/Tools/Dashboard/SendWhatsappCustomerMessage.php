<?php

namespace App\Ai\Tools\Dashboard;

use App\Services\WhatsappCloudApiService;
use App\Services\WhatsappCustomerSupportService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Approvals\Approval;
use Laravel\Ai\Concerns\InteractsWithApprovals;
use Laravel\Ai\Contracts\Approvable;
use Laravel\Ai\Tools\Request;
use RuntimeException;
use Stringable;

/**
 * Allows the Dashboard AI Copilot to send a WhatsApp message (text or approved
 * Meta template) to a customer phone number after explicit admin approval.
 */
class SendWhatsappCustomerMessage extends DashboardTool implements Approvable
{
    use InteractsWithApprovals;

    public function permission(): ?string
    {
        return 'manage support chats';
    }

    public function description(): Stringable|string
    {
        return 'Send an outbound WhatsApp message (plain text or approved Meta template) to a customer phone number via the WhatsApp Business Cloud API. Always pauses for admin approval before sending.';
    }

    protected function needsApproval(Request $request): Approval|bool
    {
        $phone = (string) ($request['phone'] ?? '');
        $mode = (string) ($request['mode'] ?? 'text');

        return Approval::required("Send WhatsApp {$mode} message to {$phone}.");
    }

    protected function run(Request $request): string
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'min:7', 'max:30'],
            'customer_name' => ['nullable', 'string', 'max:120'],
            'mode' => ['nullable', 'in:text,template'],
            'content' => ['required', 'string', 'min:1', 'max:3500'],
            'template_language' => ['nullable', 'string', 'max:20'],
        ]);

        $api = app(WhatsappCloudApiService::class);
        if (! $api->isConfigured()) {
            return 'Cannot send WhatsApp message: WhatsApp Cloud API credentials (Phone Number ID & Access Token) are not configured yet in Support Chats -> WhatsApp Settings.';
        }

        try {
            $session = app(WhatsappCustomerSupportService::class)->sendOutboundCustomerMessage(
                phone: $validated['phone'],
                customerName: $validated['customer_name'] ?? null,
                mode: $validated['mode'] ?? 'text',
                content: $validated['content'],
                templateLang: $validated['template_language'] ?? 'en_US',
            );
        } catch (RuntimeException $exception) {
            return $exception->getMessage();
        }

        return $this->json([
            'result' => 'WhatsApp message sent.',
            'session_id' => $session->id,
            'phone' => $session->visitor_phone,
            'transcript_url' => route('admin.support-chats.show', $session->id),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'phone' => $schema->string()->required()->description('Customer phone number in international format, e.g. "+201012345678".'),
            'customer_name' => $schema->string()->description('Optional customer name.'),
            'mode' => $schema->string()->enum(['text', 'template'])->description('"text" for 24h window reply (default), or "template" for approved Meta template name.'),
            'content' => $schema->string()->required()->description('Message text (when mode=text) or Meta template name like "hello_world" (when mode=template).'),
            'template_language' => $schema->string()->description('Template language code when mode=template (default "en_US" or "ar").'),
        ];
    }
}
