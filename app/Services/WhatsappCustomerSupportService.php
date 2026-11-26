<?php

namespace App\Services;

use App\Ai\Agents\CustomerSupportAgent;
use App\Models\Setting;
use App\Models\SupportChatSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Ai\Contracts\ConversationStore;
use Laravel\Ai\Enums\MessageStatus;
use Laravel\Ai\Messages\UserMessage;
use Laravel\Ai\Models\Conversation;
use Laravel\Ai\Models\ConversationMessage;
use Throwable;

/**
 * Orchestrates inbound & outbound WhatsApp Customer Support conversations,
 * AI auto-responses, and human agent takeover from the CMS dashboard.
 */
class WhatsappCustomerSupportService
{
    public function __construct(
        private WhatsappCloudApiService $whatsapp,
        private AiRuntimeGuard $guard,
    ) {}

    /**
     * Process an inbound customer message from Meta's webhook.
     */
    public function handleIncomingMessage(
        string $waId,
        ?string $profileName,
        string $messageId,
        string $text,
    ): ?SupportChatSession {
        $normalizedWaId = $this->whatsapp->normalizePhone($waId);
        if ($normalizedWaId === '' || trim($text) === '') {
            return null;
        }

        // Idempotency guard against Meta webhook retries
        if ($messageId !== '' && ! Cache::add('whatsapp:wamid:'.$messageId, true, now()->addHours(24))) {
            return null;
        }

        $session = $this->findOrCreateWhatsappSession($normalizedWaId, $profileName);

        if ($messageId !== '' && $session->last_external_message_id === $messageId) {
            return $session;
        }

        if ((bool) config('assistant.whatsapp.mark_as_read', true) && $messageId !== '') {
            $this->whatsapp->markMessageAsRead($messageId);
        }

        // Human Takeover mode: record the customer's message in the transcript without AI auto-reply
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
                'last_external_message_id' => $messageId ?: $session->last_external_message_id,
                'last_message_at' => now(),
            ])->save();

            return $session->refresh();
        }

        $agent = new CustomerSupportAgent($session);
        $rawProvider = $agent->provider();
        $primaryProvider = is_array($rawProvider) ? (string) array_key_first($rawProvider) : $rawProvider;
        $provider = $this->guard->resolvedProvider($primaryProvider);

        $replyText = null;

        if ($this->guard->isReady($provider, CustomerSupportAgent::isFaked())) {
            app(CustomerSupportChatService::class)->markTyping($session, 'ai', true, null, 35);
            try {
                $response = $agent
                    ->continueOrStart($session->conversation_id, as: $session)
                    ->prompt($text);

                $session->refresh();
                $session->forceFill([
                    'conversation_id' => $response->conversationId ?? $session->conversation_id,
                    'message_count' => $session->message_count + 1,
                    'last_external_message_id' => $messageId ?: $session->last_external_message_id,
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
                'last_external_message_id' => $messageId ?: $session->last_external_message_id,
                'last_message_at' => now(),
            ])->save();
        }

        if ($this->whatsapp->isConfigured()) {
            try {
                $this->whatsapp->sendTextMessage($normalizedWaId, $replyText);
            } catch (Throwable $exception) {
                Log::error('WhatsApp outbound reply failed', [
                    'to' => $normalizedWaId,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return $session->refresh();
    }

    /**
     * Send a manual human reply from the Admin Dashboard to an existing WhatsApp thread.
     *
     * @return array<string, mixed>
     */
    public function sendHumanReply(SupportChatSession $session, string $message, bool $pauseAi = true): array
    {
        app(CustomerSupportChatService::class)->markTyping($session, 'admin', false);

        $recipient = $session->external_thread_id ?: $session->visitor_phone;
        $apiResponse = $this->whatsapp->sendTextMessage((string) $recipient, $message);

        $conversationId = $this->ensureConversationExists($session);
        $this->appendManualAssistantMessage($conversationId, $session, trim($message), 'human_agent');

        $session->forceFill([
            'conversation_id' => $conversationId,
            'ai_paused' => $pauseAi,
            'last_message_at' => now(),
        ])->save();

        return $apiResponse;
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
    ): SupportChatSession {
        $normalized = $this->whatsapp->normalizePhone($phone);

        if ($mode === 'template') {
            $this->whatsapp->sendTemplateMessage($normalized, $content, $templateLang);
            $transcriptNote = "[Sent WhatsApp Template: {$content} ({$templateLang})]";
        } else {
            $this->whatsapp->sendTextMessage($normalized, $content);
            $transcriptNote = trim($content);
        }

        $session = $this->findOrCreateWhatsappSession($normalized, $customerName);

        $conversationId = $this->ensureConversationExists($session);
        $this->appendManualAssistantMessage($conversationId, $session, $transcriptNote, 'human_agent');

        $session->forceFill([
            'conversation_id' => $conversationId,
            'message_count' => $session->message_count + 1,
            'last_message_at' => now(),
        ])->save();

        return $session->refresh();
    }

    /**
     * Find the active WhatsApp session for a phone number or open a new one.
     */
    public function findOrCreateWhatsappSession(string $normalizedWaId, ?string $profileName = null): SupportChatSession
    {
        $session = SupportChatSession::query()
            ->whatsapp()
            ->where('external_thread_id', $normalizedWaId)
            ->where('status', '!=', SupportChatSession::STATUS_CLOSED)
            ->latest('id')
            ->first();

        $displayPhone = $this->whatsapp->displayPhone($normalizedWaId);

        if (! $session) {
            return SupportChatSession::query()->create([
                'token' => (string) Str::uuid(),
                'channel' => SupportChatSession::CHANNEL_WHATSAPP,
                'external_thread_id' => $normalizedWaId,
                'visitor_phone' => $displayPhone,
                'visitor_name' => $profileName ?: null,
                'status' => SupportChatSession::STATUS_OPEN,
                'ai_paused' => false,
                'message_count' => 0,
                'landing_page' => 'WhatsApp Cloud API',
                'last_message_at' => now(),
            ]);
        }

        if ($profileName && ! $session->visitor_name) {
            $session->forceFill(['visitor_name' => $profileName])->save();
        }

        return $session;
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
