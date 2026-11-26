<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\SupportChatMessageRequest;
use App\Services\CustomerSupportChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Public JSON endpoints for the floating AI Customer Support Chatbot widget,
 * including real-time typing presence heartbeats streamed to the Admin Dashboard.
 */
class SupportChatController extends Controller
{
    public function __construct(private CustomerSupportChatService $supportChats) {}

    /**
     * Hydrate existing visitor session (or return initial greeting without creating DB rows).
     */
    public function session(Request $request): JsonResponse
    {
        if (! $this->supportChats->isEnabled()) {
            return response()->json(['ok' => false, 'enabled' => false], 404);
        }

        $session = $this->supportChats->findByToken($request->query('token'));

        return response()->json([
            'ok' => true,
            'enabled' => true,
            'session' => $this->supportChats->serializeSession($session),
        ]);
    }

    /**
     * Record a live typing heartbeat from the visitor so the Admin Dashboard
     * displays "Customer is typing…" in real time.
     */
    public function typing(Request $request): JsonResponse
    {
        if (! $this->supportChats->isEnabled()) {
            return response()->json(['ok' => false], 404);
        }

        $validated = $request->validate([
            'token' => ['nullable', 'string', 'max:64'],
            'is_typing' => ['required', 'boolean'],
            'preview' => ['nullable', 'string', 'max:160'],
            'landing_page' => ['nullable', 'string', 'max:250'],
        ]);

        $isTyping = (bool) $validated['is_typing'];
        $preview = (string) ($validated['preview'] ?? '');
        $session = $this->supportChats->findByToken($validated['token'] ?? null);

        if (! $session && $isTyping && mb_strlen(trim($preview)) >= 2) {
            $session = $this->supportChats->createSession(
                $request->ip(),
                $request->userAgent(),
                $validated['landing_page'] ?? null,
            );
        }

        if ($session && ! $session->isClosed()) {
            $this->supportChats->markTyping($session, 'customer', $isTyping, $preview, 6);
        }

        return response()->json([
            'ok' => true,
            'token' => $session?->token,
            'presence' => $this->supportChats->presenceFor($session),
        ]);
    }

    /**
     * Process a visitor message through the CustomerSupportAgent.
     */
    public function message(SupportChatMessageRequest $request): JsonResponse
    {
        @set_time_limit(120);

        if (! $this->supportChats->isEnabled()) {
            return response()->json([
                'ok' => false,
                'message' => 'Support chat is currently offline.',
            ], 503);
        }

        $ip = (string) ($request->ip() ?: 'unknown');
        $minuteKey = 'support-chat:min:'.$ip;
        $dayKey = 'support-chat:day:'.$ip;
        $perMinute = (int) config('assistant.support.per_minute', 8);
        $perDay = (int) config('assistant.support.per_day', 150);

        if (RateLimiter::tooManyAttempts($minuteKey, $perMinute) || RateLimiter::tooManyAttempts($dayKey, $perDay)) {
            return response()->json([
                'ok' => false,
                'message' => 'You are sending messages too quickly. Please wait a moment and try again.',
            ], 429);
        }

        RateLimiter::hit($minuteKey, 60);
        RateLimiter::hit($dayKey, 86400);

        $validated = $request->validated();

        $payload = $this->supportChats->sendMessage(
            token: $validated['token'] ?? null,
            message: (string) $validated['message'],
            ip: $request->ip(),
            userAgent: $request->userAgent(),
            landingPage: $validated['landing_page'] ?? null,
        );

        return response()->json([
            'ok' => true,
            'session' => $payload,
        ]);
    }
}
