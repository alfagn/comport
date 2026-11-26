<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * Client & webhook payload parser for the official Meta WhatsApp Business Cloud API.
 */
class WhatsappCloudApiService
{
    public function isEnabled(): bool
    {
        return (bool) config('assistant.whatsapp.enabled', true)
            && Setting::getValue('whatsapp_enabled', '1') !== '0';
    }

    public function phoneNumberId(): ?string
    {
        return Setting::getValue('whatsapp_phone_number_id')
            ?: config('assistant.whatsapp.phone_number_id')
            ?: null;
    }

    public function businessAccountId(): ?string
    {
        return Setting::getValue('whatsapp_business_account_id')
            ?: config('assistant.whatsapp.business_account_id')
            ?: null;
    }

    public function accessToken(): ?string
    {
        return Setting::getValue('whatsapp_access_token')
            ?: config('assistant.whatsapp.access_token')
            ?: null;
    }

    public function verifyToken(): ?string
    {
        return Setting::getValue('whatsapp_verify_token')
            ?: config('assistant.whatsapp.verify_token')
            ?: null;
    }

    public function appSecret(): ?string
    {
        return Setting::getValue('whatsapp_app_secret')
            ?: config('assistant.whatsapp.app_secret')
            ?: null;
    }

    public function apiVersion(): string
    {
        return Setting::getValue('whatsapp_api_version')
            ?: (string) config('assistant.whatsapp.api_version', 'v22.0');
    }

    public function isConfigured(): bool
    {
        return filled($this->phoneNumberId()) && filled($this->accessToken());
    }

    /**
     * Resolve the public Click-to-WhatsApp (`wa.me`) URL for the website floating button.
     */
    public function publicClickToChatUrl(): ?string
    {
        if (! $this->isEnabled()) {
            return null;
        }

        $rawNumber = Setting::getValue('whatsapp_public_number')
            ?: config('assistant.whatsapp.public_number')
            ?: Setting::getValue('phone');

        $digits = $this->normalizePhone((string) $rawNumber);
        if ($digits === '') {
            return null;
        }

        $prefill = trim((string) (
            Setting::getValue('whatsapp_prefill_message')
            ?: config('assistant.whatsapp.prefill_message', 'مرحباً Empetix، أريد الاستفسار عن خدماتكم.')
        ));

        return 'https://wa.me/'.$digits.($prefill !== '' ? '?text='.rawurlencode($prefill) : '');
    }

    /**
     * Verify Meta's X-Hub-Signature-256 header when an App Secret is configured.
     */
    public function verifyWebhookSignature(string $rawBody, ?string $signatureHeader): bool
    {
        $secret = $this->appSecret();

        if (! filled($secret)) {
            return true;
        }

        if (! is_string($signatureHeader) || ! str_starts_with($signatureHeader, 'sha256=')) {
            return false;
        }

        $expected = 'sha256='.hash_hmac('sha256', $rawBody, (string) $secret);

        return hash_equals($expected, $signatureHeader);
    }

    /**
     * Normalize phone numbers to international E.164 digits (handles `00` prefixes
     * and local Egyptian mobile numbers like `01150949593` or `+2001150949593`).
     */
    public function normalizePhone(string $phone): string
    {
        $digits = (string) preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        // Local Egyptian mobile format: 010 / 011 / 012 / 015 + 8 digits (11 digits total)
        if (preg_match('/^0(1[0125]\d{8})$/', $digits, $matches)) {
            return '20'.$matches[1];
        }

        // Common typo: +20 followed by leading 0 (20010 / 20011 / 20012 / 20015 + 8 digits)
        if (preg_match('/^200(1[0125]\d{8})$/', $digits, $matches)) {
            return '20'.$matches[1];
        }

        return $digits;
    }

    /**
     * Human-readable E.164 phone string (+2010...).
     */
    public function displayPhone(string $phone): string
    {
        $digits = $this->normalizePhone($phone);

        return $digits !== '' ? '+'.$digits : $phone;
    }

    /**
     * Send a standard WhatsApp text message (within the 24h customer window).
     *
     * @return array<string, mixed>
     */
    public function sendTextMessage(string $to, string $body, bool $previewUrl = false): array
    {
        $recipient = $this->normalizePhone($to);
        if ($recipient === '') {
            throw new RuntimeException('Invalid WhatsApp recipient phone number.');
        }

        return $this->postMessages([
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $recipient,
            'type' => 'text',
            'text' => [
                'preview_url' => $previewUrl,
                'body' => Str::limit(trim($body), 4000, '…'),
            ],
        ]);
    }

    /**
     * Send an approved Meta WhatsApp template message (for outbound / 24h+ threads).
     *
     * @param  array<int, array<string, mixed>>  $components
     * @return array<string, mixed>
     */
    public function sendTemplateMessage(
        string $to,
        string $templateName,
        string $languageCode = 'en_US',
        array $components = [],
    ): array {
        $recipient = $this->normalizePhone($to);
        if ($recipient === '') {
            throw new RuntimeException('Invalid WhatsApp recipient phone number.');
        }

        $normalizedTemplateName = strtolower((string) preg_replace('/\s+/', '_', trim($templateName)));

        $template = [
            'name' => $normalizedTemplateName,
            'language' => ['code' => trim($languageCode) ?: 'en_US'],
        ];

        if ($components !== []) {
            $template['components'] = $components;
        }

        return $this->postMessages([
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $recipient,
            'type' => 'template',
            'template' => $template,
        ]);
    }

    /**
     * Mark an inbound customer message as read on WhatsApp (blue checkmarks).
     */
    public function markMessageAsRead(string $messageId): void
    {
        if (! $this->isConfigured() || $messageId === '') {
            return;
        }

        try {
            $baseUrl = rtrim((string) config('assistant.whatsapp.base_url', 'https://graph.facebook.com'), '/');
            $endpoint = "{$baseUrl}/{$this->apiVersion()}/{$this->phoneNumberId()}/messages";

            Http::withToken((string) $this->accessToken())
                ->acceptJson()
                ->timeout(5)
                ->post($endpoint, [
                    'messaging_product' => 'whatsapp',
                    'status' => 'read',
                    'message_id' => $messageId,
                ]);
        } catch (Throwable) {
            // Non-critical: never block replying if read receipt fails.
        }
    }

    /**
     * Convert Markdown output from the AI agent into clean WhatsApp formatting.
     */
    public function formatForWhatsapp(string $markdown): string
    {
        $text = trim($markdown);

        // Convert Markdown links [Label](https://...) -> Label (https://...)
        $text = (string) preg_replace('/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/u', '$1 ($2)', $text);

        // Convert Markdown headings to bold lines
        $text = (string) preg_replace('/^#{1,6}\s+(.+)$/mu', '*$1*', $text);

        // Convert **bold** to WhatsApp *bold*
        $text = (string) preg_replace('/\*\*(.+?)\*\*/su', '*$1*', $text);

        return Str::limit(trim($text), 3900, '…');
    }

    /**
     * Extract actionable customer messages from Meta's webhook payload,
     * ignoring delivery/read status callbacks.
     *
     * @param  array<string, mixed>  $payload
     * @return list<array{wa_id: string, profile_name: ?string, message_id: string, text: string, type: string}>
     */
    public function extractIncomingMessages(array $payload): array
    {
        $extracted = [];

        foreach ((array) ($payload['entry'] ?? []) as $entry) {
            foreach ((array) ($entry['changes'] ?? []) as $change) {
                $value = (array) ($change['value'] ?? []);

                $contactsByWaId = [];
                foreach ((array) ($value['contacts'] ?? []) as $contact) {
                    $waId = (string) ($contact['wa_id'] ?? '');
                    if ($waId !== '') {
                        $contactsByWaId[$waId] = $contact['profile']['name'] ?? null;
                    }
                }

                foreach ((array) ($value['messages'] ?? []) as $msg) {
                    $from = (string) ($msg['from'] ?? '');
                    $messageId = (string) ($msg['id'] ?? '');
                    $type = (string) ($msg['type'] ?? 'unknown');

                    if ($from === '' || $messageId === '') {
                        continue;
                    }

                    $text = $this->extractMessageBody($msg, $type);
                    if ($text === null || trim($text) === '') {
                        continue;
                    }

                    $extracted[] = [
                        'wa_id' => $this->normalizePhone($from),
                        'profile_name' => $contactsByWaId[$from] ?? null,
                        'message_id' => $messageId,
                        'text' => trim($text),
                        'type' => $type,
                    ];
                }
            }
        }

        return $extracted;
    }

    /**
     * @param  array<string, mixed>  $msg
     */
    protected function extractMessageBody(array $msg, string $type): ?string
    {
        return match ($type) {
            'text' => (string) ($msg['text']['body'] ?? ''),
            'button' => (string) ($msg['button']['text'] ?? $msg['button']['payload'] ?? ''),
            'interactive' => (string) (
                $msg['interactive']['button_reply']['title']
                ?? $msg['interactive']['list_reply']['title']
                ?? ''
            ),
            'image', 'video', 'document' => filled($msg[$type]['caption'] ?? null)
                ? (string) $msg[$type]['caption']
                : "[Customer sent a {$type} attachment]",
            'audio' => '[Customer sent a voice note]',
            default => null,
        };
    }

    /**
     * Subscribe the WhatsApp Business Account (WABA) to this Meta App's webhooks
     * (`POST /{waba_id}/subscribed_apps`) — required by Meta so real phone messages fire webhooks.
     *
     * @return array<string, mixed>
     */
    public function subscribeAppToWaba(): array
    {
        $wabaId = $this->businessAccountId();
        if (! filled($wabaId) || ! filled($this->accessToken())) {
            throw new RuntimeException('WhatsApp Business Account ID and Access Token are required to subscribe webhooks.');
        }

        $baseUrl = rtrim((string) config('assistant.whatsapp.base_url', 'https://graph.facebook.com'), '/');
        $endpoint = "{$baseUrl}/{$this->apiVersion()}/{$wabaId}/subscribed_apps";

        try {
            $response = Http::withToken((string) $this->accessToken())
                ->acceptJson()
                ->timeout(20)
                ->post($endpoint)
                ->throw();
        } catch (RequestException $exception) {
            $apiMsg = $exception->response?->json('error.message') ?: $exception->getMessage();
            throw new RuntimeException("Meta WABA subscription error: {$apiMsg}", previous: $exception);
        }

        return (array) $response->json();
    }

    /**
     * Perform an authenticated POST call against Meta's Graph API `/messages` endpoint.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    protected function postMessages(array $body): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('WhatsApp Cloud API credentials (Phone Number ID and Access Token) are not configured yet.');
        }

        $baseUrl = rtrim((string) config('assistant.whatsapp.base_url', 'https://graph.facebook.com'), '/');
        $endpoint = "{$baseUrl}/{$this->apiVersion()}/{$this->phoneNumberId()}/messages";

        try {
            $response = Http::withToken((string) $this->accessToken())
                ->acceptJson()
                ->timeout(25)
                ->post($endpoint, $body)
                ->throw();
        } catch (RequestException $exception) {
            $apiMsg = $exception->response?->json('error.message') ?: $exception->getMessage();
            throw new RuntimeException("WhatsApp Cloud API error: {$apiMsg}", previous: $exception);
        }

        return (array) $response->json();
    }
}
