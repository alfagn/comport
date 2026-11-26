<?php

namespace App\Services;

use App\Ai\Agents\CustomerSupportAgent;
use App\Models\Setting;
use App\Models\SupportChatSession;
use Illuminate\Support\Str;
use Laravel\Ai\Models\Conversation;
use Laravel\Ai\Models\ConversationMessage;
use Throwable;

/**
 * Powers the public-facing Customer Support AI Chatbot widget and its admin
 * transcript viewer.
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
     * Create a new visitor chat session when the first message arrives.
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
        ]);
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

        $maxMessages = (int) config('assistant.support.max_messages_per_session', 60);
        if ($session->message_count >= $maxMessages) {
            return [
                ...$this->serializeSession($session),
                'limit_reached' => true,
                'fallback_reply' => $this->fallbackReply('This chat has reached its message limit. You can start a new chat or reach our team directly.'),
            ];
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

        try {
            $response = $agent
                ->continueOrStart($session->conversation_id, as: $session)
                ->prompt($message);
        } catch (Throwable) {
            return [
                ...$this->serializeSession($session->refresh()),
                'fallback_reply' => $this->fallbackReply(),
            ];
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
                'can_send' => true,
                'greeting' => $this->greeting(),
                'messages' => [],
            ];
        }

        $maxMessages = (int) config('assistant.support.max_messages_per_session', 60);

        return [
            'token' => $session->token,
            'status' => $session->status,
            'status_label' => $session->statusLabel(),
            'handed_off' => $session->status === SupportChatSession::STATUS_HANDED_OFF,
            'can_send' => ! $session->isClosed() && $session->message_count < $maxMessages,
            'greeting' => $this->greeting(),
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
        if ($session->conversation_id) {
            ConversationMessage::query()->where('conversation_id', $session->conversation_id)->delete();
            Conversation::query()->where('id', $session->conversation_id)->delete();
        }

        $session->delete();
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
