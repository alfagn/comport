<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SupportChatSession;
use App\Models\WhatsappMessage;
use App\Services\CustomerSupportChatService;
use App\Services\WhatsappCloudApiService;
use App\Services\WhatsappCustomerSupportService;
use App\Services\WhatsappMediaCrypto;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Dedicated WhatsApp Business Inbox Studio (/admin/whatsapp).
 *
 * Authentic WhatsApp Web + Enterprise CRM architecture:
 * - Zero-latency optimistic send (`status = sending` clock `🕒` -> `✓` -> `✓✓` -> blue `✓✓`)
 * - Strict per-message idempotency (`X-Idempotency-Key` + unique DB index + burst lock)
 * - Full AES-256 encryption at rest for messages, metadata, notes, and media binaries
 * - Contact profile photos, `@username`, and formatted international phone numbers
 * - Authentic unread-only green badges, bold unread contact names, and green timestamps
 */
class WhatsappInboxController extends Controller
{
    private const AVATAR_PALETTE = [
        '#00A884', '#0284C7', '#7C3AED', '#EA580C', '#DB2777', '#0D9488', '#4F46E5', '#D97706',
    ];

    public function __construct(
        private WhatsappCloudApiService $whatsappApi,
        private WhatsappCustomerSupportService $whatsappSupport,
        private CustomerSupportChatService $supportChats,
        private WhatsappMediaCrypto $mediaCrypto,
    ) {}

    /**
     * Render the full-viewport WhatsApp Business Inbox workspace.
     */
    public function index(Request $request): View
    {
        Cache::put('whatsapp:admin_online_any', true, now()->addSeconds(18));

        $requestedChatId = (int) $request->query('chat', 0);

        $contacts = $this->loadContacts();
        $activeSession = null;

        if ($requestedChatId > 0) {
            $activeSession = $contacts->firstWhere('id', $requestedChatId)
                ?: SupportChatSession::whatsapp()->with('contactMessage')->find($requestedChatId);
        } elseif ($contacts->isNotEmpty()) {
            $activeSession = $contacts->first();
        }

        $initialPayload = null;
        if ($activeSession) {
            $this->whatsappSupport->syncLegacyMessagesIfNeeded($activeSession);
            $this->whatsappSupport->markSessionReadByAdmin($activeSession);
            Cache::put("whatsapp:admin_viewing:{$activeSession->id}", true, now()->addSeconds(18));
            $activeSession->refresh();
            $initialPayload = $this->serializeActiveChat($activeSession);
        }

        $contactsPayload = $this->loadContacts()
            ->map(fn (SupportChatSession $s): array => $this->serializeContact($s))
            ->values()
            ->all();

        return view('admin.whatsapp.index', [
            'whatsappConfigured' => $this->whatsappApi->isConfigured(),
            'whatsappEnabled' => $this->whatsappApi->isEnabled(),
            'contacts' => $contactsPayload,
            'activeChat' => $initialPayload,
            'totalUnread' => (int) SupportChatSession::whatsapp()->where('is_archived', false)->sum('unread_count'),
            'archivedCount' => SupportChatSession::whatsapp()->where('is_archived', true)->count(),
            'archivedUnread' => (int) SupportChatSession::whatsapp()->where('is_archived', true)->sum('unread_count'),
            'notificationSettings' => [
                'enabled' => $this->whatsappSupport->isUnreadEmailEnabled(),
                'email' => Setting::getValue('whatsapp_notify_email')
                    ?: config('assistant.whatsapp.notify_email_to')
                    ?: implode(', ', $this->whatsappSupport->resolveNotificationRecipients()),
                'delay_seconds' => $this->whatsappSupport->unreadDelaySeconds(),
            ],
        ]);
    }

    /**
     * Real-time runtime poller for the WhatsApp workspace: syncs contacts list,
     * unread counters, active chat messages, status checkmarks, and typing indicators.
     */
    public function poll(Request $request): JsonResponse
    {
        Cache::put('whatsapp:admin_online_any', true, now()->addSeconds(18));

        $activeId = (int) $request->query('active_id', 0);
        $filter = (string) $request->query('filter', 'all');
        $search = trim((string) $request->query('q', ''));

        $activePayload = null;

        if ($activeId > 0) {
            $activeSession = SupportChatSession::whatsapp()->with('contactMessage')->find($activeId);
            if ($activeSession) {
                Cache::put("whatsapp:admin_viewing:{$activeSession->id}", true, now()->addSeconds(18));
                if ($activeSession->unread_count > 0) {
                    $this->whatsappSupport->markSessionReadByAdmin($activeSession);
                    $activeSession->refresh();
                }
                $activePayload = $this->serializeActiveChat($activeSession);
            }
        }

        // Trigger email notifications for any unseen messages on OTHER threads past the grace delay
        $this->whatsappSupport->dispatchOverdueUnreadEmailAlerts();

        $contacts = $this->loadContacts($filter, $search, 100)
            ->map(fn (SupportChatSession $s): array => $this->serializeContact($s))
            ->values()
            ->all();

        return response()->json([
            'ok' => true,
            'total_unread' => (int) SupportChatSession::whatsapp()->where('is_archived', false)->sum('unread_count'),
            'archived_count' => SupportChatSession::whatsapp()->where('is_archived', true)->count(),
            'archived_unread' => (int) SupportChatSession::whatsapp()->where('is_archived', true)->sum('unread_count'),
            'total_contacts' => SupportChatSession::whatsapp()->count(),
            'contacts' => $contacts,
            'active_chat' => $activePayload,
        ]);
    }

    /**
     * Load a single WhatsApp conversation, mark all its messages read, and return full details.
     */
    public function show(int $id): JsonResponse
    {
        Cache::put('whatsapp:admin_online_any', true, now()->addSeconds(18));

        $session = SupportChatSession::whatsapp()->with('contactMessage')->findOrFail($id);
        $this->whatsappSupport->syncLegacyMessagesIfNeeded($session);
        $this->whatsappSupport->markSessionReadByAdmin($session);
        Cache::put("whatsapp:admin_viewing:{$session->id}", true, now()->addSeconds(18));

        return response()->json([
            'ok' => true,
            'total_unread' => (int) SupportChatSession::whatsapp()->sum('unread_count'),
            'active_chat' => $this->serializeActiveChat($session->refresh()),
        ]);
    }

    /**
     * Start a new WhatsApp conversation with a phone number (or send an initial text/template).
     */
    public function startConversation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'min:7', 'max:30'],
            'customer_name' => ['nullable', 'string', 'max:120'],
            'username' => ['nullable', 'string', 'max:120'],
            'mode' => ['required', 'in:contact_only,text,template'],
            'content' => ['nullable', 'string', 'max:3500'],
            'template_language' => ['nullable', 'string', 'max:20'],
        ]);

        $normalized = $this->whatsappApi->normalizePhone($validated['phone']);
        if ($normalized === '') {
            return response()->json(['ok' => false, 'message' => 'Invalid phone number.'], 422);
        }

        try {
            if ($validated['mode'] === 'contact_only') {
                $session = $this->whatsappSupport->findOrCreateWhatsappSession(
                    $normalized,
                    $validated['customer_name'] ?? null,
                    $validated['username'] ?? null,
                );
            } else {
                $content = trim((string) ($validated['content'] ?? ''));
                if ($content === '') {
                    return response()->json(['ok' => false, 'message' => 'Message or template name is required.'], 422);
                }

                $session = $this->whatsappSupport->sendOutboundCustomerMessage(
                    phone: $normalized,
                    customerName: $validated['customer_name'] ?? null,
                    mode: $validated['mode'],
                    content: $content,
                    templateLang: $validated['template_language'] ?: 'en_US',
                    sender: $request->user(),
                );

                if (filled($validated['username'] ?? null)) {
                    $session->forceFill(['visitor_username' => ltrim(trim((string) $validated['username']), '@')])->save();
                }
            }
        } catch (RuntimeException $exception) {
            return response()->json([
                'ok' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'contact' => $this->serializeContact($session),
            'active_chat' => $this->serializeActiveChat($session),
            'total_unread' => (int) SupportChatSession::whatsapp()->where('is_archived', false)->sum('unread_count'),
            'archived_count' => SupportChatSession::whatsapp()->where('is_archived', true)->count(),
            'archived_unread' => (int) SupportChatSession::whatsapp()->where('is_archived', true)->sum('unread_count'),
        ]);
    }

    /**
     * Send text, photos, videos, documents, voice notes, locations, or templates inside an open thread.
     *
     * Enforces strict idempotency (`idempotency_key` / `X-Idempotency-Key` + burst guard)
     * and dispatches to Meta Cloud API asynchronously after flushing the HTTP response
     * so messages appear in 0ms with the WhatsApp clock (`🕒`) state.
     */
    public function sendMessage(Request $request, int $id): JsonResponse
    {
        @set_time_limit(120);

        $session = SupportChatSession::whatsapp()->with('contactMessage')->findOrFail($id);

        $validated = $request->validate([
            'mode' => ['nullable', 'in:text,media,voice,location,template'],
            'idempotency_key' => ['nullable', 'string', 'max:100'],
            'message' => ['nullable', 'string', 'max:4000'],
            'reply_to_wamid' => ['nullable', 'string', 'max:190'],
            'pause_ai' => ['nullable', 'boolean'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:25600'],
            'voice_note' => ['nullable', 'file', 'max:16384'],
            'duration' => ['nullable', 'integer', 'min:0', 'max:3600'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'location_name' => ['nullable', 'string', 'max:120'],
            'location_address' => ['nullable', 'string', 'max:240'],
            'template_name' => ['nullable', 'string', 'max:120'],
            'template_language' => ['nullable', 'string', 'max:20'],
        ]);

        $mode = (string) ($validated['mode'] ?? 'text');
        $pauseAi = $request->has('pause_ai') ? $request->boolean('pause_ai') : true;
        $replyToWamid = $validated['reply_to_wamid'] ?? null;
        $captionOrBody = trim((string) ($validated['message'] ?? ''));
        $user = $request->user();

        $rawKey = trim((string) ($request->header('X-Idempotency-Key') ?: ($validated['idempotency_key'] ?? '')));
        $idempotencyKey = (string) Str::limit((string) preg_replace('/[^A-Za-z0-9._:-]/', '', $rawKey), 90, '');

        // 1. Explicit Idempotency Key Guard (DB + Atomic Cache Lock)
        if ($idempotencyKey !== '') {
            $existing = WhatsappMessage::query()
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing || ! Cache::add("whatsapp:idem_lock:{$idempotencyKey}", true, now()->addMinutes(10))) {
                $session->refresh();

                return response()->json([
                    'ok' => true,
                    'duplicate' => true,
                    'contact' => $this->serializeContact($session),
                    'active_chat' => $this->serializeActiveChat($session),
                ]);
            }
        } else {
            // 2. Rapid Double-Click Burst Guard (same user + chat + payload within 1 second)
            $burstFingerprint = hash('sha256', implode('|', [
                $session->id,
                $user?->id ?? 0,
                $mode,
                $captionOrBody,
                $validated['template_name'] ?? '',
                $validated['latitude'] ?? '',
                $validated['longitude'] ?? '',
                $request->hasFile('voice_note') ? ($request->file('voice_note')->getSize() ?: 'v') : '',
                count($request->file('attachments', [])),
                (string) floor(microtime(true)),
            ]));

            if (! Cache::add("whatsapp:burst:{$burstFingerprint}", true, now()->addSeconds(2))) {
                $session->refresh();

                return response()->json([
                    'ok' => true,
                    'duplicate' => true,
                    'contact' => $this->serializeContact($session),
                    'active_chat' => $this->serializeActiveChat($session),
                ]);
            }
        }

        try {
            if ($request->hasFile('voice_note')) {
                $this->whatsappSupport->queueInstantMediaReply(
                    session: $session,
                    file: $request->file('voice_note'),
                    caption: null,
                    isVoice: true,
                    duration: isset($validated['duration']) ? (int) $validated['duration'] : null,
                    pauseAi: $pauseAi,
                    sender: $user,
                    replyToWamid: $replyToWamid,
                    idempotencyKey: $idempotencyKey ?: null,
                );
            } elseif ($request->hasFile('attachments')) {
                $files = $request->file('attachments', []);
                foreach ($files as $idx => $file) {
                    $itemKey = $idempotencyKey !== '' ? ($idx === 0 ? $idempotencyKey : "{$idempotencyKey}_{$idx}") : null;
                    $this->whatsappSupport->queueInstantMediaReply(
                        session: $session,
                        file: $file,
                        caption: $idx === 0 && $captionOrBody !== '' ? $captionOrBody : null,
                        isVoice: false,
                        duration: null,
                        pauseAi: $pauseAi,
                        sender: $user,
                        replyToWamid: $idx === 0 ? $replyToWamid : null,
                        idempotencyKey: $itemKey,
                    );
                }
            } elseif ($mode === 'location' && isset($validated['latitude'], $validated['longitude'])) {
                $lat = (float) $validated['latitude'];
                $lng = (float) $validated['longitude'];
                $locName = $validated['location_name'] ?? null;
                $locAddr = $validated['location_address'] ?? null;

                $this->whatsappSupport->queueInstantOutboundMessage(
                    session: $session,
                    attributes: [
                        'reply_to_wa_message_id' => $replyToWamid ?: null,
                        'sender_user_id' => $user?->id,
                        'type' => 'location',
                        'body' => $locName ?: $locAddr,
                        'metadata' => [
                            'location' => [
                                'latitude' => $lat,
                                'longitude' => $lng,
                                'name' => $locName,
                                'address' => $locAddr,
                            ],
                        ],
                    ],
                    transcriptNote: '[Sent Location: '.($locName ?: "{$lat}, {$lng}").']',
                    pauseAi: $pauseAi,
                    idempotencyKey: $idempotencyKey ?: null,
                );
            } elseif ($mode === 'template' && filled($validated['template_name'] ?? null)) {
                $tplName = trim((string) $validated['template_name']);
                $tplLang = trim((string) ($validated['template_language'] ?: 'en_US'));
                $note = "[Sent WhatsApp Template: {$tplName} ({$tplLang})]";

                $this->whatsappSupport->queueInstantOutboundMessage(
                    session: $session,
                    attributes: [
                        'sender_user_id' => $user?->id,
                        'type' => 'template',
                        'body' => $note,
                        'metadata' => [
                            'template_name' => $tplName,
                            'language' => $tplLang,
                        ],
                    ],
                    transcriptNote: $note,
                    pauseAi: $pauseAi,
                    idempotencyKey: $idempotencyKey ?: null,
                );
            } else {
                if ($captionOrBody === '') {
                    return response()->json([
                        'ok' => false,
                        'message' => 'Please enter a message or attach a file.',
                    ], 422);
                }

                $quoted = null;
                if (filled($replyToWamid)) {
                    $target = WhatsappMessage::query()
                        ->where('support_chat_session_id', $session->id)
                        ->where('wa_message_id', $replyToWamid)
                        ->first();
                    if ($target) {
                        $quoted = [
                            'wamid' => $replyToWamid,
                            'sender' => $target->isInbound() ? 'Customer' : 'Empetix',
                            'preview' => $target->previewLabel(),
                        ];
                    }
                }

                $this->whatsappSupport->queueInstantOutboundMessage(
                    session: $session,
                    attributes: [
                        'reply_to_wa_message_id' => $replyToWamid ?: null,
                        'sender_user_id' => $user?->id,
                        'type' => 'text',
                        'body' => $captionOrBody,
                        'metadata' => $quoted ? ['quoted_preview' => $quoted] : null,
                    ],
                    transcriptNote: $captionOrBody,
                    pauseAi: $pauseAi,
                    idempotencyKey: $idempotencyKey ?: null,
                );
            }
        } catch (RuntimeException $exception) {
            return response()->json([
                'ok' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        $session->refresh();

        return response()->json([
            'ok' => true,
            'contact' => $this->serializeContact($session),
            'active_chat' => $this->serializeActiveChat($session),
            'total_unread' => (int) SupportChatSession::whatsapp()->where('is_archived', false)->sum('unread_count'),
            'archived_count' => SupportChatSession::whatsapp()->where('is_archived', true)->count(),
            'archived_unread' => (int) SupportChatSession::whatsapp()->where('is_archived', true)->sum('unread_count'),
        ]);
    }

    /**
     * React to a message with an emoji (`👍`, `❤️`, `😂`, `😮`, `😢`, `🙏`, or empty to clear).
     */
    public function react(Request $request, int $id, int $messageId): JsonResponse
    {
        $session = SupportChatSession::whatsapp()->findOrFail($id);
        $target = WhatsappMessage::query()
            ->where('support_chat_session_id', $session->id)
            ->findOrFail($messageId);

        $validated = $request->validate([
            'emoji' => ['nullable', 'string', 'max:16'],
        ]);

        try {
            $updated = $this->whatsappSupport->sendHumanReaction(
                $session,
                $target,
                (string) ($validated['emoji'] ?? ''),
            );
        } catch (RuntimeException $exception) {
            return response()->json(['ok' => false, 'message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'ok' => true,
            'message_id' => $updated->id,
            'reaction_emoji' => $updated->reaction_emoji,
        ]);
    }

    /**
     * Update contact CRM metadata, `@username`, profile photo, pin state, AI toggle, or workflow status.
     */
    public function updateContact(Request $request, int $id): JsonResponse
    {
        $session = SupportChatSession::whatsapp()->with('contactMessage')->findOrFail($id);

        $validated = $request->validate([
            'visitor_name' => ['nullable', 'string', 'max:120'],
            'visitor_username' => ['nullable', 'string', 'max:120'],
            'visitor_email' => ['nullable', 'email', 'max:160'],
            'contact_notes' => ['nullable', 'string', 'max:3000'],
            'status' => ['nullable', 'in:open,handed_off,closed'],
            'is_pinned' => ['nullable', 'boolean'],
            'is_archived' => ['nullable', 'boolean'],
            'mark_unread' => ['nullable', 'boolean'],
            'ai_paused' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'image', 'max:5120'],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        $updates = [];
        foreach (['visitor_name', 'visitor_email', 'contact_notes', 'status'] as $field) {
            if ($request->exists($field)) {
                $updates[$field] = $validated[$field] ?? null;
            }
        }

        if ($request->exists('visitor_username')) {
            $cleanUser = ltrim(trim((string) ($validated['visitor_username'] ?? '')), '@');
            $updates['visitor_username'] = $cleanUser !== '' ? $cleanUser : null;
        }

        if ($request->has('is_pinned')) {
            $updates['is_pinned'] = $request->boolean('is_pinned');
        }
        if ($request->has('is_archived')) {
            $updates['is_archived'] = $request->boolean('is_archived');
            if ($updates['is_archived']) {
                $updates['is_pinned'] = false;
            }
        }
        if ($request->has('mark_unread')) {
            if ($request->boolean('mark_unread')) {
                $updates['unread_count'] = max(1, (int) $session->unread_count);
            } else {
                $this->whatsappSupport->markSessionReadByAdmin($session, sendWhatsappReceipt: false);
                $updates['unread_count'] = 0;
            }
        }
        if ($request->has('ai_paused')) {
            $updates['ai_paused'] = $request->boolean('ai_paused');
        }

        if ($request->boolean('remove_avatar')) {
            if ($session->visitor_avatar && str_starts_with($session->visitor_avatar, 'local:')) {
                Storage::disk(WhatsappCustomerSupportService::MEDIA_DISK)
                    ->delete(substr($session->visitor_avatar, 6));
            }
            $updates['visitor_avatar'] = null;
        } elseif ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $mime = strtolower((string) ($file->getMimeType() ?: 'image/jpeg'));
            $avatarPath = WhatsappCustomerSupportService::MEDIA_DIR.'/'.$session->id.'/avatar_'.substr((string) Str::uuid(), 0, 8).'.'.$ext;

            if ($session->visitor_avatar && str_starts_with($session->visitor_avatar, 'local:')) {
                $oldMeta = explode('|', substr($session->visitor_avatar, 6), 2);
                Storage::disk(WhatsappCustomerSupportService::MEDIA_DISK)->delete($oldMeta[0]);
            }

            $this->mediaCrypto->putEncrypted(
                WhatsappCustomerSupportService::MEDIA_DISK,
                $avatarPath,
                (string) $file->get(),
            );

            $updates['visitor_avatar'] = "local:{$avatarPath}|{$mime}";
        }

        if ($updates !== []) {
            $session->forceFill($updates)->save();
        }

        $session->refresh();

        return response()->json([
            'ok' => true,
            'contact' => $this->serializeContact($session),
            'active_chat' => $this->serializeActiveChat($session),
            'total_unread' => (int) SupportChatSession::whatsapp()->where('is_archived', false)->sum('unread_count'),
            'archived_count' => SupportChatSession::whatsapp()->where('is_archived', true)->count(),
            'archived_unread' => (int) SupportChatSession::whatsapp()->where('is_archived', true)->sum('unread_count'),
        ]);
    }

    /**
     * Delete a single message inside a WhatsApp thread (and remove its stored media file if present).
     */
    public function destroyMessage(int $id, int $messageId): JsonResponse
    {
        $session = SupportChatSession::whatsapp()->findOrFail($id);
        $message = WhatsappMessage::query()
            ->where('support_chat_session_id', $session->id)
            ->findOrFail($messageId);

        if ($message->media_path) {
            $disk = $message->media_disk ?: WhatsappCustomerSupportService::MEDIA_DISK;
            Storage::disk($disk)->delete($message->media_path);
        }

        $message->delete();

        $latest = WhatsappMessage::query()
            ->where('support_chat_session_id', $session->id)
            ->latest('id')
            ->first();

        $session->forceFill([
            'last_message_preview' => $latest?->previewLabel(),
        ])->save();

        $session->refresh();

        return response()->json([
            'ok' => true,
            'contact' => $this->serializeContact($session),
            'active_chat' => $this->serializeActiveChat($session),
        ]);
    }

    /**
     * Stream a contact's encrypted profile photo securely.
     */
    public function avatar(int $id): Response
    {
        $session = SupportChatSession::whatsapp()->findOrFail($id);
        $avatar = (string) ($session->visitor_avatar ?? '');

        if ($avatar === '') {
            throw new NotFoundHttpException('Contact avatar not set.');
        }

        if (str_starts_with($avatar, 'https://') || str_starts_with($avatar, 'http://')) {
            return redirect()->away($avatar);
        }

        if (! str_starts_with($avatar, 'local:')) {
            throw new NotFoundHttpException('Invalid avatar reference.');
        }

        [$path, $mime] = array_pad(explode('|', substr($avatar, 6), 2), 2, 'image/jpeg');
        $bytes = $this->mediaCrypto->getDecrypted(WhatsappCustomerSupportService::MEDIA_DISK, $path);

        if ($bytes === null || $bytes === '') {
            throw new NotFoundHttpException('Avatar file missing.');
        }

        return response($bytes, 200, [
            'Content-Type' => $mime ?: 'image/jpeg',
            'Content-Length' => (string) strlen($bytes),
            'Cache-Control' => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Delete a WhatsApp conversation and all its stored media files.
     */
    public function destroy(int $id): JsonResponse
    {
        $session = SupportChatSession::whatsapp()->findOrFail($id);
        $this->supportChats->deleteSession($session);

        return response()->json([
            'ok' => true,
            'total_unread' => (int) SupportChatSession::whatsapp()->sum('unread_count'),
        ]);
    }

    /**
     * Update unread WhatsApp email notification preferences.
     */
    public function updateNotificationSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'enabled' => ['required', 'boolean'],
            'email' => ['nullable', 'string', 'max:250'],
            'delay_seconds' => ['required', 'integer', 'min:0', 'max:3600'],
        ]);

        Setting::setValue('whatsapp_notify_unread_email', $validated['enabled'] ? '1' : '0', 'whatsapp');
        Setting::setValue('whatsapp_notify_email', trim((string) ($validated['email'] ?? '')), 'whatsapp');
        Setting::setValue('whatsapp_unread_delay_seconds', (string) $validated['delay_seconds'], 'whatsapp');

        return response()->json([
            'ok' => true,
            'message' => 'Email notification preferences saved.',
        ]);
    }

    /**
     * Decrypt and stream an authenticated WhatsApp media file (image, voice note, audio, video, document)
     * with full HTTP Range (`206 Partial Content`) support for instant audio/video scrubbing.
     */
    public function media(Request $request, int $messageId): Response
    {
        $message = WhatsappMessage::query()->findOrFail($messageId);

        if (! $message->media_path) {
            throw new NotFoundHttpException('Media not found.');
        }

        $diskName = $message->media_disk ?: WhatsappCustomerSupportService::MEDIA_DISK;
        $bytes = $this->mediaCrypto->getDecrypted($diskName, $message->media_path);

        if ($bytes === null || $bytes === '') {
            throw new NotFoundHttpException('Media file missing on disk.');
        }

        $mime = $message->media_mime ?: 'application/octet-stream';
        $filename = preg_replace('/[^A-Za-z0-9._-]/', '_', (string) ($message->media_filename ?: basename($message->media_path))) ?: 'media';
        $totalLen = strlen($bytes);

        if ($request->boolean('download')) {
            return response($bytes, 200, [
                'Content-Type' => $mime,
                'Content-Length' => (string) $totalLen,
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        $rangeHeader = $request->header('Range');
        if (is_string($rangeHeader) && preg_match('/bytes=(\d*)-(\d*)/i', $rangeHeader, $matches) && $totalLen > 0) {
            $start = $matches[1] !== '' ? (int) $matches[1] : 0;
            $end = $matches[2] !== '' ? min($totalLen - 1, (int) $matches[2]) : ($totalLen - 1);

            if ($start <= $end && $start < $totalLen) {
                $slice = substr($bytes, $start, ($end - $start) + 1);

                return response($slice, 206, [
                    'Content-Type' => $mime,
                    'Content-Length' => (string) strlen($slice),
                    'Content-Range' => "bytes {$start}-{$end}/{$totalLen}",
                    'Accept-Ranges' => 'bytes',
                    'Cache-Control' => 'private, max-age=3600',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            }
        }

        return response($bytes, 200, [
            'Content-Type' => $mime,
            'Content-Length' => (string) $totalLen,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Load contacts with encrypted-aware search & latest message metadata eager-loaded.
     *
     * @return Collection<int, SupportChatSession>
     */
    protected function loadContacts(string $filter = 'all', string $search = '', int $limit = 80): Collection
    {
        $query = SupportChatSession::query()
            ->whatsapp()
            ->with(['whatsappMessages' => fn ($q) => $q->latest('id')->limit($search !== '' ? 20 : 1)])
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_message_at')
            ->orderByDesc('id');

        if ($filter === 'archived') {
            $query->where('is_archived', true);
        } elseif ($search === '') {
            $query->where('is_archived', false);

            if ($filter === 'unread') {
                $query->where('unread_count', '>', 0);
            } elseif ($filter === 'pinned') {
                $query->where('is_pinned', true);
            } elseif ($filter === 'ai') {
                $query->where('ai_paused', false);
            } elseif ($filter === 'human') {
                $query->where('ai_paused', true);
            }
        }

        if ($search === '') {
            return $query->limit($limit)->get();
        }

        $needle = mb_strtolower($search);
        $candidates = $query->limit(250)->get();

        return $candidates
            ->filter(function (SupportChatSession $s) use ($needle): bool {
                $haystack = implode(' ', [
                    (string) $s->visitor_name,
                    (string) $s->visitor_username,
                    (string) $s->visitor_phone,
                    (string) $s->external_thread_id,
                    (string) $s->last_message_preview,
                    (string) $s->contact_notes,
                ]);

                if (str_contains(mb_strtolower($haystack), $needle)) {
                    return true;
                }

                foreach ($s->whatsappMessages as $m) {
                    if (str_contains(mb_strtolower((string) ($m->body ?? '')), $needle)) {
                        return true;
                    }
                }

                return false;
            })
            ->take($limit)
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    protected function serializeContact(SupportChatSession $session): array
    {
        $presence = $this->supportChats->presenceFor($session);
        $isBsuid = $session->external_thread_id && $this->whatsappApi->isBsuid((string) $session->external_thread_id);
        $rawPhone = $session->visitor_phone ?: ((! $isBsuid && $session->external_thread_id) ? '+'.$session->external_thread_id : '');
        $formattedPhone = $rawPhone !== '' ? $this->whatsappApi->formattedPhone($rawPhone) : '';
        $cleanUsername = ltrim(trim((string) ($session->visitor_username ?? '')), '@');
        $displayName = trim((string) ($session->visitor_name ?? '')) !== ''
            ? trim((string) $session->visitor_name)
            : ($cleanUsername !== '' ? '@'.$cleanUsername : ($formattedPhone ?: $session->visitorLabel()));
        $paletteIdx = abs(crc32((string) ($session->external_thread_id ?: $session->id))) % count(self::AVATAR_PALETTE);

        $timeLabel = '';
        if ($session->last_message_at) {
            $timeLabel = $session->last_message_at->isToday()
                ? $session->last_message_at->format('H:i')
                : ($session->last_message_at->isYesterday() ? 'Yesterday' : $session->last_message_at->format('d/m/Y'));
        }

        $latestMsg = $session->relationLoaded('whatsappMessages')
            ? $session->whatsappMessages->first()
            : WhatsappMessage::query()->where('support_chat_session_id', $session->id)->latest('id')->first();

        $totalMsgs = WhatsappMessage::query()->where('support_chat_session_id', $session->id)->count();
        if ($totalMsgs === 0 && $session->message_count > 0) {
            $totalMsgs = $session->message_count;
        }

        $avatarUrl = null;
        if (filled($session->visitor_avatar)) {
            $avatarUrl = str_starts_with((string) $session->visitor_avatar, 'http')
                ? (string) $session->visitor_avatar
                : route('admin.whatsapp.contact.avatar', $session->id).'?v='.substr(md5((string) $session->visitor_avatar), 0, 8);
        }

        return [
            'id' => $session->id,
            'name' => $displayName,
            'visitor_name' => $session->visitor_name ?? '',
            'username' => $cleanUsername !== '' ? '@'.$cleanUsername : '',
            'raw_username' => $cleanUsername,
            'phone' => $formattedPhone ?: $rawPhone,
            'raw_phone' => $rawPhone,
            'email' => $session->visitor_email ?? '',
            'avatar_url' => $avatarUrl,
            'initial' => mb_strtoupper(mb_substr($displayName, 0, 1)) ?: 'W',
            'avatar_color' => self::AVATAR_PALETTE[$paletteIdx],
            'unread_count' => (int) $session->unread_count,
            'total_messages' => $totalMsgs,
            'is_pinned' => (bool) $session->is_pinned,
            'is_archived' => (bool) $session->is_archived,
            'ai_paused' => (bool) $session->ai_paused,
            'status' => $session->status,
            'status_label' => $session->statusLabel(),
            'within_24h' => $session->withinCustomerServiceWindow(),
            'preview' => $session->last_message_preview ?: ($latestMsg?->previewLabel() ?: ($session->handoff_summary ?: 'Start chatting on WhatsApp')),
            'last_direction' => $latestMsg?->direction,
            'last_status' => $latestMsg?->status,
            'time_label' => $timeLabel,
            'ai_typing' => $presence['ai_typing'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function serializeActiveChat(SupportChatSession $session): array
    {
        $messages = WhatsappMessage::query()
            ->with('senderUser:id,name')
            ->where('support_chat_session_id', $session->id)
            ->orderBy('id')
            ->get();

        $serializedMessages = $messages->map(function (WhatsappMessage $msg): array {
            $hasStoredFile = filled($msg->media_path);
            $mediaUrl = $hasStoredFile ? route('admin.whatsapp.media', $msg->id) : null;
            $downloadUrl = $hasStoredFile ? route('admin.whatsapp.media', ['message' => $msg->id, 'download' => 1]) : null;

            $body = (string) ($msg->body ?? '');
            $hideAutoPlaceholder = in_array($msg->type, ['image', 'video', 'voice', 'audio', 'document', 'sticker', 'location'], true)
                && (str_starts_with($body, '[Customer sent ') || str_starts_with($body, '[Customer shared '));

            $displayBody = $hideAutoPlaceholder ? '' : $body;
            $meta = is_array($msg->metadata) ? $msg->metadata : [];

            return [
                'id' => $msg->id,
                'wamid' => $msg->wa_message_id,
                'idempotency_key' => $msg->idempotency_key,
                'reply_to_wamid' => $msg->reply_to_wa_message_id,
                'quoted' => $meta['quoted_preview'] ?? null,
                'direction' => $msg->direction,
                'sender_type' => $msg->sender_type,
                'sender_name' => match ($msg->sender_type) {
                    WhatsappMessage::SENDER_ADMIN => $msg->senderUser?->name ?: 'Team Member',
                    WhatsappMessage::SENDER_AI => 'Empetix AI',
                    default => null,
                },
                'type' => $msg->type,
                'body' => $displayBody,
                'html' => $displayBody !== ''
                    ? $this->formatWhatsappHtml($displayBody)
                    : '',
                'media_url' => $mediaUrl,
                'download_url' => $downloadUrl,
                'media_mime' => $msg->media_mime,
                'media_filename' => $msg->media_filename,
                'media_size_human' => $msg->media_size ? $this->formatBytes((int) $msg->media_size) : null,
                'media_duration' => $msg->media_duration,
                'location' => $meta['location'] ?? null,
                'contacts' => $meta['contacts'] ?? null,
                'template_name' => $meta['template_name'] ?? null,
                'error' => $meta['error'] ?? null,
                'reaction_emoji' => $msg->reaction_emoji,
                'status' => $msg->status,
                'encrypted' => true,
                'time' => $msg->created_at?->format('H:i') ?? '',
                'date_key' => $msg->created_at?->format('Y-m-d') ?? '',
                'date_label' => $msg->created_at
                    ? ($msg->created_at->isToday() ? 'Today' : ($msg->created_at->isYesterday() ? 'Yesterday' : $msg->created_at->format('d M Y')))
                    : '',
            ];
        })->values()->all();

        $sharedMedia = collect($serializedMessages)
            ->filter(fn (array $m): bool => $m['media_url'] !== null && in_array($m['type'], ['image', 'video', 'document', 'voice', 'audio'], true))
            ->reverse()
            ->take(24)
            ->values()
            ->all();

        return [
            'contact' => [
                ...$this->serializeContact($session),
                'contact_notes' => $session->contact_notes ?? '',
                'handoff_summary' => $session->handoff_summary ?? '',
                'contact_message_url' => $session->contactMessage
                    ? route('admin.inbox.show', $session->contactMessage->id)
                    : null,
                'window_expires_human' => $session->withinCustomerServiceWindow() && $session->last_customer_message_at
                    ? $session->last_customer_message_at->copy()->addHours(24)->diffForHumans()
                    : null,
            ],
            'presence' => $this->supportChats->presenceFor($session),
            'messages' => $serializedMessages,
            'shared_media' => $sharedMedia,
        ];
    }

    /**
     * Render WhatsApp / Markdown bold, italics, template badges, and safe links cleanly.
     */
    protected function formatWhatsappHtml(string $text): string
    {
        if (preg_match('/^\[Sent WhatsApp Template:\s*(.+?)\]$/i', trim($text), $m)) {
            return '<span style="display:inline-flex;align-items:center;gap:6px;font-size:0.82rem;font-weight:600;color:var(--wa-green);">📋 Template sent: <strong>'.e($m[1]).'</strong></span>';
        }

        $escaped = e($text);

        // Convert **bold** and *bold* to <strong>
        $escaped = (string) preg_replace('/\*\*(.+?)\*\*/su', '<strong>$1</strong>', $escaped);
        $escaped = (string) preg_replace('/(?<!\w)\*([^*\n]+)\*(?!\w)/u', '<strong>$1</strong>', $escaped);

        // Auto-link https:// URLs safely
        $escaped = (string) preg_replace(
            '/(https?:\/\/[^\s<]+)/i',
            '<a href="$1" target="_blank" rel="noopener noreferrer" style="color:var(--wa-green);font-weight:600;text-decoration:underline;">$1</a>',
            $escaped,
        );

        return nl2br($escaped);
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }
        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return round($bytes / (1024 * 1024), 1).' MB';
    }
}
