<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportChatSession;
use App\Services\CustomerSupportChatService;
use App\Services\WhatsappMediaCrypto;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Ai\Models\ConversationMessage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Dedicated Website Live Chat Studio (/admin/website-chat).
 *
 * Uses the exact 3-pane WhatsApp Web System Design for public website visitors
 * chatting with the Empetix AI Concierge or live human specialists:
 * - Left pane: Visitors list with unread-only green badges, bold unread names, live typing preview & filters
 * - Center pane: WhatsApp-style chat stage with 0ms optimistic send (`🕒` -> `✓✓`), idempotency & AI/Human switch
 * - Right pane: Visitor CRM profile, landing page, IP, handoff brief link & encrypted notes
 */
class WebsiteChatController extends Controller
{
    private const AVATAR_PALETTE = [
        '#4F46E5', '#0284C7', '#0D9488', '#7C3AED', '#EA580C', '#DB2777', '#00A884', '#D97706',
    ];

    public function __construct(
        private CustomerSupportChatService $supportChats,
        private WhatsappMediaCrypto $mediaCrypto,
    ) {}

    /**
     * Render the full-viewport Website Chat Studio workspace.
     */
    public function index(Request $request): View
    {
        $requestedChatId = (int) $request->query('chat', 0);

        $contacts = $this->loadVisitors();
        $activeSession = null;

        if ($requestedChatId > 0) {
            $activeSession = $contacts->firstWhere('id', $requestedChatId)
                ?: SupportChatSession::web()->with('contactMessage')->find($requestedChatId);
        } elseif ($contacts->isNotEmpty()) {
            $activeSession = $contacts->first();
        }

        $initialPayload = null;
        if ($activeSession) {
            $this->markVisitorChatRead($activeSession);
            Cache::put("webchat:admin_viewing:{$activeSession->id}", true, now()->addSeconds(18));
            $activeSession->refresh();
            $initialPayload = $this->serializeActiveChat($activeSession);
        }

        $contactsPayload = $this->loadVisitors()
            ->map(fn (SupportChatSession $s): array => $this->serializeVisitor($s))
            ->values()
            ->all();

        return view('admin.website-chat.index', [
            'chatbotEnabled' => $this->supportChats->isEnabled(),
            'contacts' => $contactsPayload,
            'activeChat' => $initialPayload,
            'totalUnread' => (int) SupportChatSession::web()->sum('unread_count'),
        ]);
    }

    /**
     * Real-time poller for the Website Chat Studio.
     */
    public function poll(Request $request): JsonResponse
    {
        $activeId = (int) $request->query('active_id', 0);
        $filter = (string) $request->query('filter', 'all');
        $search = trim((string) $request->query('q', ''));

        $activePayload = null;
        if ($activeId > 0) {
            $activeSession = SupportChatSession::web()->with('contactMessage')->find($activeId);
            if ($activeSession) {
                Cache::put("webchat:admin_viewing:{$activeSession->id}", true, now()->addSeconds(18));
                if ($activeSession->unread_count > 0) {
                    $this->markVisitorChatRead($activeSession);
                    $activeSession->refresh();
                }
                $activePayload = $this->serializeActiveChat($activeSession);
            }
        }

        $contacts = $this->loadVisitors($filter, $search, 100)
            ->map(fn (SupportChatSession $s): array => $this->serializeVisitor($s))
            ->values()
            ->all();

        return response()->json([
            'ok' => true,
            'total_unread' => (int) SupportChatSession::web()->sum('unread_count'),
            'contacts' => $contacts,
            'active_chat' => $activePayload,
        ]);
    }

    /**
     * Load one Website visitor conversation and mark it read.
     */
    public function show(int $id): JsonResponse
    {
        $session = SupportChatSession::web()->with('contactMessage')->findOrFail($id);
        $this->markVisitorChatRead($session);
        Cache::put("webchat:admin_viewing:{$session->id}", true, now()->addSeconds(18));

        return response()->json([
            'ok' => true,
            'total_unread' => (int) SupportChatSession::web()->sum('unread_count'),
            'active_chat' => $this->serializeActiveChat($session->refresh()),
        ]);
    }

    /**
     * Send a live human specialist reply into the visitor's Website chat widget with strict idempotency.
     */
    public function sendMessage(Request $request, int $id): JsonResponse
    {
        $session = SupportChatSession::web()->with('contactMessage')->findOrFail($id);

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:1', 'max:4000'],
            'idempotency_key' => ['nullable', 'string', 'max:100'],
            'pause_ai' => ['nullable', 'boolean'],
        ]);

        $body = trim($validated['message']);
        $pauseAi = $request->has('pause_ai') ? $request->boolean('pause_ai') : true;
        $rawKey = trim((string) ($request->header('X-Idempotency-Key') ?: ($validated['idempotency_key'] ?? '')));
        $idemKey = (string) Str::limit((string) preg_replace('/[^A-Za-z0-9._:-]/', '', $rawKey), 90, '');

        if ($idemKey !== '' && ! Cache::add("webchat:idem:{$idemKey}", true, now()->addMinutes(10))) {
            $session->refresh();

            return response()->json([
                'ok' => true,
                'duplicate' => true,
                'contact' => $this->serializeVisitor($session),
                'active_chat' => $this->serializeActiveChat($session, $idemKey),
            ]);
        }

        $senderName = $request->user()?->name ?: 'Empetix Specialist';

        $updated = $this->supportChats->sendHumanReply(
            session: $session,
            message: $body,
            pauseAi: $pauseAi,
            senderName: $senderName,
        );

        // Stamp idempotency_key on latest assistant message metadata for smooth optimistic reconciliation
        if ($idemKey !== '' && $updated->conversation_id) {
            $latest = ConversationMessage::query()
                ->where('conversation_id', $updated->conversation_id)
                ->latest('created_at')
                ->first();
            if ($latest) {
                $meta = is_array($latest->meta) ? $latest->meta : [];
                $meta['idempotency_key'] = $idemKey;
                $latest->forceFill(['meta' => $meta])->save();
            }
        }

        return response()->json([
            'ok' => true,
            'contact' => $this->serializeVisitor($updated),
            'active_chat' => $this->serializeActiveChat($updated),
        ]);
    }

    /**
     * Broadcast admin typing heartbeat to the public website visitor widget.
     */
    public function typing(Request $request, int $id): JsonResponse
    {
        $session = SupportChatSession::web()->findOrFail($id);
        $this->supportChats->markTyping($session, 'admin', $request->boolean('is_typing', true), null, 6);

        return response()->json(['ok' => true]);
    }

    /**
     * Update visitor CRM details, avatar photo, pin state, AI auto-reply toggle, or workflow status.
     */
    public function updateVisitor(Request $request, int $id): JsonResponse
    {
        $session = SupportChatSession::web()->with('contactMessage')->findOrFail($id);

        $validated = $request->validate([
            'visitor_name' => ['nullable', 'string', 'max:120'],
            'visitor_email' => ['nullable', 'email', 'max:160'],
            'visitor_phone' => ['nullable', 'string', 'max:40'],
            'contact_notes' => ['nullable', 'string', 'max:3000'],
            'status' => ['nullable', 'in:open,handed_off,closed'],
            'is_pinned' => ['nullable', 'boolean'],
            'ai_paused' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'image', 'max:5120'],
        ]);

        $updates = [];
        foreach (['visitor_name', 'visitor_email', 'visitor_phone', 'contact_notes', 'status'] as $field) {
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

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $mime = strtolower((string) ($file->getMimeType() ?: 'image/jpeg'));
            $avatarPath = 'webchat-avatars/'.$session->id.'/avatar_'.substr((string) Str::uuid(), 0, 8).'.'.$ext;

            if ($session->visitor_avatar && str_starts_with($session->visitor_avatar, 'local:')) {
                $oldMeta = explode('|', substr($session->visitor_avatar, 6), 2);
                Storage::disk('local')->delete($oldMeta[0]);
            }

            $this->mediaCrypto->putEncrypted('local', $avatarPath, (string) $file->get());
            $updates['visitor_avatar'] = "local:{$avatarPath}|{$mime}";
        }

        if ($updates !== []) {
            $session->forceFill($updates)->save();
        }

        $session->refresh();

        return response()->json([
            'ok' => true,
            'contact' => $this->serializeVisitor($session),
            'active_chat' => $this->serializeActiveChat($session),
        ]);
    }

    /**
     * Stream encrypted visitor avatar photo.
     */
    public function avatar(int $id): Response
    {
        $session = SupportChatSession::web()->findOrFail($id);
        $avatar = (string) ($session->visitor_avatar ?? '');

        if ($avatar === '' || ! str_starts_with($avatar, 'local:')) {
            throw new NotFoundHttpException('Avatar not set.');
        }

        [$path, $mime] = array_pad(explode('|', substr($avatar, 6), 2), 2, 'image/jpeg');
        $bytes = $this->mediaCrypto->getDecrypted('local', $path);

        if ($bytes === null || $bytes === '') {
            throw new NotFoundHttpException('Avatar missing.');
        }

        return response($bytes, 200, [
            'Content-Type' => $mime ?: 'image/jpeg',
            'Content-Length' => (string) strlen($bytes),
            'Cache-Control' => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Delete a Website visitor conversation and its transcript.
     */
    public function destroy(int $id): JsonResponse
    {
        $session = SupportChatSession::web()->findOrFail($id);
        Storage::disk('local')->deleteDirectory('webchat-avatars/'.$session->id);
        $this->supportChats->deleteSession($session);

        return response()->json([
            'ok' => true,
            'total_unread' => (int) SupportChatSession::web()->sum('unread_count'),
        ]);
    }

    protected function markVisitorChatRead(SupportChatSession $session): void
    {
        if ($session->unread_count > 0 || $session->last_admin_read_at === null) {
            $session->forceFill([
                'unread_count' => 0,
                'last_admin_read_at' => now(),
            ])->save();
        }
    }

    /**
     * @return Collection<int, SupportChatSession>
     */
    protected function loadVisitors(string $filter = 'all', string $search = '', int $limit = 80): Collection
    {
        $query = SupportChatSession::query()
            ->web()
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_message_at')
            ->orderByDesc('id');

        if ($filter === 'unread') {
            $query->where('unread_count', '>', 0);
        } elseif ($filter === 'pinned') {
            $query->where('is_pinned', true);
        } elseif ($filter === 'human') {
            $query->where(function ($q) {
                $q->where('ai_paused', true)->orWhere('status', SupportChatSession::STATUS_HANDED_OFF);
            });
        }

        $candidates = $query->limit($search !== '' ? 200 : $limit)->get();

        if ($filter === 'live') {
            $candidates = $candidates->filter(fn (SupportChatSession $s): bool => $this->supportChats->presenceFor($s)['online']);
        }

        if ($search === '') {
            return $candidates->take($limit)->values();
        }

        $needle = mb_strtolower($search);

        return $candidates
            ->filter(function (SupportChatSession $s) use ($needle): bool {
                $haystack = implode(' ', [
                    $s->visitorLabel(),
                    (string) $s->visitor_name,
                    (string) $s->visitor_email,
                    (string) $s->visitor_phone,
                    (string) $s->landing_page,
                    (string) $s->ip_address,
                    (string) $s->last_message_preview,
                    (string) $s->handoff_summary,
                    (string) $s->contact_notes,
                ]);

                if (str_contains(mb_strtolower($haystack), $needle)) {
                    return true;
                }

                if ($s->conversation_id) {
                    return ConversationMessage::query()
                        ->where('conversation_id', $s->conversation_id)
                        ->where('content', 'like', '%'.$needle.'%')
                        ->exists();
                }

                return false;
            })
            ->take($limit)
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    protected function serializeVisitor(SupportChatSession $session): array
    {
        $presence = $this->supportChats->presenceFor($session);
        $name = trim((string) ($session->visitor_name ?? '')) !== ''
            ? trim((string) $session->visitor_name)
            : ('Visitor #'.$session->id);

        $paletteIdx = abs(crc32((string) $session->token)) % count(self::AVATAR_PALETTE);

        $timeLabel = '';
        if ($session->last_message_at) {
            $timeLabel = $session->last_message_at->isToday()
                ? $session->last_message_at->format('H:i')
                : ($session->last_message_at->isYesterday() ? 'Yesterday' : $session->last_message_at->format('d/m/Y'));
        }

        $avatarUrl = null;
        if (filled($session->visitor_avatar)) {
            $avatarUrl = str_starts_with((string) $session->visitor_avatar, 'http')
                ? (string) $session->visitor_avatar
                : route('admin.website-chat.avatar', $session->id).'?v='.substr(md5((string) $session->visitor_avatar), 0, 8);
        }

        $subIdentity = $session->visitor_email
            ?: ($session->visitor_phone ?: ($session->landing_page ?: 'Website Visitor'));

        $lastRole = null;
        if ($session->conversation_id) {
            $lastRole = ConversationMessage::query()
                ->where('conversation_id', $session->conversation_id)
                ->latest('created_at')
                ->value('role');
        }

        return [
            'id' => $session->id,
            'name' => $name,
            'visitor_name' => $session->visitor_name ?? '',
            'email' => $session->visitor_email ?? '',
            'phone' => $session->visitor_phone ?? '',
            'sub_identity' => $subIdentity,
            'landing_page' => $session->landing_page ?? '/',
            'ip_address' => $session->ip_address ?? '',
            'avatar_url' => $avatarUrl,
            'initial' => mb_strtoupper(mb_substr($name, 0, 1)) ?: 'V',
            'avatar_color' => self::AVATAR_PALETTE[$paletteIdx],
            'unread_count' => (int) $session->unread_count,
            'total_messages' => (int) $session->message_count,
            'is_pinned' => (bool) $session->is_pinned,
            'ai_paused' => (bool) $session->ai_paused,
            'status' => $session->status,
            'status_label' => $session->statusLabel(),
            'online' => $presence['online'],
            'customer_typing' => $presence['customer_typing'],
            'customer_preview' => $presence['customer_preview'],
            'ai_typing' => $presence['ai_typing'],
            'last_direction' => $lastRole === 'assistant' ? 'outbound' : 'inbound',
            'preview' => $session->last_message_preview ?: ($session->handoff_summary ?: 'Started website chat'),
            'time_label' => $timeLabel,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function serializeActiveChat(SupportChatSession $session): array
    {
        $rows = $session->conversation_id
            ? ConversationMessage::query()
                ->where('conversation_id', $session->conversation_id)
                ->orderBy('created_at')
                ->orderBy('id')
                ->get()
            : collect();

        $messages = $rows->map(function (ConversationMessage $row): array {
            $content = (string) ($row->content ?? '');
            $isOut = $row->role === 'assistant';
            $meta = is_array($row->meta) ? $row->meta : [];
            $source = $meta['source'] ?? null;

            $tools = array_values(array_map(
                fn (array $call): string => (string) ($call['name'] ?? 'Tool'),
                is_array($row->tool_calls) ? $row->tool_calls : [],
            ));

            return [
                'id' => (string) $row->id,
                'idempotency_key' => $meta['idempotency_key'] ?? null,
                'direction' => $isOut ? 'outbound' : 'inbound',
                'sender_type' => $isOut ? ($source === 'human_agent' ? 'admin' : 'ai') : 'customer',
                'sender_name' => $isOut
                    ? ($source === 'human_agent' ? ($meta['sender_name'] ?? 'Team Specialist') : 'Empetix AI')
                    : null,
                'body' => $content,
                'html' => $isOut
                    ? Str::markdown($content, ['html_input' => 'strip', 'allow_unsafe_links' => false])
                    : nl2br(e($content)),
                'tools' => $tools,
                'status' => 'read',
                'time' => $row->created_at?->format('H:i') ?? '',
                'date_key' => $row->created_at?->format('Y-m-d') ?? '',
                'date_label' => $row->created_at
                    ? ($row->created_at->isToday() ? 'Today' : ($row->created_at->isYesterday() ? 'Yesterday' : $row->created_at->format('d M Y')))
                    : '',
            ];
        })->values()->all();

        return [
            'contact' => [
                ...$this->serializeVisitor($session),
                'contact_notes' => $session->contact_notes ?? '',
                'handoff_summary' => $session->handoff_summary ?? '',
                'contact_message_url' => $session->contactMessage
                    ? route('admin.inbox.show', $session->contactMessage->id)
                    : null,
            ],
            'presence' => $this->supportChats->presenceFor($session),
            'messages' => $messages,
        ];
    }
}
