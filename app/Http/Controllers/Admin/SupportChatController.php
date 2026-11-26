<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\SupportChatDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupportChatSettingsRequest;
use App\Models\Setting;
use App\Models\SupportChatSession;
use App\Services\AiRuntimeGuard;
use App\Services\CustomerSupportChatService;
use App\Services\WhatsappCloudApiService;
use App\Services\WhatsappCustomerSupportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * Admin management for Website & WhatsApp AI Customer Support:
 * - index()           : Conversations DataTable & KPIs
 * - livePulse()       : Real-time typing & new-message pulse for the index table
 * - dispatchForm()    : Outbound WhatsApp Customer Dispatch page
 * - whatsappSettings(): Meta WhatsApp Business Cloud API setup page
 * - aiSettings()      : Website Chatbot & AI Provider settings page
 * - show()            : Individual conversation transcript & live human reply
 * - live()            : Real-time JSON delta endpoint for an open conversation
 * - typing()          : Admin typing heartbeat endpoint
 */
class SupportChatController extends Controller
{
    public function __construct(
        private CustomerSupportChatService $supportChats,
        private WhatsappCloudApiService $whatsappApi,
        private WhatsappCustomerSupportService $whatsappSupport,
        private AiRuntimeGuard $guard,
    ) {}

    /**
     * Conversations listing table & live KPIs.
     */
    public function index(SupportChatDataTable $table): View|JsonResponse
    {
        return $table->handle('admin.support-chats.index', [
            'stats' => $this->buildStats(),
            'whatsappConfigured' => $this->whatsappApi->isConfigured(),
        ]);
    }

    /**
     * Real-time pulse for the Support Conversations index page: live KPIs,
     * active typing sessions, and latest activity signature to trigger table sync.
     */
    public function livePulse(): JsonResponse
    {
        $recentSessions = SupportChatSession::query()
            ->latest('updated_at')
            ->limit(25)
            ->get();

        $activeTyping = [];
        $signatureParts = [];

        foreach ($recentSessions as $session) {
            $presence = $this->supportChats->presenceFor($session);
            $signatureParts[] = $session->id.':'.$session->message_count.':'.$session->status.':'.($presence['customer_typing'] ? '1' : '0').':'.($presence['ai_typing'] ? '1' : '0');

            if ($presence['customer_typing'] || $presence['ai_typing']) {
                $activeTyping[] = [
                    'id' => $session->id,
                    'visitor_label' => $session->visitorLabel(),
                    'channel' => $session->channel,
                    'is_whatsapp' => $session->isWhatsapp(),
                    'customer_typing' => $presence['customer_typing'],
                    'customer_preview' => $presence['customer_preview'],
                    'ai_typing' => $presence['ai_typing'],
                    'url' => route('admin.support-chats.show', $session->id),
                ];
            }
        }

        return response()->json([
            'ok' => true,
            'stats' => $this->buildStats(),
            'active_typing' => $activeTyping,
            'signature' => md5(implode('|', $signatureParts)),
        ]);
    }

    /**
     * Dedicated page for sending outbound WhatsApp messages & templates.
     */
    public function dispatchForm(): View
    {
        $recentContacts = SupportChatSession::whatsapp()
            ->whereNotNull('visitor_phone')
            ->latest('last_message_at')
            ->limit(12)
            ->get()
            ->unique('external_thread_id')
            ->values();

        return view('admin.support-chats.dispatch', [
            'whatsappConfigured' => $this->whatsappApi->isConfigured(),
            'recentContacts' => $recentContacts,
        ]);
    }

    /**
     * Dedicated page for Meta WhatsApp Business Cloud API credentials & webhook setup.
     */
    public function whatsappSettings(): View
    {
        return view('admin.support-chats.whatsapp', [
            'whatsappConfigured' => $this->whatsappApi->isConfigured(),
            'whatsappEnabled' => $this->whatsappApi->isEnabled(),
            'whatsappVerifyToken' => $this->whatsappApi->verifyToken(),
            'whatsappWebhookUrl' => url('/api/webhooks/whatsapp'),
        ]);
    }

    /**
     * Dedicated page for Website Chatbot behavior, shared FAQ knowledge & AI provider settings.
     */
    public function aiSettings(): View
    {
        $provider = Setting::getValue('ai_support_provider')
            ?: config('assistant.support.provider')
            ?: config('ai.default', 'openai');

        return view('admin.support-chats.settings', [
            'providerReady' => $this->guard->isReady($provider),
            'providerName' => $this->guard->resolvedProvider($provider),
            'providerHint' => $this->guard->missingCredentialsHint($provider),
        ]);
    }

    /**
     * Inspect one visitor/WhatsApp chat session and its full live transcript.
     */
    public function show(int $id): View
    {
        $session = SupportChatSession::query()->with('contactMessage')->findOrFail($id);

        return view('admin.support-chats.show', [
            'session' => $session,
            'transcript' => $this->supportChats->transcriptFor($session, includeTools: true),
            'presence' => $this->supportChats->presenceFor($session),
            'whatsappConfigured' => $this->whatsappApi->isConfigured(),
        ]);
    }

    /**
     * Real-time JSON snapshot for an open conversation transcript page (messages + typing presence).
     */
    public function live(int $id): JsonResponse
    {
        $session = SupportChatSession::query()->with('contactMessage')->findOrFail($id);

        return response()->json($this->buildLiveSessionPayload($session));
    }

    /**
     * Record an admin typing heartbeat while composing a reply in the Dashboard.
     */
    public function typing(Request $request, int $id): JsonResponse
    {
        $session = SupportChatSession::query()->findOrFail($id);
        $isTyping = $request->boolean('is_typing', true);

        $this->supportChats->markTyping($session, 'admin', $isTyping, null, 6);

        return response()->json([
            'ok' => true,
            'presence' => $this->supportChats->presenceFor($session),
        ]);
    }

    /**
     * Save AI Customer Support & Copilot provider/behavior settings.
     */
    public function updateSettings(SupportChatSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Setting::setValue('ai_support_enabled', ! empty($validated['ai_support_enabled']) ? '1' : '0', 'ai');
        Setting::setValue('ai_support_greeting', $validated['ai_support_greeting'], 'ai');
        Setting::setValue('ai_support_knowledge', $validated['ai_support_knowledge'] ?? '', 'ai');
        Setting::setValue('ai_support_provider', $validated['ai_support_provider'] ?? null, 'ai');
        Setting::setValue('ai_support_model', $validated['ai_support_model'] ?? null, 'ai');
        Setting::setValue('ai_dashboard_provider', $validated['ai_dashboard_provider'] ?? null, 'ai');
        Setting::setValue('ai_dashboard_model', $validated['ai_dashboard_model'] ?? null, 'ai');

        return back()->with('success', 'Chatbot behavior and AI provider settings saved.');
    }

    /**
     * Save Meta WhatsApp Business Cloud API credentials and webhook settings.
     */
    public function updateWhatsappSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'whatsapp_enabled' => ['nullable', 'boolean'],
            'whatsapp_public_number' => ['nullable', 'string', 'max:40'],
            'whatsapp_prefill_message' => ['nullable', 'string', 'max:250'],
            'whatsapp_phone_number_id' => ['nullable', 'string', 'max:80'],
            'whatsapp_business_account_id' => ['nullable', 'string', 'max:80'],
            'whatsapp_access_token' => ['nullable', 'string', 'max:1000'],
            'whatsapp_verify_token' => ['required', 'string', 'min:6', 'max:160'],
            'whatsapp_app_secret' => ['nullable', 'string', 'max:160'],
            'whatsapp_api_version' => ['nullable', 'string', 'regex:/^v\d+\.\d+$/'],
        ]);

        Setting::setValue('whatsapp_enabled', ! empty($validated['whatsapp_enabled']) ? '1' : '0', 'whatsapp');
        Setting::setValue('whatsapp_public_number', $validated['whatsapp_public_number'] ?? null, 'whatsapp');
        Setting::setValue('whatsapp_prefill_message', $validated['whatsapp_prefill_message'] ?? null, 'whatsapp');
        Setting::setValue('whatsapp_phone_number_id', $validated['whatsapp_phone_number_id'] ?? null, 'whatsapp');
        Setting::setValue('whatsapp_business_account_id', $validated['whatsapp_business_account_id'] ?? null, 'whatsapp');
        Setting::setValue('whatsapp_verify_token', $validated['whatsapp_verify_token'], 'whatsapp');
        Setting::setValue('whatsapp_api_version', $validated['whatsapp_api_version'] ?: 'v22.0', 'whatsapp');

        if (filled($validated['whatsapp_access_token'] ?? null)) {
            Setting::setValue('whatsapp_access_token', trim((string) $validated['whatsapp_access_token']), 'whatsapp');
        }
        if (filled($validated['whatsapp_app_secret'] ?? null)) {
            Setting::setValue('whatsapp_app_secret', trim((string) $validated['whatsapp_app_secret']), 'whatsapp');
        }

        return back()->with('success', 'WhatsApp Cloud API configuration saved.');
    }

    /**
     * Register WABA webhook subscription (`POST /{waba_id}/subscribed_apps`) on Meta.
     */
    public function subscribeWebhook(): RedirectResponse
    {
        try {
            $this->whatsappApi->subscribeAppToWaba();
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'WhatsApp Business Account successfully subscribed to live webhook events!');
    }

    /**
     * Initiate or send an outbound WhatsApp message (text or Meta template) to a customer.
     */
    public function sendWhatsappOutbound(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'min:7', 'max:30'],
            'customer_name' => ['nullable', 'string', 'max:120'],
            'mode' => ['required', 'in:text,template'],
            'content' => ['required', 'string', 'min:1', 'max:3500'],
            'template_language' => ['nullable', 'string', 'max:20'],
        ]);

        try {
            $session = $this->whatsappSupport->sendOutboundCustomerMessage(
                phone: $validated['phone'],
                customerName: $validated['customer_name'] ?? null,
                mode: $validated['mode'],
                content: $validated['content'],
                templateLang: $validated['template_language'] ?: 'en_US',
            );
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.support-chats.show', $session->id)
            ->with('success', 'WhatsApp message delivered to '.$session->visitor_phone.'.');
    }

    /**
     * Send a direct human reply inside an existing WhatsApp or Website chat thread.
     */
    public function replyWhatsapp(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $session = SupportChatSession::query()->with('contactMessage')->findOrFail($id);

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:1', 'max:3500'],
            'pause_ai' => ['nullable', 'boolean'],
        ]);

        $pauseAi = (bool) ($validated['pause_ai'] ?? true);

        try {
            if ($session->isWhatsapp()) {
                $this->whatsappSupport->sendHumanReply(
                    session: $session,
                    message: $validated['message'],
                    pauseAi: $pauseAi,
                );
            } else {
                $this->supportChats->sendHumanReply(
                    session: $session,
                    message: $validated['message'],
                    pauseAi: $pauseAi,
                );
            }
        } catch (RuntimeException $exception) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => $exception->getMessage(),
                ], 422);
            }

            return back()->with('error', $exception->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json($this->buildLiveSessionPayload($session->fresh(['contactMessage'])));
        }

        return back()->with('success', $session->isWhatsapp() ? 'WhatsApp reply sent to customer.' : 'Live reply sent to website visitor.');
    }

    /**
     * Pause or resume AI auto-replies on a WhatsApp or Website customer thread.
     */
    public function toggleAi(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $session = SupportChatSession::query()->findOrFail($id);
        $nextPaused = ! $session->ai_paused;
        $session->forceFill(['ai_paused' => $nextPaused])->save();

        $notice = $nextPaused
            ? 'AI auto-reply paused (Human Takeover active).'
            : 'AI auto-reply resumed for this customer.';

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'ai_paused' => $nextPaused,
                'message' => $notice,
            ]);
        }

        return back()->with('success', $notice);
    }

    /**
     * Update a support chat's workflow status (open / handed_off / closed).
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,handed_off,closed'],
        ]);

        $session = SupportChatSession::query()->findOrFail($id);
        $session->forceFill(['status' => $validated['status']])->save();

        return back()->with('success', 'Support chat status updated to '.$session->statusLabel().'.');
    }

    /**
     * Delete a support chat session and its transcript.
     */
    public function destroy(int $id): RedirectResponse
    {
        $session = SupportChatSession::query()->findOrFail($id);
        $this->supportChats->deleteSession($session);

        return redirect()->route('admin.support-chats.index')->with('success', 'Support chat deleted.');
    }

    /**
     * @return array{total: int, whatsapp: int, handed_off: int, today: int}
     */
    protected function buildStats(): array
    {
        return [
            'total' => SupportChatSession::query()->count(),
            'whatsapp' => SupportChatSession::whatsapp()->count(),
            'handed_off' => SupportChatSession::handedOff()->count(),
            'today' => SupportChatSession::query()->whereDate('created_at', today())->count(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildLiveSessionPayload(SupportChatSession $session): array
    {
        return [
            'ok' => true,
            'session' => [
                'id' => $session->id,
                'channel' => $session->channel,
                'is_whatsapp' => $session->isWhatsapp(),
                'visitor_label' => $session->visitorLabel(),
                'visitor_initial' => strtoupper(substr($session->visitorLabel(), 0, 1)),
                'visitor_phone' => $session->visitor_phone,
                'visitor_email' => $session->visitor_email,
                'status' => $session->status,
                'status_label' => $session->statusLabel(),
                'ai_paused' => (bool) $session->ai_paused,
                'message_count' => $session->message_count,
                'handoff_summary' => $session->handoff_summary,
                'contact_message_id' => $session->contact_message_id,
                'contact_message_url' => $session->contactMessage ? route('admin.inbox.show', $session->contactMessage->id) : null,
                'last_active_human' => $session->last_message_at?->diffForHumans() ?? 'just now',
            ],
            'presence' => $this->supportChats->presenceFor($session),
            'messages' => $this->supportChats->transcriptFor($session, includeTools: true),
        ];
    }
}
