<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessIncomingWhatsappMessage;
use App\Services\WhatsappCloudApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Official Meta WhatsApp Business Cloud API webhook endpoint.
 *
 * - GET  /api/webhooks/whatsapp : Meta verification challenge (`hub.verify_token`)
 * - POST /api/webhooks/whatsapp : Inbound customer messages (`X-Hub-Signature-256`)
 */
class WhatsappWebhookController extends Controller
{
    public function __construct(private WhatsappCloudApiService $whatsapp) {}

    /**
     * Respond to Meta's webhook verification handshake.
     */
    public function verify(Request $request): Response
    {
        $mode = $request->query('hub_mode') ?? $request->query('hub.mode');
        $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');

        $expectedTokens = array_values(array_filter([
            $this->whatsapp->verifyToken(),
            config('assistant.whatsapp.verify_token'),
        ]));

        if ($mode === 'subscribe' && is_string($token) && $token !== '') {
            foreach ($expectedTokens as $expected) {
                if (hash_equals((string) $expected, $token)) {
                    return response((string) $challenge, 200)->header('Content-Type', 'text/plain');
                }
            }
        }

        return response('Forbidden: invalid verify token.', 403);
    }

    /**
     * Receive inbound WhatsApp events from Meta and dispatch AI/support handling.
     */
    public function handle(Request $request): JsonResponse
    {
        @set_time_limit(120);

        Log::info('WhatsApp webhook hit', [
            'object' => $request->input('object'),
            'has_signature' => $request->hasHeader('X-Hub-Signature-256'),
        ]);

        if (! $this->whatsapp->verifyWebhookSignature($request->getContent(), $request->header('X-Hub-Signature-256'))) {
            Log::warning('WhatsApp webhook rejected due to signature mismatch');

            return response()->json([
                'ok' => false,
                'message' => 'Invalid webhook signature.',
            ], 401);
        }

        if (! $this->whatsapp->isEnabled()) {
            return response()->json([
                'ok' => true,
                'ignored' => 'whatsapp_disabled',
            ]);
        }

        $messages = $this->whatsapp->extractIncomingMessages($request->all());

        $useQueue = (bool) config('assistant.whatsapp.queue_webhooks', false);

        foreach ($messages as $item) {
            $job = new ProcessIncomingWhatsappMessage(
                waId: $item['wa_id'],
                profileName: $item['profile_name'],
                messageId: $item['message_id'],
                text: $item['text'],
            );

            if ($useQueue) {
                dispatch($job);
            } else {
                dispatch_sync($job);
            }
        }

        return response()->json([
            'ok' => true,
            'processed' => count($messages),
        ]);
    }
}
