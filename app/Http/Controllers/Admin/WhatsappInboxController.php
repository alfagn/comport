<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SupportChatSession;
use App\Models\WhatsappMessage;
use App\Services\CustomerSupportChatService;
use App\Services\WhatsappCloudApiService;
use App\Services\WhatsappCustomerSupportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Dedicated WhatsApp Business Inbox Studio (/admin/whatsapp).
 *
 * Completely independent from the Email Inbox: provides a WhatsApp Web /
 * Enterprise CRM layout with contacts list, unread counters, photos, videos,
 * voice note recording & waveform playback, document attachments, locations,
 * quoted replies, emoji reactions, blue read checkmarks, and unread email alerts.
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
    ) {}

    /**
     * Render the full-viewport WhatsApp Business Inbox workspace.
     */
    public function index(Request $request): View
    {
        Cache::put('whatsapp:admin_online_any', true, now()->addSeconds(18));

        $requestedChatId = (int) $request->query('chat', 0);

        $contacts = $this->queryContacts()->limit(80)->get();
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

        $contactsPayload = $this->queryContacts()
            ->limit(80)
            ->get()
            ->map(fn (SupportChatSession $s): array => $this->serializeContact($s))
            ->values()
            ->all();

        return view('admin.whatsapp.index', [
            'whatsappConfigured' => $this->whatsappApi->isConfigured(),
            'whatsappEnabled' => $this->whatsappApi->isEnabled(),
            'contacts' => $contactsPayload,
            'activeChat' => $initialPayload,
            'totalUnread' => (int) SupportChatSession::whatsapp()->sum('unread_count'),
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

        $contacts = $this->queryContacts($filter, $search)
            ->limit(100)
            ->get()
            ->map(fn (SupportChatSession $s): array => $this->serializeContact($s))
            ->values()
            ->all();

        return response()->json([
            'ok' => true,
            'total_unread' => (int) SupportChatSession::whatsapp()->sum('unread_count'),
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
        ]);
    }

    /**
     * Send text, photos, videos, documents, voice notes, locations, or templates inside an open thread.
     */
    public function sendMessage(Request $request, int $id): JsonResponse
    {
        @set_time_limit(120);

        $session = SupportChatSession::whatsapp()->with('contactMessage')->findOrFail($id);

        $validated = $request->validate([
            'mode' => ['nullable', 'in:text,media,voice,location,template'],
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
        $pauseAi = $request->has('pause_ai') ? $request->boolean('pause_ai') : (bool) $session->ai_paused || true;
        $replyToWamid = $validated['reply_to_wamid'] ?? null;
        $captionOrBody = trim((string) ($validated['message'] ?? ''));
        $user = $request->user();

        try {
            if ($request->hasFile('voice_note')) {
                $this->whatsappSupport->sendHumanMediaReply(
                    session: $session,
                    file: $request->file('voice_note'),
                    caption: null,
                    isVoice: true,
                    duration: isset($validated['duration']) ? (int) $validated['duration'] : null,
                    pauseAi: $pauseAi,
                    sender: $user,
                    replyToWamid: $replyToWamid,
                );
            } elseif ($request->hasFile('attachments')) {
                $files = $request->file('attachments', []);
                foreach ($files as $idx => $file) {
                    $this->whatsappSupport->sendHumanMediaReply(
                        session: $session,
                        file: $file,
                        caption: $idx === 0 && $captionOrBody !== '' ? $captionOrBody : null,
                        isVoice: false,
                        duration: null,
                        pauseAi: $pauseAi,
                        sender: $user,
                        replyToWamid: $idx === 0 ? $replyToWamid : null,
                    );
                }
            } elseif ($mode === 'location' && isset($validated['latitude'], $validated['longitude'])) {
                $this->whatsappSupport->sendHumanLocationReply(
                    session: $session,
                    latitude: (float) $validated['latitude'],
                    longitude: (float) $validated['longitude'],
                    name: $validated['location_name'] ?? null,
                    address: $validated['location_address'] ?? null,
                    pauseAi: $pauseAi,
                    sender: $user,
                    replyToWamid: $replyToWamid,
                );
            } elseif ($mode === 'template' && filled($validated['template_name'] ?? null)) {
                $this->whatsappSupport->sendOutboundCustomerMessage(
                    phone: (string) ($session->external_thread_id ?: $session->visitor_phone),
                    customerName: $session->visitor_name,
                    mode: 'template',
                    content: (string) $validated['template_name'],
                    templateLang: $validated['template_language'] ?: 'en_US',
                    sender: $user,
                );
            } else {
                if ($captionOrBody === '') {
                    return response()->json([
                        'ok' => false,
                        'message' => 'Please enter a message or attach a file.',
                    ], 422);
                }

                $this->whatsappSupport->sendHumanReply(
                    session: $session,
                    message: $captionOrBody,
                    pauseAi: $pauseAi,
                    sender: $user,
                    replyToWamid: $replyToWamid,
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
     * Update contact CRM metadata, pin state, AI auto-reply toggle, or workflow status.
     */
    public function updateContact(Request $request, int $id): JsonResponse
    {
        $session = SupportChatSession::whatsapp()->with('contactMessage')->findOrFail($id);

        $validated = $request->validate([
            'visitor_name' => ['nullable', 'string', 'max:120'],
            'visitor_email' => ['nullable', 'email', 'max:160'],
            'contact_notes' => ['nullable', 'string', 'max:3000'],
            'status' => ['nullable', 'in:open,handed_off,closed'],
            'is_pinned' => ['nullable', 'boolean'],
            'ai_paused' => ['nullable', 'boolean'],
        ]);

        $updates = [];
        foreach (['visitor_name', 'visitor_email', 'contact_notes', 'status'] as $field) {
            if ($request->exists($field)) {
                $updates[$field] = $validated[$field] ?? null;
            }
        }
        if ($request->has('is_pinned')) {
            $updates['is_pinned'] = $request->boolean('is_pinned');
        }
        if ($request->has('ai_paused')) {
            $updates['ai_paused'] = $request->boolean('ai_paused');
        }

        if ($updates !== []) {
            $session->forceFill($updates)->save();
        }

        $session->refresh();

        return response()->json([
            'ok' => true,
            'contact' => $this->serializeContact($session),
            'active_chat' => $this->serializeActiveChat($session),
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
     * Stream an authenticated WhatsApp media file (image, voice note, audio, video, document).
     */
    public function media(Request $request, int $messageId): BinaryFileResponse
    {
        $message = WhatsappMessage::query()->findOrFail($messageId);

        if (! $message->media_path) {
            throw new NotFoundHttpException('Media not found.');
        }

        $diskName = $message->media_disk ?: WhatsappCustomerSupportService::MEDIA_DISK;
        $disk = Storage::disk($diskName);

        if (! $disk->exists($message->media_path)) {
            throw new NotFoundHttpException('Media file missing on disk.');
        }

        $fullPath = $disk->path($message->media_path);
        $mime = $message->media_mime ?: 'application/octet-stream';
        $filename = $message->media_filename ?: basename($message->media_path);

        if ($request->boolean('download')) {
            return response()->download($fullPath, $filename, [
                'Content-Type' => $mime,
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        return response()->file($fullPath, [
            'Content-Type' => $mime,
            'Accept-Ranges' => 'bytes',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    protected function queryContacts(string $filter = 'all', string $search = '')
    {
        $query = SupportChatSession::query()
            ->whatsapp()
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_message_at')
            ->orderByDesc('id');

        if ($filter === 'unread') {
            $query->where('unread_count', '>', 0);
        } elseif ($filter === 'pinned') {
            $query->where('is_pinned', true);
        } elseif ($filter === 'ai') {
            $query->where('ai_paused', false);
        } elseif ($filter === 'human') {
            $query->where('ai_paused', true);
        }

        if ($search !== '') {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like) {
                $q->where('visitor_name', 'like', $like)
                    ->orWhere('visitor_phone', 'like', $like)
                    ->orWhere('external_thread_id', 'like', $like)
                    ->orWhere('last_message_preview', 'like', $like)
                    ->orWhereHas('whatsappMessages', fn ($mq) => $mq->where('body', 'like', $like));
            });
        }

        return $query;
    }

    /**
     * @return array<string, mixed>
     */
    protected function serializeContact(SupportChatSession $session): array
    {
        $presence = $this->supportChats->presenceFor($session);
        $label = $session->visitorLabel();
        $paletteIdx = abs(crc32((string) ($session->external_thread_id ?: $session->id))) % count(self::AVATAR_PALETTE);

        $timeLabel = '';
        if ($session->last_message_at) {
            $timeLabel = $session->last_message_at->isToday()
                ? $session->last_message_at->format('H:i')
                : ($session->last_message_at->isYesterday() ? 'Yesterday' : $session->last_message_at->format('d/m/Y'));
        }

        $totalMsgs = WhatsappMessage::query()->where('support_chat_session_id', $session->id)->count();
        if ($totalMsgs === 0 && $session->message_count > 0) {
            $totalMsgs = $session->message_count;
        }

        return [
            'id' => $session->id,
            'name' => $label,
            'visitor_name' => $session->visitor_name ?? '',
            'phone' => $session->visitor_phone ?: ($session->external_thread_id ? '+'.$session->external_thread_id : ''),
            'email' => $session->visitor_email ?? '',
            'initial' => mb_strtoupper(mb_substr($label, 0, 1)) ?: 'W',
            'avatar_color' => self::AVATAR_PALETTE[$paletteIdx],
            'unread_count' => (int) $session->unread_count,
            'total_messages' => $totalMsgs,
            'is_pinned' => (bool) $session->is_pinned,
            'ai_paused' => (bool) $session->ai_paused,
            'status' => $session->status,
            'status_label' => $session->statusLabel(),
            'within_24h' => $session->withinCustomerServiceWindow(),
            'preview' => $session->last_message_preview ?: ($session->handoff_summary ?: 'Start chatting on WhatsApp'),
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

            return [
                'id' => $msg->id,
                'wamid' => $msg->wa_message_id,
                'reply_to_wamid' => $msg->reply_to_wa_message_id,
                'quoted' => $msg->metadata['quoted_preview'] ?? null,
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
                'location' => $msg->metadata['location'] ?? null,
                'contacts' => $msg->metadata['contacts'] ?? null,
                'template_name' => $msg->metadata['template_name'] ?? null,
                'error' => $msg->metadata['error'] ?? null,
                'reaction_emoji' => $msg->reaction_emoji,
                'status' => $msg->status,
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
