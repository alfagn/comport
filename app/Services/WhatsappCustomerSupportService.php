<?php

namespace App\Services;

use App\Ai\Agents\CustomerSupportAgent;
use App\Jobs\DispatchOutboundWhatsappMessageJob;
use App\Jobs\SendUnreadWhatsappAlertJob;
use App\Mail\UnreadWhatsappMessageMail;
use App\Models\Setting;
use App\Models\SupportChatSession;
use App\Models\User;
use App\Models\WhatsappMessage;
use App\Support\WebmOpusToOggConverter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Ai\Contracts\ConversationStore;
use Laravel\Ai\Enums\MessageStatus;
use Laravel\Ai\Files\Audio;
use Laravel\Ai\Files\Document;
use Laravel\Ai\Files\Image;
use Laravel\Ai\Messages\UserMessage;
use Laravel\Ai\Models\Conversation;
use Laravel\Ai\Models\ConversationMessage;
use Throwable;

/**
 * Orchestrates inbound & outbound WhatsApp Business conversations, rich media
 * (photos, videos, voice notes, audio, documents, locations, reactions, replies),
 * delivery/read checkmarks, AI auto-responses, and unread email notifications.
 */
class WhatsappCustomerSupportService
{
    public const MEDIA_DISK = 'local';

    public const MEDIA_DIR = 'whatsapp-media';

    public function __construct(
        private WhatsappCloudApiService $whatsapp,
        private AiRuntimeGuard $guard,
        private WhatsappMediaCrypto $mediaCrypto,
    ) {}

    /**
     * Process an inbound customer message or reaction from Meta's webhook.
     *
     * @param  array<string, mixed>|null  $media
     * @param  array<string, mixed>|null  $location
     * @param  array<int, mixed>|null  $contacts
     * @param  array<string, string>|null  $reaction
     */
    public function handleIncomingMessage(
        string $waId,
        ?string $profileName,
        string $messageId,
        string $text,
        string $type = 'text',
        ?string $replyToWamid = null,
        ?array $media = null,
        ?array $location = null,
        ?array $contacts = null,
        ?array $reaction = null,
        ?string $username = null,
        ?string $avatarUrl = null,
    ): ?SupportChatSession {
        $normalizedWaId = $this->whatsapp->normalizePhone($waId);
        if ($normalizedWaId === '') {
            return null;
        }

        if ($type !== 'reaction' && trim($text) === '' && $media === null && $location === null && $contacts === null) {
            return null;
        }

        // Idempotency guard against Meta webhook retries
        if ($messageId !== '' && ! Cache::add('whatsapp:wamid:'.$messageId, true, now()->addHours(24))) {
            return null;
        }

        $session = $this->findOrCreateWhatsappSession($normalizedWaId, $profileName, $username, $avatarUrl);

        // Customer emoji reaction on an existing message
        if ($type === 'reaction' && ! empty($reaction['message_id'])) {
            WhatsappMessage::query()
                ->where('support_chat_session_id', $session->id)
                ->where('wa_message_id', $reaction['message_id'])
                ->update(['reaction_emoji' => $reaction['emoji'] !== '' ? $reaction['emoji'] : null]);

            return $session;
        }

        if ($messageId !== '' && $session->last_external_message_id === $messageId) {
            return $session;
        }

        if ((bool) config('assistant.whatsapp.mark_as_read', true) && $messageId !== '') {
            $this->whatsapp->markMessageAsRead($messageId);
        }

        $this->syncLegacyMessagesIfNeeded($session);

        // Download & persist inbound media (image, voice note, audio, video, document, sticker)
        $storedMedia = $this->downloadAndStoreInboundMedia($session, $type, $media);
        $resolvedType = ($type === 'audio' && ! empty($media['voice'])) ? 'voice' : $type;

        $metadata = array_filter([
            'location' => $location,
            'contacts' => $contacts,
            'quoted_preview' => $this->lookupQuotedPreview($session->id, $replyToWamid),
        ], fn ($v): bool => $v !== null && $v !== []);

        $adminActivelyViewingThisChat = Cache::has("whatsapp:admin_viewing:{$session->id}");

        $inboundRecord = WhatsappMessage::query()->create([
            'support_chat_session_id' => $session->id,
            'wa_message_id' => $messageId ?: null,
            'reply_to_wa_message_id' => $replyToWamid ?: null,
            'direction' => WhatsappMessage::DIRECTION_INBOUND,
            'sender_type' => WhatsappMessage::SENDER_CUSTOMER,
            'type' => $resolvedType,
            'body' => $text !== '' ? $text : null,
            'media_id' => $media['id'] ?? null,
            'media_disk' => $storedMedia['disk'] ?? null,
            'media_path' => $storedMedia['path'] ?? null,
            'media_mime' => $storedMedia['mime'] ?? ($media['mime_type'] ?? null),
            'media_filename' => $storedMedia['filename'] ?? ($media['filename'] ?? null),
            'media_size' => $storedMedia['size'] ?? null,
            'metadata' => $metadata !== [] ? $metadata : null,
            'status' => WhatsappMessage::STATUS_RECEIVED,
            'read_at' => $adminActivelyViewingThisChat ? now() : null,
        ]);

        $nextUnreadCount = $adminActivelyViewingThisChat ? 0 : ($session->unread_count + 1);

        $session->forceFill([
            'unread_count' => $nextUnreadCount,
            'last_external_message_id' => $messageId !== '' ? Str::limit($messageId, 135, '') : $session->last_external_message_id,
            'last_message_preview' => $inboundRecord->previewLabel(),
            'last_customer_message_at' => now(),
            'last_message_at' => now(),
        ])->save();

        // Human Takeover mode: record customer message in SDK transcript without AI auto-reply
        if ($session->ai_paused) {
            $conversationId = $this->ensureConversationExists($session);
            resolve(ConversationStore::class)->storeUserMessage(
                $conversationId,
                Conversation::participantType($session),
                Conversation::participantKey($session),
                CustomerSupportAgent::class,
                new UserMessage($text),
            );

            $session->forceFill([
                'conversation_id' => $conversationId,
                'message_count' => $session->message_count + 1,
            ])->save();

            if (! $adminActivelyViewingThisChat) {
                $this->scheduleOrSendUnreadAlert($session->refresh());
            }

            return $session->refresh();
        }

        // AI Auto-Reply Mode
        $agent = new CustomerSupportAgent($session);
        $rawProvider = $agent->provider();
        $primaryProvider = is_array($rawProvider) ? (string) array_key_first($rawProvider) : $rawProvider;
        $provider = $this->guard->resolvedProvider($primaryProvider);

        $replyText = null;
        $sdkAttachments = $this->buildSdkAttachmentsForInbound($storedMedia, $resolvedType);

        if ($this->guard->isReady($provider, CustomerSupportAgent::isFaked())) {
            app(CustomerSupportChatService::class)->markTyping($session, 'ai', true, null, 35);
            try {
                $response = $agent
                    ->continueOrStart($session->conversation_id, as: $session)
                    ->prompt($text, attachments: $sdkAttachments);

                $session->refresh();
                $session->forceFill([
                    'conversation_id' => $response->conversationId ?? $session->conversation_id,
                    'message_count' => $session->message_count + 1,
                    'last_message_at' => now(),
                ])->save();

                $replyText = $this->whatsapp->formatForWhatsapp($response->text);
            } catch (Throwable $exception) {
                Log::warning('WhatsApp AI generation failed', ['error' => $exception->getMessage()]);
            } finally {
                app(CustomerSupportChatService::class)->markTyping($session, 'ai', false);
            }
        }

        if ($replyText === null || $replyText === '') {
            $conversationId = $this->ensureConversationExists($session);
            resolve(ConversationStore::class)->storeUserMessage(
                $conversationId,
                Conversation::participantType($session),
                Conversation::participantKey($session),
                CustomerSupportAgent::class,
                new UserMessage($text),
            );

            $email = Setting::getValue('email', 'hello@empetix.com') ?: 'hello@empetix.com';
            $replyText = "Thank you for messaging Empetix Solutions! Our team has received your message and will reply shortly, or you can reach us anytime at {$email}.";

            $this->appendManualAssistantMessage($conversationId, $session, $replyText, 'fallback');

            $session->forceFill([
                'conversation_id' => $conversationId,
                'message_count' => $session->message_count + 1,
                'last_message_at' => now(),
            ])->save();
        }

        $outboundWamid = null;
        $outboundStatus = WhatsappMessage::STATUS_SENT;

        if ($this->whatsapp->isConfigured()) {
            try {
                $apiRes = $this->whatsapp->sendTextMessage($normalizedWaId, $replyText);
                $outboundWamid = $apiRes['messages'][0]['id'] ?? null;
            } catch (Throwable $exception) {
                $outboundStatus = WhatsappMessage::STATUS_FAILED;
                Log::error('WhatsApp outbound reply failed', [
                    'to' => $normalizedWaId,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        WhatsappMessage::query()->create([
            'support_chat_session_id' => $session->id,
            'wa_message_id' => is_string($outboundWamid) ? $outboundWamid : null,
            'direction' => WhatsappMessage::DIRECTION_OUTBOUND,
            'sender_type' => WhatsappMessage::SENDER_AI,
            'type' => 'text',
            'body' => $replyText,
            'status' => $outboundStatus,
        ]);

        $session->forceFill([
            'last_message_preview' => Str::limit($replyText, 90),
            'last_message_at' => now(),
        ])->save();

        if (! $adminActivelyViewingThisChat) {
            $this->scheduleOrSendUnreadAlert($session->refresh());
        }

        return $session->refresh();
    }

    /**
     * Update outbound message delivery/read checkmarks from Meta's `statuses` webhook.
     *
     * @param  list<array{wamid: string, status: string, recipient_id: string, error: ?string}>  $updates
     */
    public function handleStatusUpdates(array $updates): int
    {
        $updated = 0;

        foreach ($updates as $item) {
            $matches = WhatsappMessage::query()->where('wa_message_id', $item['wamid'])->get();
            foreach ($matches as $msg) {
                $rank = [
                    WhatsappMessage::STATUS_SENDING => 0,
                    WhatsappMessage::STATUS_SENT => 1,
                    WhatsappMessage::STATUS_DELIVERED => 2,
                    WhatsappMessage::STATUS_READ => 3,
                    WhatsappMessage::STATUS_FAILED => 4,
                ];

                $currentRank = $rank[$msg->status] ?? 0;
                $nextRank = $rank[$item['status']] ?? 0;

                if ($nextRank < $currentRank && $item['status'] !== WhatsappMessage::STATUS_FAILED) {
                    continue;
                }

                $attrs = ['status' => $item['status']];
                if ($item['status'] === WhatsappMessage::STATUS_DELIVERED && ! $msg->delivered_at) {
                    $attrs['delivered_at'] = now();
                }
                if ($item['status'] === WhatsappMessage::STATUS_READ) {
                    $attrs['delivered_at'] = $msg->delivered_at ?? now();
                    $attrs['read_at'] = now();
                }
                if ($item['error']) {
                    $meta = is_array($msg->metadata) ? $msg->metadata : [];
                    $meta['error'] = $item['error'];
                    $attrs['metadata'] = $meta;
                }

                $msg->forceFill($attrs)->save();
                $updated++;
            }
        }

        return $updated;
    }

    /**
     * Send a manual human text reply from the Admin Dashboard to a WhatsApp thread.
     *
     * @return array<string, mixed>
     */
    public function sendHumanReply(
        SupportChatSession $session,
        string $message,
        bool $pauseAi = true,
        ?User $sender = null,
        ?string $replyToWamid = null,
    ): array {
        app(CustomerSupportChatService::class)->markTyping($session, 'admin', false);

        $recipient = $session->external_thread_id ?: $session->visitor_phone;
        $trimmed = trim($message);

        $apiResponse = $this->whatsapp->sendTextMessage(
            to: (string) $recipient,
            body: $trimmed,
            previewUrl: false,
            replyToWamid: $replyToWamid,
        );

        $wamid = $apiResponse['messages'][0]['id'] ?? null;

        $conversationId = $this->ensureConversationExists($session);
        $this->appendManualAssistantMessage($conversationId, $session, $trimmed, 'human_agent');

        $quoted = $this->lookupQuotedPreview($session->id, $replyToWamid);

        $waMessage = WhatsappMessage::query()->create([
            'support_chat_session_id' => $session->id,
            'wa_message_id' => is_string($wamid) ? $wamid : null,
            'reply_to_wa_message_id' => $replyToWamid ?: null,
            'direction' => WhatsappMessage::DIRECTION_OUTBOUND,
            'sender_type' => WhatsappMessage::SENDER_ADMIN,
            'sender_user_id' => $sender?->id,
            'type' => 'text',
            'body' => $trimmed,
            'metadata' => $quoted ? ['quoted_preview' => $quoted] : null,
            'status' => WhatsappMessage::STATUS_SENT,
        ]);

        $session->forceFill([
            'conversation_id' => $conversationId,
            'ai_paused' => $pauseAi,
            'unread_count' => 0,
            'last_admin_read_at' => now(),
            'last_message_preview' => $waMessage->previewLabel(),
            'last_message_at' => now(),
        ])->save();

        $this->markSessionReadByAdmin($session, sendWhatsappReceipt: false);

        return $apiResponse;
    }

    /**
     * Upload & send a rich media attachment or recorded voice note from the WhatsApp Inbox.
     */
    public function sendHumanMediaReply(
        SupportChatSession $session,
        UploadedFile $file,
        ?string $caption = null,
        bool $isVoice = false,
        ?int $duration = null,
        bool $pauseAi = true,
        ?User $sender = null,
        ?string $replyToWamid = null,
    ): WhatsappMessage {
        app(CustomerSupportChatService::class)->markTyping($session, 'admin', false);

        $originalName = $file->getClientOriginalName() ?: ($isVoice ? 'voice-note.ogg' : 'attachment');
        $rawMime = (string) ($file->getMimeType() ?: $file->getClientMimeType() ?: 'application/octet-stream');
        $ext = strtolower($file->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION));
        $size = (int) $file->getSize();

        $safeBase = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME)) ?: 'media';
        $storedFilename = substr((string) Str::uuid(), 0, 8).'_'.Str::limit($safeBase, 48, '').($ext !== '' ? '.'.$ext : '');
        $directory = self::MEDIA_DIR.'/'.$session->id;
        $storedPath = $file->storeAs($directory, $storedFilename, self::MEDIA_DISK);

        $absolutePath = Storage::disk(self::MEDIA_DISK)->path((string) $storedPath);

        $uploaded = $this->whatsapp->uploadMedia($absolutePath, $rawMime, $originalName, isVoice: $isVoice);

        if ($uploaded['converted_bytes'] !== null) {
            $storedFilename = pathinfo($storedFilename, PATHINFO_FILENAME).'.ogg';
            $newPath = $directory.'/'.$storedFilename;
            Storage::disk(self::MEDIA_DISK)->delete((string) $storedPath);
            $this->mediaCrypto->putEncrypted(self::MEDIA_DISK, $newPath, $uploaded['converted_bytes']);
            $storedPath = $newPath;
            $size = strlen($uploaded['converted_bytes']);
        } else {
            $plainBytes = (string) file_get_contents($absolutePath);
            $this->mediaCrypto->putEncrypted(self::MEDIA_DISK, (string) $storedPath, $plainBytes);
        }

        $mediaType = $this->resolveWhatsappMediaType($uploaded['mime_type'], $ext, $isVoice);
        $recipient = (string) ($session->external_thread_id ?: $session->visitor_phone);

        $apiResponse = $this->whatsapp->sendMediaMessage(
            to: $recipient,
            type: $mediaType,
            mediaId: $uploaded['media_id'],
            caption: $caption,
            filename: $originalName,
            replyToWamid: $replyToWamid,
            isVoice: $isVoice,
            uploadedMime: $uploaded['mime_type'],
        );

        $wamid = $apiResponse['messages'][0]['id'] ?? null;

        $conversationId = $this->ensureConversationExists($session);
        $transcriptNote = filled($caption) ? trim((string) $caption) : "[Sent {$mediaType}: {$originalName}]";
        $this->appendManualAssistantMessage($conversationId, $session, $transcriptNote, 'human_agent');

        $quoted = $this->lookupQuotedPreview($session->id, $replyToWamid);

        $waMessage = WhatsappMessage::query()->create([
            'support_chat_session_id' => $session->id,
            'wa_message_id' => is_string($wamid) ? $wamid : null,
            'reply_to_wa_message_id' => $replyToWamid ?: null,
            'direction' => WhatsappMessage::DIRECTION_OUTBOUND,
            'sender_type' => WhatsappMessage::SENDER_ADMIN,
            'sender_user_id' => $sender?->id,
            'type' => $isVoice ? 'voice' : $mediaType,
            'body' => filled($caption) ? trim((string) $caption) : null,
            'media_id' => $uploaded['media_id'],
            'media_disk' => self::MEDIA_DISK,
            'media_path' => $storedPath,
            'media_mime' => $uploaded['mime_type'],
            'media_filename' => $originalName,
            'media_size' => $size,
            'media_duration' => $duration,
            'metadata' => $quoted ? ['quoted_preview' => $quoted] : null,
            'status' => WhatsappMessage::STATUS_SENT,
        ]);

        $session->forceFill([
            'conversation_id' => $conversationId,
            'ai_paused' => $pauseAi,
            'unread_count' => 0,
            'message_count' => $session->message_count + 1,
            'last_admin_read_at' => now(),
            'last_message_preview' => $waMessage->previewLabel(),
            'last_message_at' => now(),
        ])->save();

        return $waMessage;
    }

    /**
     * Send a location pin from the WhatsApp Inbox.
     */
    public function sendHumanLocationReply(
        SupportChatSession $session,
        float $latitude,
        float $longitude,
        ?string $name = null,
        ?string $address = null,
        bool $pauseAi = true,
        ?User $sender = null,
        ?string $replyToWamid = null,
    ): WhatsappMessage {
        $recipient = (string) ($session->external_thread_id ?: $session->visitor_phone);

        $apiResponse = $this->whatsapp->sendLocationMessage(
            to: $recipient,
            latitude: $latitude,
            longitude: $longitude,
            name: $name,
            address: $address,
            replyToWamid: $replyToWamid,
        );

        $wamid = $apiResponse['messages'][0]['id'] ?? null;

        $waMessage = WhatsappMessage::query()->create([
            'support_chat_session_id' => $session->id,
            'wa_message_id' => is_string($wamid) ? $wamid : null,
            'reply_to_wa_message_id' => $replyToWamid ?: null,
            'direction' => WhatsappMessage::DIRECTION_OUTBOUND,
            'sender_type' => WhatsappMessage::SENDER_ADMIN,
            'sender_user_id' => $sender?->id,
            'type' => 'location',
            'body' => $name ?: $address,
            'metadata' => [
                'location' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'name' => $name,
                    'address' => $address,
                ],
            ],
            'status' => WhatsappMessage::STATUS_SENT,
        ]);

        $session->forceFill([
            'ai_paused' => $pauseAi,
            'unread_count' => 0,
            'message_count' => $session->message_count + 1,
            'last_admin_read_at' => now(),
            'last_message_preview' => $waMessage->previewLabel(),
            'last_message_at' => now(),
        ])->save();

        return $waMessage;
    }

    /**
     * Queue an outbound message in `sending` (`🕒`) state with strict idempotency
     * protection, and dispatch the Meta Cloud API call immediately after the HTTP
     * response flushes to the browser (`dispatchAfterResponse`) for zero UI delay.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function queueInstantOutboundMessage(
        SupportChatSession $session,
        array $attributes,
        string $transcriptNote,
        bool $pauseAi = true,
        ?string $idempotencyKey = null,
    ): WhatsappMessage {
        app(CustomerSupportChatService::class)->markTyping($session, 'admin', false);

        if ($idempotencyKey !== null && $idempotencyKey !== '') {
            $existing = WhatsappMessage::query()
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        $conversationId = $this->ensureConversationExists($session);
        $this->appendManualAssistantMessage($conversationId, $session, $transcriptNote, 'human_agent');

        $waMessage = WhatsappMessage::query()->create([
            ...$attributes,
            'support_chat_session_id' => $session->id,
            'idempotency_key' => $idempotencyKey ?: null,
            'direction' => WhatsappMessage::DIRECTION_OUTBOUND,
            'sender_type' => WhatsappMessage::SENDER_ADMIN,
            'status' => WhatsappMessage::STATUS_SENDING,
        ]);

        $session->forceFill([
            'conversation_id' => $conversationId,
            'ai_paused' => $pauseAi,
            'unread_count' => 0,
            'message_count' => $session->message_count + 1,
            'last_admin_read_at' => now(),
            'last_message_preview' => $waMessage->previewLabel(),
            'last_message_at' => now(),
        ])->save();

        $this->markSessionReadByAdmin($session, sendWhatsappReceipt: false);

        DispatchOutboundWhatsappMessageJob::dispatchAfterResponse($waMessage->id);

        return $waMessage;
    }

    /**
     * Store an uploaded file/voice note encrypted at rest and immediately queue its
     * outbound `sending` (`🕒`) message for post-response Meta Cloud API delivery.
     */
    public function queueInstantMediaReply(
        SupportChatSession $session,
        UploadedFile $file,
        ?string $caption = null,
        bool $isVoice = false,
        ?int $duration = null,
        bool $pauseAi = true,
        ?User $sender = null,
        ?string $replyToWamid = null,
        ?string $idempotencyKey = null,
    ): WhatsappMessage {
        if ($idempotencyKey !== null && $idempotencyKey !== '') {
            $existing = WhatsappMessage::query()->where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing;
            }
        }

        $originalName = $file->getClientOriginalName() ?: ($isVoice ? 'voice-note.ogg' : 'attachment');
        $rawMime = strtolower(trim(explode(';', (string) ($file->getMimeType() ?: $file->getClientMimeType() ?: 'application/octet-stream'))[0]));
        $ext = strtolower($file->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION));
        $plainBytes = (string) $file->get();
        $size = strlen($plainBytes);

        // Pre-convert WebM Opus voice notes locally right away if ffmpeg isn't needed so browser playback is also clean Ogg
        if ($isVoice && str_starts_with(substr($plainBytes, 0, 4), "\x1A\x45\xDF\xA3")) {
            $oggBytes = WebmOpusToOggConverter::convertBytes($plainBytes);
            if ($oggBytes !== null) {
                $plainBytes = $oggBytes;
                $rawMime = 'audio/ogg';
                $ext = 'ogg';
                $originalName = pathinfo($originalName, PATHINFO_FILENAME).'.ogg';
                $size = strlen($plainBytes);
            }
        }

        $safeBase = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME)) ?: 'media';
        $storedFilename = substr((string) Str::uuid(), 0, 8).'_'.Str::limit($safeBase, 48, '').($ext !== '' ? '.'.$ext : '');
        $relativePath = self::MEDIA_DIR.'/'.$session->id.'/'.$storedFilename;

        $this->mediaCrypto->putEncrypted(self::MEDIA_DISK, $relativePath, $plainBytes);

        $mediaType = $this->resolveWhatsappMediaType($rawMime, $ext, $isVoice);
        $quoted = $this->lookupQuotedPreview($session->id, $replyToWamid);
        $transcriptNote = filled($caption) ? trim((string) $caption) : "[Sent {$mediaType}: {$originalName}]";

        return $this->queueInstantOutboundMessage(
            session: $session,
            attributes: [
                'reply_to_wa_message_id' => $replyToWamid ?: null,
                'sender_user_id' => $sender?->id,
                'type' => $isVoice ? 'voice' : $mediaType,
                'body' => filled($caption) ? trim((string) $caption) : null,
                'media_disk' => self::MEDIA_DISK,
                'media_path' => $relativePath,
                'media_mime' => $rawMime,
                'media_filename' => $originalName,
                'media_size' => $size,
                'media_duration' => $duration,
                'metadata' => $quoted ? ['quoted_preview' => $quoted] : null,
            ],
            transcriptNote: $transcriptNote,
            pauseAi: $pauseAi,
            idempotencyKey: $idempotencyKey,
        );
    }

    /**
     * Deliver a pending `sending` (`🕒`) outbound message to Meta Cloud API and
     * transition its status to `sent` (`✓`) or `failed` (`⚠`).
     */
    public function deliverPendingOutboundMessage(WhatsappMessage $message): void
    {
        if ($message->status !== WhatsappMessage::STATUS_SENDING) {
            return;
        }

        $session = $message->session;
        if (! $session) {
            return;
        }

        $recipient = (string) ($session->external_thread_id ?: $session->visitor_phone);
        $meta = is_array($message->metadata) ? $message->metadata : [];

        try {
            if ($message->type === 'text') {
                $apiRes = $this->whatsapp->sendTextMessage(
                    to: $recipient,
                    body: (string) ($message->body ?? ''),
                    previewUrl: false,
                    replyToWamid: $message->reply_to_wa_message_id,
                );
                $wamid = $apiRes['messages'][0]['id'] ?? null;
                $message->forceFill([
                    'wa_message_id' => is_string($wamid) ? $wamid : null,
                    'status' => WhatsappMessage::STATUS_SENT,
                ])->save();

                return;
            }

            if ($message->type === 'template') {
                $tplName = (string) ($meta['template_name'] ?? 'hello_world');
                $tplLang = (string) ($meta['language'] ?? 'en_US');
                $apiRes = $this->whatsapp->sendTemplateMessage($recipient, $tplName, $tplLang);
                $wamid = $apiRes['messages'][0]['id'] ?? null;
                $message->forceFill([
                    'wa_message_id' => is_string($wamid) ? $wamid : null,
                    'status' => WhatsappMessage::STATUS_SENT,
                ])->save();

                return;
            }

            if ($message->type === 'location') {
                $loc = (array) ($meta['location'] ?? []);
                $apiRes = $this->whatsapp->sendLocationMessage(
                    to: $recipient,
                    latitude: (float) ($loc['latitude'] ?? 0),
                    longitude: (float) ($loc['longitude'] ?? 0),
                    name: $loc['name'] ?? null,
                    address: $loc['address'] ?? null,
                    replyToWamid: $message->reply_to_wa_message_id,
                );
                $wamid = $apiRes['messages'][0]['id'] ?? null;
                $message->forceFill([
                    'wa_message_id' => is_string($wamid) ? $wamid : null,
                    'status' => WhatsappMessage::STATUS_SENT,
                ])->save();

                return;
            }

            // Media / Voice note delivery
            $disk = $message->media_disk ?: self::MEDIA_DISK;
            $plainBytes = $message->media_path
                ? $this->mediaCrypto->getDecrypted($disk, $message->media_path)
                : null;

            if ($plainBytes === null || $plainBytes === '') {
                throw new \RuntimeException('Stored media payload could not be read for upload.');
            }

            $isVoice = $message->type === 'voice';
            $ext = pathinfo((string) ($message->media_filename ?: 'media'), PATHINFO_EXTENSION) ?: ($isVoice ? 'ogg' : 'bin');
            $tempFile = sys_get_temp_dir().DIRECTORY_SEPARATOR.'wa_out_'.Str::uuid().'.'.$ext;
            file_put_contents($tempFile, $plainBytes);

            try {
                $uploaded = $this->whatsapp->uploadMedia(
                    absolutePath: $tempFile,
                    mimeType: (string) ($message->media_mime ?: 'application/octet-stream'),
                    filename: (string) ($message->media_filename ?: ('media.'.$ext)),
                    isVoice: $isVoice,
                );
            } finally {
                if (is_file($tempFile)) {
                    @unlink($tempFile);
                }
            }

            // Persist transcoded Ogg bytes back to encrypted storage if ffmpeg converted the file
            $updatedPath = $message->media_path;
            $updatedSize = $message->media_size;
            if ($uploaded['converted_bytes'] !== null && $message->media_path) {
                $newPath = preg_replace('/\.[A-Za-z0-9]+$/', '.ogg', $message->media_path) ?: ($message->media_path.'.ogg');
                if ($newPath !== $message->media_path) {
                    Storage::disk($disk)->delete($message->media_path);
                }
                $this->mediaCrypto->putEncrypted($disk, $newPath, $uploaded['converted_bytes']);
                $updatedPath = $newPath;
                $updatedSize = strlen($uploaded['converted_bytes']);
            }

            $waMediaType = $isVoice ? 'audio' : $message->type;
            $apiRes = $this->whatsapp->sendMediaMessage(
                to: $recipient,
                type: $waMediaType,
                mediaId: $uploaded['media_id'],
                caption: $message->body,
                filename: $message->media_filename,
                replyToWamid: $message->reply_to_wa_message_id,
                isVoice: $isVoice,
                uploadedMime: $uploaded['mime_type'],
            );

            $wamid = $apiRes['messages'][0]['id'] ?? null;
            $message->forceFill([
                'wa_message_id' => is_string($wamid) ? $wamid : null,
                'media_id' => $uploaded['media_id'],
                'media_path' => $updatedPath,
                'media_mime' => $uploaded['mime_type'],
                'media_size' => $updatedSize,
                'status' => WhatsappMessage::STATUS_SENT,
            ])->save();
        } catch (Throwable $exception) {
            $meta['error'] = $exception->getMessage();
            $message->forceFill([
                'status' => WhatsappMessage::STATUS_FAILED,
                'metadata' => $meta,
            ])->save();

            Log::warning('Async outbound WhatsApp dispatch failed', [
                'message_id' => $message->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Send an emoji reaction on a customer or team message.
     */
    public function sendHumanReaction(
        SupportChatSession $session,
        WhatsappMessage $targetMessage,
        string $emoji,
    ): WhatsappMessage {
        if (filled($targetMessage->wa_message_id) && $this->whatsapp->isConfigured()) {
            $recipient = (string) ($session->external_thread_id ?: $session->visitor_phone);
            $this->whatsapp->sendReaction($recipient, (string) $targetMessage->wa_message_id, $emoji);
        }

        $targetMessage->forceFill([
            'reaction_emoji' => $emoji !== '' ? $emoji : null,
        ])->save();

        return $targetMessage->refresh();
    }

    /**
     * Initiate or continue an outbound WhatsApp message (text or Meta template) to any phone number.
     */
    public function sendOutboundCustomerMessage(
        string $phone,
        ?string $customerName,
        string $mode,
        string $content,
        string $templateLang = 'en_US',
        ?User $sender = null,
    ): SupportChatSession {
        $normalized = $this->whatsapp->normalizePhone($phone);

        if ($mode === 'template') {
            $apiRes = $this->whatsapp->sendTemplateMessage($normalized, $content, $templateLang);
            $transcriptNote = "[Sent WhatsApp Template: {$content} ({$templateLang})]";
            $msgType = 'template';
            $meta = ['template_name' => $content, 'language' => $templateLang];
        } else {
            $apiRes = $this->whatsapp->sendTextMessage($normalized, $content);
            $transcriptNote = trim($content);
            $msgType = 'text';
            $meta = null;
        }

        $wamid = $apiRes['messages'][0]['id'] ?? null;

        $session = $this->findOrCreateWhatsappSession($normalized, $customerName);
        $this->syncLegacyMessagesIfNeeded($session);

        $conversationId = $this->ensureConversationExists($session);
        $this->appendManualAssistantMessage($conversationId, $session, $transcriptNote, 'human_agent');

        $waMessage = WhatsappMessage::query()->create([
            'support_chat_session_id' => $session->id,
            'wa_message_id' => is_string($wamid) ? $wamid : null,
            'direction' => WhatsappMessage::DIRECTION_OUTBOUND,
            'sender_type' => WhatsappMessage::SENDER_ADMIN,
            'sender_user_id' => $sender?->id,
            'type' => $msgType,
            'body' => $transcriptNote,
            'metadata' => $meta,
            'status' => WhatsappMessage::STATUS_SENT,
        ]);

        $session->forceFill([
            'conversation_id' => $conversationId,
            'message_count' => $session->message_count + 1,
            'last_message_preview' => $waMessage->previewLabel(),
            'last_message_at' => now(),
        ])->save();

        return $session->refresh();
    }

    /**
     * Mark all inbound messages in a WhatsApp thread as viewed by the admin.
     */
    public function markSessionReadByAdmin(SupportChatSession $session, bool $sendWhatsappReceipt = true): void
    {
        $lastUnreadInbound = WhatsappMessage::query()
            ->where('support_chat_session_id', $session->id)
            ->unreadByAdmin()
            ->latest('id')
            ->first();

        WhatsappMessage::query()
            ->where('support_chat_session_id', $session->id)
            ->unreadByAdmin()
            ->update(['read_at' => now()]);

        if ($session->unread_count > 0 || $session->last_admin_read_at === null) {
            $session->forceFill([
                'unread_count' => 0,
                'last_admin_read_at' => now(),
            ])->save();
        }

        if ($sendWhatsappReceipt && $lastUnreadInbound && filled($lastUnreadInbound->wa_message_id)) {
            $this->whatsapp->markMessageAsRead((string) $lastUnreadInbound->wa_message_id);
        }
    }

    /**
     * Decide whether to send an unread WhatsApp email notification immediately (when
     * no admin has the WhatsApp workspace open) or schedule it after the grace delay.
     */
    public function scheduleOrSendUnreadAlert(SupportChatSession $session): void
    {
        if (! $this->isUnreadEmailEnabled()) {
            return;
        }

        $delaySeconds = $this->unreadDelaySeconds();
        $anyAdminOnline = Cache::has('whatsapp:admin_online_any');

        // If no admin has the WhatsApp Inbox open at all (or delay is 0), notify immediately (with 60s anti-spam cooldown per chat)
        if (! $anyAdminOnline || $delaySeconds <= 0) {
            if ($session->last_unread_notified_at && $session->last_unread_notified_at->gt(now()->subSeconds(60))) {
                return;
            }

            $this->notifyUnreadForSession($session->id);

            return;
        }

        // An admin is currently online in the WhatsApp Inbox (looking at another contact): give them the grace window first
        try {
            SendUnreadWhatsappAlertJob::dispatch($session->id)->delay(now()->addSeconds($delaySeconds));
        } catch (Throwable) {
        }
    }

    /**
     * Send an email digest for any still-unseen inbound messages on a specific session.
     */
    public function notifyUnreadForSession(int $sessionId): bool
    {
        if (! $this->isUnreadEmailEnabled()) {
            return false;
        }

        $session = SupportChatSession::query()->find($sessionId);
        if (! $session || ! $session->isWhatsapp()) {
            return false;
        }

        if (Cache::has("whatsapp:admin_viewing:{$session->id}")) {
            $this->markSessionReadByAdmin($session, sendWhatsappReceipt: false);

            return false;
        }

        $unreadMessages = WhatsappMessage::query()
            ->where('support_chat_session_id', $session->id)
            ->where('direction', WhatsappMessage::DIRECTION_INBOUND)
            ->whereNull('read_at')
            ->whereNull('notified_email_at')
            ->orderBy('id')
            ->get();

        if ($unreadMessages->isEmpty()) {
            return false;
        }

        $recipients = $this->resolveNotificationRecipients();
        if ($recipients === []) {
            return false;
        }

        $inboxUrl = url('/admin/whatsapp?chat='.$session->id);

        try {
            Mail::to($recipients)->send(new UnreadWhatsappMessageMail(
                session: $session,
                unreadMessages: $unreadMessages,
                inboxUrl: $inboxUrl,
            ));

            WhatsappMessage::query()
                ->whereIn('id', $unreadMessages->pluck('id'))
                ->update(['notified_email_at' => now()]);

            $session->forceFill(['last_unread_notified_at' => now()])->save();

            return true;
        } catch (Throwable $exception) {
            Log::warning('Failed to send unread WhatsApp email notification', [
                'session_id' => $session->id,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Sweep all WhatsApp sessions that have unseen messages older than the grace delay.
     */
    public function dispatchOverdueUnreadEmailAlerts(): int
    {
        if (! $this->isUnreadEmailEnabled()) {
            return 0;
        }

        $cutoff = now()->subSeconds(max(10, $this->unreadDelaySeconds()));

        $sessionIds = WhatsappMessage::query()
            ->where('direction', WhatsappMessage::DIRECTION_INBOUND)
            ->whereNull('read_at')
            ->whereNull('notified_email_at')
            ->where('created_at', '<=', $cutoff)
            ->distinct()
            ->pluck('support_chat_session_id');

        $sent = 0;
        foreach ($sessionIds as $sessionId) {
            if ($this->notifyUnreadForSession((int) $sessionId)) {
                $sent++;
            }
        }

        return $sent;
    }

    /**
     * Resolve target email addresses for unread WhatsApp alerts.
     *
     * @return list<string>
     */
    public function resolveNotificationRecipients(): array
    {
        $configured = trim((string) (
            Setting::getValue('whatsapp_notify_email')
            ?: config('assistant.whatsapp.notify_email_to')
        ));

        if ($configured !== '') {
            $emails = array_values(array_filter(
                array_map('trim', preg_split('/[,;\s]+/', $configured) ?: []),
                fn (string $e): bool => filter_var($e, FILTER_VALIDATE_EMAIL) !== false,
            ));

            if ($emails !== []) {
                return $emails;
            }
        }

        $ownerEmails = User::role('owner')
            ->pluck('email')
            ->filter(fn ($e): bool => is_string($e) && filter_var($e, FILTER_VALIDATE_EMAIL) !== false)
            ->values()
            ->all();

        if ($ownerEmails !== []) {
            return $ownerEmails;
        }

        $siteEmail = trim((string) Setting::getValue('email', ''));

        return filter_var($siteEmail, FILTER_VALIDATE_EMAIL) ? [$siteEmail] : [];
    }

    public function isUnreadEmailEnabled(): bool
    {
        return (bool) config('assistant.whatsapp.notify_unread_email', true)
            && Setting::getValue('whatsapp_notify_unread_email', '1') !== '0';
    }

    public function unreadDelaySeconds(): int
    {
        $saved = Setting::getValue('whatsapp_unread_delay_seconds');

        return $saved !== null && $saved !== ''
            ? max(0, (int) $saved)
            : max(0, (int) config('assistant.whatsapp.unread_delay_seconds', 90));
    }

    /**
     * Import historical SDK `ConversationMessage` rows into `whatsapp_messages` once
     * for pre-existing WhatsApp threads created before the dedicated table existed.
     */
    public function syncLegacyMessagesIfNeeded(SupportChatSession $session): void
    {
        if (! $session->isWhatsapp() || ! $session->conversation_id) {
            return;
        }

        if (WhatsappMessage::query()->where('support_chat_session_id', $session->id)->exists()) {
            return;
        }

        $legacyRows = ConversationMessage::query()
            ->where('conversation_id', $session->conversation_id)
            ->orderBy('id')
            ->get();

        $lastInboundAt = null;
        $lastPreview = null;

        foreach ($legacyRows as $row) {
            $isCustomer = $row->role === 'user';
            $source = is_array($row->meta) ? ($row->meta['source'] ?? null) : null;

            $created = WhatsappMessage::query()->create([
                'support_chat_session_id' => $session->id,
                'direction' => $isCustomer ? WhatsappMessage::DIRECTION_INBOUND : WhatsappMessage::DIRECTION_OUTBOUND,
                'sender_type' => $isCustomer
                    ? WhatsappMessage::SENDER_CUSTOMER
                    : ($source === 'human_agent' ? WhatsappMessage::SENDER_ADMIN : WhatsappMessage::SENDER_AI),
                'type' => 'text',
                'body' => (string) ($row->content ?? ''),
                'status' => $isCustomer ? WhatsappMessage::STATUS_RECEIVED : WhatsappMessage::STATUS_SENT,
                'read_at' => $row->created_at ?? now(),
                'created_at' => $row->created_at ?? now(),
                'updated_at' => $row->updated_at ?? now(),
            ]);

            if ($isCustomer && $row->created_at) {
                $lastInboundAt = $row->created_at;
            }
            $lastPreview = $created->previewLabel();
        }

        $updates = [];
        if ($lastInboundAt && ! $session->last_customer_message_at) {
            $updates['last_customer_message_at'] = $lastInboundAt;
        }
        if ($lastPreview && ! $session->last_message_preview) {
            $updates['last_message_preview'] = $lastPreview;
        }
        if ($updates !== []) {
            $session->forceFill($updates)->save();
        }
    }

    /**
     * Find the active WhatsApp session for a phone number or open a new one.
     */
    public function findOrCreateWhatsappSession(
        string $normalizedWaId,
        ?string $profileName = null,
        ?string $username = null,
        ?string $avatarUrl = null,
    ): SupportChatSession {
        $session = SupportChatSession::query()
            ->whatsapp()
            ->where('external_thread_id', $normalizedWaId)
            ->latest('id')
            ->first();

        $displayPhone = $this->whatsapp->displayPhone($normalizedWaId);
        $cleanUsername = $username !== null ? ltrim(trim($username), '@') : null;

        if (! $session) {
            return SupportChatSession::query()->create([
                'token' => (string) Str::uuid(),
                'channel' => SupportChatSession::CHANNEL_WHATSAPP,
                'external_thread_id' => $normalizedWaId,
                'visitor_phone' => $displayPhone,
                'visitor_name' => $profileName ?: null,
                'visitor_username' => $cleanUsername ?: null,
                'visitor_avatar' => $avatarUrl ?: null,
                'status' => SupportChatSession::STATUS_OPEN,
                'ai_paused' => false,
                'is_pinned' => false,
                'message_count' => 0,
                'unread_count' => 0,
                'landing_page' => 'WhatsApp Cloud API',
                'last_message_at' => now(),
            ]);
        }

        $updates = [];
        if ($profileName && ! $session->visitor_name) {
            $updates['visitor_name'] = $profileName;
        }
        if ($cleanUsername && $cleanUsername !== $session->visitor_username) {
            $updates['visitor_username'] = $cleanUsername;
        }
        if ($avatarUrl && ! $session->visitor_avatar) {
            $updates['visitor_avatar'] = $avatarUrl;
        }
        if ($session->status === SupportChatSession::STATUS_CLOSED) {
            $updates['status'] = SupportChatSession::STATUS_OPEN;
        }
        if ($updates !== []) {
            $session->forceFill($updates)->save();
        }

        return $session;
    }

    /**
     * Download inbound media from Meta Graph API and save AES-256 encrypted inside `whatsapp-media/{session_id}`.
     *
     * @param  array<string, mixed>|null  $media
     * @return array{disk: string, path: string, mime: string, filename: string, size: int, bytes: string}|null
     */
    protected function downloadAndStoreInboundMedia(SupportChatSession $session, string $type, ?array $media): ?array
    {
        if (! is_array($media) || empty($media['id'])) {
            return null;
        }

        $downloaded = $this->whatsapp->downloadMedia((string) $media['id']);
        if (! $downloaded) {
            return null;
        }

        $mime = strtolower(trim(explode(';', $downloaded['mime_type'])[0]));
        $ext = $this->extensionForMime($mime, $type);
        $rawName = ! empty($media['filename'])
            ? (string) $media['filename']
            : ($type.'_'.now()->format('Ymd_His').'.'.$ext);

        $safeBase = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($rawName, PATHINFO_FILENAME)) ?: $type;
        $storedFilename = substr((string) Str::uuid(), 0, 8).'_'.Str::limit($safeBase, 48, '').'.'.$ext;
        $relativePath = self::MEDIA_DIR.'/'.$session->id.'/'.$storedFilename;

        $this->mediaCrypto->putEncrypted(self::MEDIA_DISK, $relativePath, $downloaded['bytes']);

        return [
            'disk' => self::MEDIA_DISK,
            'path' => $relativePath,
            'mime' => $mime,
            'filename' => $rawName,
            'size' => $downloaded['size'],
            'bytes' => $downloaded['bytes'],
        ];
    }

    /**
     * Build Laravel AI SDK attachments in-memory from decrypted media bytes.
     *
     * @param  array{disk: string, path: string, mime: string, filename: string, size: int, bytes?: string}|null  $storedMedia
     * @return list<Image|Document|Audio>
     */
    protected function buildSdkAttachmentsForInbound(?array $storedMedia, string $resolvedType): array
    {
        if (! $storedMedia) {
            return [];
        }

        $plainBytes = $storedMedia['bytes'] ?? $this->mediaCrypto->getDecrypted($storedMedia['disk'], $storedMedia['path']);
        if ($plainBytes === null || $plainBytes === '') {
            return [];
        }

        $b64 = base64_encode($plainBytes);

        return match (true) {
            $resolvedType === 'image' => [Image::fromBase64($b64, $storedMedia['mime'])->as($storedMedia['filename'])],
            in_array($resolvedType, ['voice', 'audio'], true) => [Audio::fromBase64($b64, $storedMedia['mime'])],
            $storedMedia['mime'] === 'application/pdf' => [Document::fromBase64($b64, $storedMedia['mime'])->as($storedMedia['filename'])],
            default => [],
        };
    }

    /**
     * @return array{sender: string, preview: string}|null
     */
    protected function lookupQuotedPreview(int $sessionId, ?string $replyToWamid): ?array
    {
        if (! filled($replyToWamid)) {
            return null;
        }

        $target = WhatsappMessage::query()
            ->where('support_chat_session_id', $sessionId)
            ->where('wa_message_id', $replyToWamid)
            ->first();

        if (! $target) {
            return null;
        }

        return [
            'wamid' => $replyToWamid,
            'sender' => $target->isInbound() ? 'Customer' : 'Empetix',
            'preview' => $target->previewLabel(),
        ];
    }

    protected function resolveWhatsappMediaType(string $mime, string $ext, bool $isVoice): string
    {
        if ($isVoice) {
            return 'audio';
        }
        if (str_starts_with($mime, 'image/webp') && $ext === 'webp') {
            return 'image';
        }
        if (str_starts_with($mime, 'image/')) {
            return 'image';
        }
        if (str_starts_with($mime, 'video/')) {
            return 'video';
        }
        if (str_starts_with($mime, 'audio/')) {
            return 'audio';
        }

        return 'document';
    }

    protected function extensionForMime(string $mime, string $fallbackType): string
    {
        return match ($mime) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'audio/ogg', 'application/ogg' => 'ogg',
            'audio/mpeg', 'audio/mp3' => 'mp3',
            'audio/mp4', 'audio/aac', 'audio/x-m4a' => 'm4a',
            'video/mp4' => 'mp4',
            'application/pdf' => 'pdf',
            default => $fallbackType === 'image' ? 'jpg' : ($fallbackType === 'audio' ? 'ogg' : 'bin'),
        };
    }

    protected function ensureConversationExists(SupportChatSession $session): string
    {
        if ($session->conversation_id) {
            return $session->conversation_id;
        }

        $title = 'WhatsApp · '.($session->visitor_name ?: $session->visitor_phone ?: $session->id);

        return resolve(ConversationStore::class)->storeConversation(
            Conversation::participantType($session),
            Conversation::participantKey($session),
            $title,
        );
    }

    protected function appendManualAssistantMessage(
        string $conversationId,
        SupportChatSession $session,
        string $content,
        string $source,
    ): void {
        ConversationMessage::query()->create([
            'id' => (string) Str::uuid7(),
            'conversation_id' => $conversationId,
            'participant_type' => Conversation::participantType($session),
            'participant_id' => Conversation::participantKey($session),
            'agent' => CustomerSupportAgent::class,
            'role' => 'assistant',
            'content' => $content,
            'attachments' => [],
            'steps' => [],
            'usage' => [],
            'meta' => ['source' => $source],
            'status' => MessageStatus::Completed,
        ]);
    }
}
