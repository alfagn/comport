<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessIncomingWhatsappMessage;
use App\Services\WhatsappCloudApiService;
use App\Services\WhatsappCustomerSupportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Official Meta WhatsApp Business Cloud API webhook endpoint.
 *
 * - GET  /api/webhooks/whatsapp : Meta verification challenge (`hub.verify_token`)
 * - POST /api/webhooks/whatsapp : Inbound messages, media, reactions & delivery/read statuses
 */
class WhatsappWebhookController extends Controller
{
    public function __construct(
        private WhatsappCloudApiService $whatsapp,
        private WhatsappCustomerSupportService $whatsappSupport,
    ) {}

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
     * Receive inbound WhatsApp messages, media, reactions & status updates from Meta.
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

        $payload = $request->all();

        $statusUpdates = $this->whatsapp->extractStatusUpdates($payload);
        $statusesProcessed = $statusUpdates !== []
            ? $this->whatsappSupport->handleStatusUpdates($statusUpdates)
            : 0;

        $messages = $this->whatsapp->extractIncomingMessages($payload);
        $useQueue = (bool) config('assistant.whatsapp.queue_webhooks', false);

        foreach ($messages as $item) {
            $job = new ProcessIncomingWhatsappMessage(
                waId: $item['wa_id'],
                profileName: $item['profile_name'],
                messageId: $item['message_id'],
                text: $item['text'],
                type: $item['type'] ?? 'text',
                replyToWamid: $item['reply_to_wamid'] ?? null,
                media: $item['media'] ?? null,
                location: $item['location'] ?? null,
                contacts: $item['contacts'] ?? null,
                reaction: $item['reaction'] ?? null,
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
            'statuses_updated' => $statusesProcessed,
        ]);
    }
}
