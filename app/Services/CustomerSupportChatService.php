<?php

namespace App\Services;

use App\Ai\Agents\CustomerSupportAgent;
use App\Models\Setting;
use App\Models\SupportChatSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Ai\Contracts\ConversationStore;
use Laravel\Ai\Enums\MessageStatus;
use Laravel\Ai\Messages\UserMessage;
use Laravel\Ai\Models\Conversation;
use Laravel\Ai\Models\ConversationMessage;
use Throwable;

/**
 * Powers the public-facing Customer Support AI Chatbot widget, real-time typing
 * presence heartbeats, and its live admin transcript viewer & human takeover.
 */
class CustomerSupportChatService
{
    public const DEFAULT_GREETING = "Hi there! I'm the Empetix assistant. Ask me about our services, case studies, or open roles — or tell me about your project and I'll connect you with our engineering leads.";

    public function __construct(private AiRuntimeGuard $guard) {}

    /**
     * Whether the public chatbot widget is enabled in config and dashboard settings.
     */
    public function isEnabled(): bool
    {
        return (bool) config('assistant.support.enabled', true)
            && Setting::getValue('ai_support_enabled', '1') !== '0';
    }

    /**
     * Configured welcome message shown before the visitor sends a message.
     */
    public function greeting(): string
    {
        return Setting::getValue('ai_support_greeting', self::DEFAULT_GREETING) ?: self::DEFAULT_GREETING;
    }

    /**
     * Look up an existing session by UUID token, if valid.
     */
    public function findByToken(?string $token): ?SupportChatSession
    {
        if (! $token || ! Str::isUuid($token)) {
            return null;
        }

        return SupportChatSession::query()->where('token', $token)->first();
    }

    /**
     * Create a new visitor chat session when the first message or typing event arrives.
     */
    public function createSession(?string $ip, ?string $userAgent, ?string $landingPage): SupportChatSession
    {
        return SupportChatSession::query()->create([
            'token' => (string) Str::uuid(),
            'status' => SupportChatSession::STATUS_OPEN,
            'message_count' => 0,
            'ip_address' => $ip ? Str::limit($ip, 45, '') : null,
            'user_agent' => $userAgent ? Str::limit($userAgent, 250, '') : null,
            'landing_page' => $landingPage ? Str::limit($landingPage, 250, '') : null,
            'last_message_at' => now(),
        ]);
    }

    /**
     * Record a real-time typing heartbeat for a session actor ('customer', 'ai', or 'admin').
     */
    public function markTyping(
        SupportChatSession $session,
        string $actor,
        bool $isTyping = true,
        ?string $preview = null,
        int $ttlSeconds = 6,
    ): void {
        $key = $this->typingCacheKey($session->id, $actor);

        if ($isTyping) {
            Cache::put($key, [
                'typing' => true,
                'preview' => $preview !== null && trim($preview) !== '' ? Str::limit(trim($preview), 140) : null,
                'at' => now()->timestamp,
            ], now()->addSeconds($ttlSeconds));

            $activeIds = Cache::get('support_chat:active_ids', []);
            if (is_array($activeIds)) {
                $activeIds[$session->id] = now()->timestamp;
                Cache::put('support_chat:active_ids', $activeIds, now()->addMinutes(5));
            }
        } else {
            Cache::forget($key);
        }
    }

    /**
     * Get live typing & online presence state for a support chat session.
     *
     * @return array{customer_typing: bool, customer_preview: ?string, ai_typing: bool, admin_typing: bool, ai_paused: bool, online: bool}
     */
    public function presenceFor(?SupportChatSession $session): array
    {
        if (! $session) {
            return [
                'customer_typing' => false,
                'customer_preview' => null,
                'ai_typing' => false,
                'admin_typing' => false,
                'ai_paused' => false,
                'online' => false,
            ];
        }

        $customer = Cache::get($this->typingCacheKey($session->id, 'customer'));
        $ai = Cache::get($this->typingCacheKey($session->id, 'ai'));
        $admin = Cache::get($this->typingCacheKey($session->id, 'admin'));

        $customerTyping = is_array($customer) && ! empty($customer['typing']);
        $aiTyping = is_array($ai) && ! empty($ai['typing']);
        $adminTyping = is_array($admin) && ! empty($admin['typing']);

        $recentActivity = $session->last_message_at && $session->last_message_at->gt(now()->subMinutes(5));

        return [
            'customer_typing' => $customerTyping,
            'customer_preview' => $customerTyping ? ($customer['preview'] ?? null) : null,
            'ai_typing' => $aiTyping,
            'admin_typing' => $adminTyping,
            'ai_paused' => (bool) $session->ai_paused,
            'online' => $customerTyping || $aiTyping || $adminTyping || $recentActivity,
        ];
    }

    /**
     * Send a visitor message to the support agent and return the updated session payload.
     *
     * @return array<string, mixed>
     */
    public function sendMessage(
        ?string $token,
        string $message,
        ?string $ip = null,
        ?string $userAgent = null,
        ?string $landingPage = null,
    ): array {
        $session = $this->findByToken($token);

        if (! $session || $session->isClosed()) {
            $session = $this->createSession($ip, $userAgent, $landingPage);
        }

        $this->markTyping($session, 'customer', false);

        $maxMessages = (int) config('assistant.support.max_messages_per_session', 60);
        if ($session->message_count >= $maxMessages) {
            return [
                ...$this->serializeSession($session),
                'limit_reached' => true,
                'fallback_reply' => $this->fallbackReply('This chat has reached its message limit. You can start a new chat or reach our team directly.'),
            ];
        }

        // Human Takeover mode on Website Chat: record customer message without invoking AI
        if ($session->ai_paused) {
            $conversationId = $this->ensureWebConversationExists($session, $message);
            resolve(ConversationStore::class)->storeUserMessage(
                $conversationId,
                Conversation::participantType($session),
                Conversation::participantKey($session),
                CustomerSupportAgent::class,
                new UserMessage($message),
            );

            $session->forceFill([
                'conversation_id' => $conversationId,
                'message_count' => $session->message_count + 1,
                'last_message_at' => now(),
            ])->save();

            return $this->serializeSession($session->refresh());
        }

        $agent = new CustomerSupportAgent($session);
        $rawProvider = $agent->provider();
        $primaryProvider = is_array($rawProvider) ? (string) array_key_first($rawProvider) : $rawProvider;
        $provider = $this->guard->resolvedProvider($primaryProvider);

        if (! $this->guard->isReady($provider, CustomerSupportAgent::isFaked())) {
            return [
                ...$this->serializeSession($session),
                'fallback_reply' => $this->fallbackReply(),
            ];
        }

        $this->markTyping($session, 'ai', true, null, 35);

        try {
            $response = $agent
                ->continueOrStart($session->conversation_id, as: $session)
                ->prompt($message);
        } catch (Throwable) {
            $this->markTyping($session, 'ai', false);

            return [
                ...$this->serializeSession($session->refresh()),
                'fallback_reply' => $this->fallbackReply(),
            ];
        } finally {
            $this->markTyping($session, 'ai', false);
        }

        $session->refresh();
        $session->forceFill([
            'conversation_id' => $response->conversationId ?? $session->conversation_id,
            'message_count' => $session->message_count + 1,
            'last_message_at' => now(),
        ])->save();

        return $this->serializeSession($session->refresh());
    }

    /**
     * Send a direct human specialist reply from the Admin Dashboard to a Website chat session.
     */
    public function sendHumanReply(SupportChatSession $session, string $message, bool $pauseAi = true): SupportChatSession
    {
        $this->markTyping($session, 'admin', false);

        $conversationId = $this->ensureWebConversationExists($session, $message);

        ConversationMessage::query()->create([
            'id' => (string) Str::uuid7(),
            'conversation_id' => $conversationId,
            'participant_type' => Conversation::participantType($session),
            'participant_id' => Conversation::participantKey($session),
            'agent' => CustomerSupportAgent::class,
            'role' => 'assistant',
            'content' => trim($message),
            'attachments' => [],
            'steps' => [],
            'usage' => [],
            'meta' => ['source' => 'human_agent'],
            'status' => MessageStatus::Completed,
        ]);

        $session->forceFill([
            'conversation_id' => $conversationId,
            'ai_paused' => $pauseAi,
            'last_message_at' => now(),
        ])->save();

        return $session->refresh();
    }

    /**
     * Public JSON representation of a chat session (or empty initial state).
     *
     * @return array<string, mixed>
     */
    public function serializeSession(?SupportChatSession $session): array
    {
        if (! $session) {
            return [
                'token' => null,
                'status' => SupportChatSession::STATUS_OPEN,
                'status_label' => 'Open',
                'handed_off' => false,
                'ai_paused' => false,
                'can_send' => true,
                'greeting' => $this->greeting(),
                'presence' => $this->presenceFor(null),
                'messages' => [],
            ];
        }

        $maxMessages = (int) config('assistant.support.max_messages_per_session', 60);

        return [
            'token' => $session->token,
            'status' => $session->status,
            'status_label' => $session->statusLabel(),
            'handed_off' => $session->status === SupportChatSession::STATUS_HANDED_OFF,
            'ai_paused' => (bool) $session->ai_paused,
            'can_send' => ! $session->isClosed() && $session->message_count < $maxMessages,
            'greeting' => $this->greeting(),
            'presence' => $this->presenceFor($session),
            'messages' => $this->transcriptFor($session, includeTools: false),
        ];
    }

    /**
     * Full transcript for a session (with optional tool call metadata for admins).
     *
     * @return list<array<string, mixed>>
     */
    public function transcriptFor(SupportChatSession $session, bool $includeTools = true): array
    {
        if (! $session->conversation_id) {
            return [];
        }

        return ConversationMessage::query()
            ->where('conversation_id', $session->conversation_id)
            ->orderBy('id')
            ->get()
            ->map(function (ConversationMessage $row) use ($includeTools): array {
                $content = (string) ($row->content ?? '');

                $entry = [
                    'id' => $row->id,
                    'role' => $row->role,
                    'source' => is_array($row->meta) ? ($row->meta['source'] ?? null) : null,
                    'content' => $content,
                    'html' => $row->role === 'assistant'
                        ? Str::markdown($content, ['html_input' => 'strip', 'allow_unsafe_links' => false])
                        : nl2br(e($content)),
                    'time' => $row->created_at?->format('H:i') ?? '',
                    'timestamp' => $row->created_at?->format('d M Y, H:i') ?? '',
                ];

                if ($includeTools) {
                    $entry['tools'] = array_values(array_map(
                        fn (array $call): string => (string) ($call['name'] ?? 'Tool'),
                        $row->tool_calls,
                    ));
                }

                return $entry;
            })
            ->values()
            ->all();
    }

    /**
     * Delete a support session and its underlying SDK conversation records.
     */
    public function deleteSession(SupportChatSession $session): void
    {
        $this->markTyping($session, 'customer', false);
        $this->markTyping($session, 'ai', false);
        $this->markTyping($session, 'admin', false);

        if ($session->conversation_id) {
            ConversationMessage::query()->where('conversation_id', $session->conversation_id)->delete();
            Conversation::query()->where('id', $session->conversation_id)->delete();
        }

        if ($session->isWhatsapp()) {
            Storage::disk(WhatsappCustomerSupportService::MEDIA_DISK)
                ->deleteDirectory(WhatsappCustomerSupportService::MEDIA_DIR.'/'.$session->id);
        }

        $session->delete();
    }

    protected function ensureWebConversationExists(SupportChatSession $session, string $seedTitle): string
    {
        if ($session->conversation_id) {
            return $session->conversation_id;
        }

        return resolve(ConversationStore::class)->storeConversation(
            Conversation::participantType($session),
            Conversation::participantKey($session),
            Str::limit($seedTitle ?: 'Web Support Chat', 50, preserveWords: true),
        );
    }

    protected function typingCacheKey(int $sessionId, string $actor): string
    {
        return "support_chat:typing:{$sessionId}:{$actor}";
    }

    /**
     * Friendly fallback message when the AI provider is temporarily unreachable.
     */
    protected function fallbackReply(?string $lead = null): array
    {
        $email = Setting::getValue('email', 'hello@empetix.com') ?: 'hello@empetix.com';
        $contactUrl = page_active('contact') ? route('contact') : "mailto:{$email}";
        $prefix = $lead ?: 'Our live AI assistant is momentarily unavailable, so let us connect you directly with our team.';
        $markdown = "{$prefix}\n\nYou can submit a project brief on our [Contact Page]({$contactUrl}) or email us at **{$email}** — we respond within 24 business hours.";

        return [
            'role' => 'assistant',
            'content' => $markdown,
            'html' => Str::markdown($markdown, ['html_input' => 'strip', 'allow_unsafe_links' => false]),
            'time' => now()->format('H:i'),
        ];
    }
}
