<?php

namespace App\Services;

use App\Models\Setting;
use App\Support\WebmOpusToOggConverter;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * Client & webhook payload parser for the official Meta WhatsApp Business Cloud API.
 *
 * Supports text, templates, quoted replies, emoji reactions, locations, and full
 * inbound/outbound media (photos, videos, voice notes, audio, documents, stickers)
 * plus delivery/read status callbacks (`sent`, `delivered`, `read`, `failed`).
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
     * Detect whether an identifier is a Meta Business-Scoped User ID (BSUID, e.g. `EG.134912...`)
     * used when a WhatsApp customer hides their phone number behind `@username`.
     */
    public function isBsuid(string $identifier): bool
    {
        return preg_match('/^[A-Za-z]{2}\.[A-Za-z0-9_-]{4,}$/', trim($identifier)) === 1;
    }

    /**
     * Normalize either a BSUID (`EG.xxx`) or an international E.164 phone number.
     */
    public function normalizeRecipientId(string $identifier): string
    {
        $trimmed = trim($identifier);
        if ($this->isBsuid($trimmed)) {
            [$cc, $rest] = explode('.', $trimmed, 2);

            return strtoupper($cc).'.'.$rest;
        }

        return $this->normalizePhone($trimmed);
    }

    /**
     * Build Meta's Graph API target field: `recipient` for BSUIDs, `to` for phone numbers.
     *
     * @return array{to?: string, recipient?: string}
     */
    public function buildRecipientField(string $to): array
    {
        $normalized = $this->normalizeRecipientId($to);
        if ($normalized === '') {
            throw new RuntimeException('Invalid WhatsApp recipient phone number or user ID.');
        }

        return $this->isBsuid($normalized)
            ? ['recipient' => $normalized]
            : ['to' => $normalized];
    }

    /**
     * Normalize phone numbers to international E.164 digits.
     */
    public function normalizePhone(string $phone): string
    {
        if ($this->isBsuid($phone)) {
            return '';
        }

        $digits = (string) preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (preg_match('/^0(1[0125]\d{8})$/', $digits, $matches)) {
            return '20'.$matches[1];
        }

        if (preg_match('/^200(1[0125]\d{8})$/', $digits, $matches)) {
            return '20'.$matches[1];
        }

        return $digits;
    }

    /**
     * Human-readable E.164 phone string (+20 10 5520 9612).
     */
    public function displayPhone(string $phone): string
    {
        if ($this->isBsuid($phone)) {
            return '';
        }

        $digits = $this->normalizePhone($phone);

        return $digits !== '' ? '+'.$digits : $phone;
    }

    /**
     * Spaced international phone string for WhatsApp Web style headers & cards.
     */
    public function formattedPhone(string $phone): string
    {
        if ($this->isBsuid($phone)) {
            return '';
        }

        $digits = $this->normalizePhone($phone);
        if ($digits === '') {
            return $phone;
        }

        if (preg_match('/^20(\d{2})(\d{4})(\d{4})$/', $digits, $m)) {
            return "+20 {$m[1]} {$m[2]} {$m[3]}";
        }

        if (preg_match('/^966(\d{2})(\d{3})(\d{4})$/', $digits, $m)) {
            return "+966 {$m[1]} {$m[2]} {$m[3]}";
        }

        return '+'.$digits;
    }

    /**
     * Send a standard WhatsApp text message (with optional quoted reply context).
     *
     * @return array<string, mixed>
     */
    public function sendTextMessage(
        string $to,
        string $body,
        bool $previewUrl = false,
        ?string $replyToWamid = null,
    ): array {
        $targetField = $this->buildRecipientField($to);

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            ...$targetField,
            'type' => 'text',
            'text' => [
                'preview_url' => $previewUrl,
                'body' => Str::limit(trim($body), 4000, '…'),
            ],
        ];

        if (filled($replyToWamid)) {
            $payload['context'] = ['message_id' => $replyToWamid];
        }

        return $this->postMessages($payload);
    }

    /**
     * Upload a binary media file to Meta's WhatsApp `/media` endpoint and return its `media_id`.
     * Automatically converts browser `audio/webm` (or voice recordings) into RFC 7845 `audio/ogg`
     * Opus via `ffmpeg` when installed or our built-in pure-PHP `WebmOpusToOggConverter`.
     *
     * @return array{media_id: string, mime_type: string, filename: string, converted_bytes: ?string}
     */
    public function uploadMedia(
        string $absolutePath,
        string $mimeType,
        string $filename,
        bool $isVoice = false,
    ): array {
        if (! $this->isConfigured()) {
            throw new RuntimeException('WhatsApp Cloud API credentials are not configured yet.');
        }

        if (! is_file($absolutePath)) {
            throw new RuntimeException('Media file not found on disk.');
        }

        $uploadPath = $absolutePath;
        $uploadMime = strtolower(trim(explode(';', $mimeType)[0]));
        $uploadFilename = $filename;
        $tempConverted = null;
        $convertedBytes = null;

        $headBytes = (string) file_get_contents($absolutePath, false, null, 0, 16);
        $isOggContainer = str_starts_with($headBytes, 'OggS');
        $isWebmContainer = str_starts_with($headBytes, "\x1A\x45\xDF\xA3");

        if ($isOggContainer) {
            $uploadMime = 'audio/ogg';
            $uploadFilename = pathinfo($filename, PATHINFO_FILENAME).'.ogg';
        } elseif ($isWebmContainer || in_array($uploadMime, ['audio/webm', 'video/webm'], true) || ($isVoice && $uploadMime !== 'audio/ogg')) {
            $converted = $this->transcodeWebmToOggOpus($absolutePath);
            if ($converted === null && $isWebmContainer) {
                $converted = WebmOpusToOggConverter::convertFile($absolutePath);
            }

            if ($converted !== null && is_file($converted)) {
                $tempConverted = $converted;
                $uploadPath = $converted;
                $uploadMime = 'audio/ogg';
                $uploadFilename = pathinfo($filename, PATHINFO_FILENAME).'.ogg';
                $convertedBytes = (string) file_get_contents($converted);
            } elseif ($uploadMime === 'audio/x-m4a' || $uploadMime === 'audio/m4a') {
                $uploadMime = 'audio/mp4';
                $uploadFilename = pathinfo($filename, PATHINFO_FILENAME).'.m4a';
            }
        }

        $baseUrl = rtrim((string) config('assistant.whatsapp.base_url', 'https://graph.facebook.com'), '/');
        $endpoint = "{$baseUrl}/{$this->apiVersion()}/{$this->phoneNumberId()}/media";

        try {
            $contents = $convertedBytes ?? (string) file_get_contents($uploadPath);
            $response = Http::withToken((string) $this->accessToken())
                ->acceptJson()
                ->timeout(45)
                ->attach('file', $contents, $uploadFilename, ['Content-Type' => $uploadMime])
                ->post($endpoint, [
                    'messaging_product' => 'whatsapp',
                    'type' => $uploadMime,
                ])
                ->throw();
        } catch (RequestException $exception) {
            $apiMsg = $exception->response?->json('error.message') ?: $exception->getMessage();
            throw new RuntimeException("WhatsApp media upload error: {$apiMsg}", previous: $exception);
        } finally {
            if ($tempConverted !== null && is_file($tempConverted)) {
                @unlink($tempConverted);
            }
        }

        $mediaId = (string) ($response->json('id') ?? '');
        if ($mediaId === '') {
            throw new RuntimeException('Meta Graph API did not return a media ID.');
        }

        return [
            'media_id' => $mediaId,
            'mime_type' => $uploadMime,
            'filename' => $uploadFilename,
            'converted_bytes' => $convertedBytes,
        ];
    }

    /**
     * Send an uploaded media message (`image`, `video`, `audio`, `document`, `sticker`).
     *
     * @return array<string, mixed>
     */
    public function sendMediaMessage(
        string $to,
        string $type,
        string $mediaId,
        ?string $caption = null,
        ?string $filename = null,
        ?string $replyToWamid = null,
        bool $isVoice = false,
        ?string $uploadedMime = null,
    ): array {
        $targetField = $this->buildRecipientField($to);

        $waType = $type === 'voice' ? 'audio' : $type;
        if (! in_array($waType, ['image', 'video', 'audio', 'document', 'sticker'], true)) {
            $waType = 'document';
        }

        $mediaPayload = ['id' => $mediaId];

        if (in_array($waType, ['image', 'video', 'document'], true) && filled($caption)) {
            $mediaPayload['caption'] = Str::limit(trim((string) $caption), 1024, '…');
        }

        if ($waType === 'document' && filled($filename)) {
            $mediaPayload['filename'] = $filename;
        }

        // Meta strictly requires `audio/ogg` (Opus) when `voice: true` is set; non-OGG audio (e.g. Safari AAC MP4) must omit `voice: true` to avoid #131053 Media upload error.
        $canFlagNativeVoice = $uploadedMime === null || $uploadedMime === '' || $uploadedMime === 'audio/ogg';
        if ($waType === 'audio' && ($isVoice || $type === 'voice') && $canFlagNativeVoice) {
            $mediaPayload['voice'] = true;
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            ...$targetField,
            'type' => $waType,
            $waType => $mediaPayload,
        ];

        if (filled($replyToWamid)) {
            $payload['context'] = ['message_id' => $replyToWamid];
        }

        return $this->postMessages($payload);
    }

    /**
     * Send a geographic pin / location card on WhatsApp.
     *
     * @return array<string, mixed>
     */
    public function sendLocationMessage(
        string $to,
        float $latitude,
        float $longitude,
        ?string $name = null,
        ?string $address = null,
        ?string $replyToWamid = null,
    ): array {
        $targetField = $this->buildRecipientField($to);

        $location = [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

        if (filled($name)) {
            $location['name'] = Str::limit(trim((string) $name), 120, '');
        }
        if (filled($address)) {
            $location['address'] = Str::limit(trim((string) $address), 240, '');
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            ...$targetField,
            'type' => 'location',
            'location' => $location,
        ];

        if (filled($replyToWamid)) {
            $payload['context'] = ['message_id' => $replyToWamid];
        }

        return $this->postMessages($payload);
    }

    /**
     * Send an emoji reaction (`👍`, `❤️`, `😂`, etc., or `""` to remove) on an existing message.
     *
     * @return array<string, mixed>
     */
    public function sendReaction(string $to, string $targetWamid, string $emoji): array
    {
        if (trim($targetWamid) === '') {
            throw new RuntimeException('Target WhatsApp message ID is required.');
        }

        $targetField = $this->buildRecipientField($to);

        return $this->postMessages([
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            ...$targetField,
            'type' => 'reaction',
            'reaction' => [
                'message_id' => $targetWamid,
                'emoji' => $emoji,
            ],
        ]);
    }

    /**
     * Download an inbound WhatsApp media binary using Meta's two-step Graph lookup.
     *
     * @return array{bytes: string, mime_type: string, size: int}|null
     */
    public function downloadMedia(string $mediaId): ?array
    {
        if (! $this->isConfigured() || trim($mediaId) === '') {
            return null;
        }

        $baseUrl = rtrim((string) config('assistant.whatsapp.base_url', 'https://graph.facebook.com'), '/');
        $metaEndpoint = "{$baseUrl}/{$this->apiVersion()}/{$mediaId}";

        try {
            $metaRes = Http::withToken((string) $this->accessToken())
                ->acceptJson()
                ->timeout(15)
                ->get($metaEndpoint)
                ->throw();

            $downloadUrl = (string) ($metaRes->json('url') ?? '');
            $mimeType = (string) ($metaRes->json('mime_type') ?? 'application/octet-stream');

            if ($downloadUrl === '') {
                return null;
            }

            $binaryRes = Http::withToken((string) $this->accessToken())
                ->timeout(30)
                ->get($downloadUrl)
                ->throw();

            $bytes = $binaryRes->body();
            if ($bytes === '') {
                return null;
            }

            return [
                'bytes' => $bytes,
                'mime_type' => $mimeType,
                'size' => strlen($bytes),
            ];
        } catch (Throwable) {
            return null;
        }
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
        $targetField = $this->buildRecipientField($to);

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
            ...$targetField,
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

        $text = (string) preg_replace('/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/u', '$1 ($2)', $text);
        $text = (string) preg_replace('/^#{1,6}\s+(.+)$/mu', '*$1*', $text);
        $text = (string) preg_replace('/\*\*(.+?)\*\*/su', '*$1*', $text);

        return Str::limit(trim($text), 3900, '…');
    }

    /**
     * Extract actionable customer messages (including rich media, voice notes,
     * locations, contacts, reactions, and quoted reply IDs) from Meta's webhook payload.
     *
     * @param  array<string, mixed>  $payload
     * @return list<array<string, mixed>>
     */
    public function extractIncomingMessages(array $payload): array
    {
        $extracted = [];

        foreach ((array) ($payload['entry'] ?? []) as $entry) {
            foreach ((array) ($entry['changes'] ?? []) as $change) {
                $value = (array) ($change['value'] ?? []);

                $contactsByWaId = [];
                foreach ((array) ($value['contacts'] ?? []) as $contact) {
                    $rawWaId = (string) (($contact['wa_id'] ?? '') !== '' ? $contact['wa_id'] : ($contact['user_id'] ?? ''));
                    $normContactId = $this->normalizeRecipientId($rawWaId);
                    if ($normContactId !== '') {
                        $contactsByWaId[$normContactId] = [
                            'name' => $contact['profile']['name'] ?? null,
                            'username' => $contact['profile']['username'] ?? ($contact['username'] ?? null),
                            'avatar' => $contact['profile']['picture_url'] ?? ($contact['profile']['avatar'] ?? null),
                        ];
                    }
                }

                foreach ((array) ($value['messages'] ?? []) as $msg) {
                    $from = (string) (($msg['from'] ?? '') !== '' ? $msg['from'] : ($msg['user_id'] ?? ($msg['from_user_id'] ?? '')));
                    $normFrom = $this->normalizeRecipientId($from);
                    $messageId = (string) ($msg['id'] ?? '');
                    $type = (string) ($msg['type'] ?? 'unknown');

                    if ($normFrom === '' || $messageId === '') {
                        continue;
                    }

                    $text = $this->extractMessageBody($msg, $type);
                    if ($text === null) {
                        continue;
                    }

                    $media = null;
                    if (in_array($type, ['image', 'video', 'audio', 'document', 'sticker'], true) && is_array($msg[$type] ?? null)) {
                        $mediaBlock = $msg[$type];
                        $media = [
                            'id' => (string) ($mediaBlock['id'] ?? ''),
                            'mime_type' => (string) ($mediaBlock['mime_type'] ?? 'application/octet-stream'),
                            'filename' => $mediaBlock['filename'] ?? null,
                            'voice' => (bool) ($mediaBlock['voice'] ?? false),
                        ];
                    }

                    $location = null;
                    if ($type === 'location' && is_array($msg['location'] ?? null)) {
                        $location = [
                            'latitude' => (float) ($msg['location']['latitude'] ?? 0),
                            'longitude' => (float) ($msg['location']['longitude'] ?? 0),
                            'name' => $msg['location']['name'] ?? null,
                            'address' => $msg['location']['address'] ?? null,
                        ];
                    }

                    $reaction = null;
                    if ($type === 'reaction' && is_array($msg['reaction'] ?? null)) {
                        $reaction = [
                            'message_id' => (string) ($msg['reaction']['message_id'] ?? ''),
                            'emoji' => (string) ($msg['reaction']['emoji'] ?? ''),
                        ];
                    }

                    $contactInfo = $contactsByWaId[$normFrom] ?? ($contactsByWaId[$from] ?? []);

                    $extracted[] = [
                        'wa_id' => $normFrom,
                        'profile_name' => $contactInfo['name'] ?? null,
                        'username' => $contactInfo['username'] ?? null,
                        'avatar_url' => $contactInfo['avatar'] ?? null,
                        'message_id' => $messageId,
                        'text' => trim($text),
                        'type' => $type,
                        'reply_to_wamid' => isset($msg['context']['id']) ? (string) $msg['context']['id'] : null,
                        'media' => $media,
                        'location' => $location,
                        'contacts' => $type === 'contacts' ? ($msg['contacts'] ?? []) : null,
                        'reaction' => $reaction,
                    ];
                }
            }
        }

        return $extracted;
    }

    /**
     * Extract outbound delivery & read status updates (`sent`, `delivered`, `read`, `failed`)
     * from Meta's webhook payload.
     *
     * @param  array<string, mixed>  $payload
     * @return list<array{wamid: string, status: string, recipient_id: string, error: ?string}>
     */
    public function extractStatusUpdates(array $payload): array
    {
        $updates = [];

        foreach ((array) ($payload['entry'] ?? []) as $entry) {
            foreach ((array) ($entry['changes'] ?? []) as $change) {
                $value = (array) ($change['value'] ?? []);

                foreach ((array) ($value['statuses'] ?? []) as $st) {
                    $wamid = (string) ($st['id'] ?? '');
                    $status = strtolower((string) ($st['status'] ?? ''));

                    if ($wamid === '' || ! in_array($status, ['sent', 'delivered', 'read', 'failed'], true)) {
                        continue;
                    }

                    $errorMsg = null;
                    if (! empty($st['errors'][0])) {
                        $errObj = (array) $st['errors'][0];
                        $code = isset($errObj['code']) ? (string) $errObj['code'] : '';
                        $title = trim((string) ($errObj['title'] ?? ''));
                        $details = trim((string) ($errObj['error_data']['details'] ?? ''));
                        $sameText = strtolower(rtrim($details, '.')) === strtolower(rtrim($title, '.'));
                        $baseMsg = ($details !== '' && ! $sameText)
                            ? ($title !== '' ? "{$title}: {$details}" : $details)
                            : ($title ?: (string) ($errObj['message'] ?? 'Delivery failed'));
                        $errorMsg = $code !== '' ? "(#{$code}) {$baseMsg}" : $baseMsg;
                    }

                    $updates[] = [
                        'wamid' => $wamid,
                        'status' => $status,
                        'recipient_id' => $this->normalizeRecipientId((string) ($st['recipient_id'] ?? ($st['recipient_user_id'] ?? ''))),
                        'error' => $errorMsg,
                    ];
                }
            }
        }

        return $updates;
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
            'sticker' => '[Customer sent a sticker]',
            'audio' => ! empty($msg['audio']['voice'])
                ? '[Customer sent a voice note]'
                : '[Customer sent an audio file]',
            'location' => filled($msg['location']['name'] ?? null)
                ? '📍 '.$msg['location']['name'].(filled($msg['location']['address'] ?? null) ? ' — '.$msg['location']['address'] : '')
                : '[Customer shared a location]',
            'contacts' => '[Customer shared a contact card]',
            'reaction' => (string) ($msg['reaction']['emoji'] ?? ''),
            default => null,
        };
    }

    /**
     * Subscribe the WhatsApp Business Account (WABA) to this Meta App's webhooks.
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

    /**
     * Convert a WebM audio recording to OGG Opus when ffmpeg is installed on the host.
     */
    protected function transcodeWebmToOggOpus(string $sourcePath): ?string
    {
        $targetPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'wa_voice_'.Str::uuid().'.ogg';

        try {
            $result = Process::timeout(15)->run([
                'ffmpeg', '-y', '-i', $sourcePath,
                '-vn', '-c:a', 'libopus', '-b:a', '32k', '-ar', '48000', '-ac', '1', '-application', 'voip',
                $targetPath,
            ]);

            if ($result->successful() && is_file($targetPath) && filesize($targetPath) > 0) {
                return $targetPath;
            }
        } catch (Throwable) {
        }

        if (is_file($targetPath)) {
            @unlink($targetPath);
        }

        return null;
    }
}
