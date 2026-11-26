<?php

namespace Tests\Feature;

use App\Ai\Agents\CustomerSupportAgent;
use App\Ai\Tools\Dashboard\SendWhatsappCustomerMessage;
use App\Ai\Tools\Support\SubmitProjectInquiry;
use App\Mail\UnreadWhatsappMessageMail;
use App\Models\ContactMessage;
use App\Models\Setting;
use App\Models\SupportChatSession;
use App\Models\WhatsappMessage;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpClientRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Tools\Request as ToolRequest;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class WhatsappCustomerApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Setting::setValue('whatsapp_enabled', '1', 'whatsapp');
        Setting::setValue('whatsapp_phone_number_id', '109988776655', 'whatsapp');
        Setting::setValue('whatsapp_access_token', 'test-meta-bearer-token', 'whatsapp');
        Setting::setValue('whatsapp_verify_token', 'my-secret-verify-token', 'whatsapp');
        Setting::setValue('whatsapp_api_version', 'v22.0', 'whatsapp');
    }

    public function test_meta_webhook_verification_handshake(): void
    {
        $this->get('/api/webhooks/whatsapp?hub.mode=subscribe&hub.verify_token=wrong-token&hub.challenge=987654')
            ->assertForbidden();

        $this->get('/api/webhooks/whatsapp?hub.mode=subscribe&hub.verify_token=my-secret-verify-token&hub.challenge=987654')
            ->assertOk()
            ->assertSee('987654');
    }

    public function test_webhook_enforces_hmac_signature_when_app_secret_is_configured(): void
    {
        Setting::setValue('whatsapp_app_secret', 'meta-app-secret-key', 'whatsapp');

        $payload = json_encode(['object' => 'whatsapp_business_account', 'entry' => []], JSON_THROW_ON_ERROR);

        // Missing or invalid signature -> 401
        $this->call('POST', '/api/webhooks/whatsapp', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256=invalid',
        ], $payload)->assertStatus(401);

        // Valid HMAC-SHA256 signature -> 200
        $validSig = 'sha256='.hash_hmac('sha256', $payload, 'meta-app-secret-key');
        $this->call('POST', '/api/webhooks/whatsapp', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_HUB_SIGNATURE_256' => $validSig,
        ], $payload)->assertOk()->assertJsonPath('ok', true);
    }

    public function test_inbound_whatsapp_message_triggers_ai_agent_and_replies_via_cloud_api_with_idempotency(): void
    {
        Http::fake([
            'https://graph.facebook.com/*' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.OUTBOUND1']],
            ], 200),
        ]);

        CustomerSupportAgent::fake([
            'أهلاً يا عمر! نقدم تطوير **منصات الويب** وتطبيقات الموبايل. التفاصيل هنا: [أعمالنا](https://empetix.com/work)',
        ]);

        $webhookPayload = [
            'object' => 'whatsapp_business_account',
            'entry' => [[
                'id' => 'WABA_1',
                'changes' => [[
                    'field' => 'messages',
                    'value' => [
                        'messaging_product' => 'whatsapp',
                        'contacts' => [[
                            'wa_id' => '201099887766',
                            'profile' => ['name' => 'Omar Khaled'],
                        ]],
                        'messages' => [[
                            'from' => '201099887766',
                            'id' => 'wamid.INBOUND_001',
                            'timestamp' => (string) time(),
                            'type' => 'text',
                            'text' => ['body' => 'مرحبا، ما هي خدماتكم؟'],
                        ]],
                    ],
                ]],
            ]],
        ];

        $this->postJson('/api/webhooks/whatsapp', $webhookPayload)
            ->assertOk()
            ->assertJsonPath('processed', 1);

        $session = SupportChatSession::whatsapp()->where('external_thread_id', '201099887766')->firstOrFail();
        $this->assertSame('+201099887766', $session->visitor_phone);
        $this->assertSame('Omar Khaled', $session->visitor_name);
        $this->assertSame(1, $session->message_count);

        // Verify Meta Graph API received both read receipt and formatted text reply
        Http::assertSent(function (HttpClientRequest $req): bool {
            return str_contains($req->url(), '/v22.0/109988776655/messages')
                && ($req['type'] ?? null) === 'text'
                && ($req['to'] ?? null) === '201099887766'
                && str_contains((string) ($req['text']['body'] ?? ''), '*منصات الويب*')
                && str_contains((string) ($req['text']['body'] ?? ''), 'https://empetix.com/work');
        });

        // Duplicate webhook retry with same wamid must not send a second reply
        $this->postJson('/api/webhooks/whatsapp', $webhookPayload)->assertOk();
        $this->assertSame(1, $session->fresh()->message_count);
    }

    public function test_human_takeover_outbound_template_whatsapp_handoff_and_copilot_tool(): void
    {
        Http::fake([
            'https://graph.facebook.com/*' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.OUTBOUND2']],
            ], 200),
        ]);

        $owner = $this->owner();

        // 1. Admin initiates outbound template message (even using local 011... format -> normalized to 2011...)
        $this->actingAs($owner)->post('/admin/support-chats/whatsapp/send', [
            'phone' => '0111 222 3333',
            'customer_name' => 'Mariam Ali',
            'mode' => 'template',
            'content' => 'Hello World',
            'template_language' => 'en_US',
        ])->assertRedirect();

        $session = SupportChatSession::whatsapp()->where('external_thread_id', '201112223333')->firstOrFail();

        // 2. Admin sends direct human reply (pausing AI auto-reply)
        $this->actingAs($owner)->post("/admin/support-chats/{$session->id}/reply", [
            'message' => 'أهلاً أستاذة مريم، معك فريق الهندسة في Empetix.',
            'pause_ai' => '1',
        ])->assertRedirect();

        $this->assertTrue($session->fresh()->ai_paused);

        // Inbound message while ai_paused=true records message without invoking AI
        CustomerSupportAgent::fake()->preventStrayPrompts();

        $this->postJson('/api/webhooks/whatsapp', [
            'entry' => [[
                'changes' => [[
                    'value' => [
                        'contacts' => [['wa_id' => '201112223333', 'profile' => ['name' => 'Mariam Ali']]],
                        'messages' => [[
                            'from' => '201112223333',
                            'id' => 'wamid.PAUSED_MSG_1',
                            'type' => 'text',
                            'text' => ['body' => 'شكراً لتواصلكم السريع!'],
                        ]],
                    ],
                ]],
            ]],
        ])->assertOk();

        CustomerSupportAgent::assertNeverPrompted();

        // 3. WhatsApp lead handoff works even without customer email
        $handoff = new SubmitProjectInquiry($session->fresh());
        $handoffResult = json_decode((string) $handoff->handle(new ToolRequest([
            'name' => 'Mariam Ali',
            'summary' => 'تريد بناء منصة تجارة إلكترونية وتطبيق Flutter.',
        ])), true);

        $this->assertTrue($handoffResult['ok']);
        $brief = ContactMessage::query()->findOrFail($handoffResult['reference_id']);
        $this->assertStringContainsString('+201112223333', (string) $brief->brief);

        // 4. Dashboard Copilot SendWhatsappCustomerMessage tool requires approval and sends cleanly
        $copilotTool = new SendWhatsappCustomerMessage($owner);
        $this->assertNotNull($copilotTool->shouldRequestApproval(new ToolRequest([
            'phone' => '+201112223333',
            'content' => 'تم استلام طلبكم.',
        ])));

        $toolJson = json_decode((string) $copilotTool->handle(new ToolRequest([
            'phone' => '+201112223333',
            'mode' => 'text',
            'content' => 'تم استلام طلبكم وسيكلمكم مهندس الحلول اليوم.',
        ])), true);

        $this->assertSame('WhatsApp message sent.', $toolJson['result']);
    }

    public function test_dedicated_whatsapp_inbox_handles_media_voice_notes_locations_reactions_statuses_and_stays_separate_from_email_inbox(): void
    {
        Storage::fake('local');
        Mail::fake();

        Http::fake([
            'https://graph.facebook.com/v22.0/MEDIA_IMG_1' => Http::response([
                'url' => 'https://lookaside.fbsbx.com/whatsapp_business/attachments/img1',
                'mime_type' => 'image/jpeg',
            ], 200),
            'https://lookaside.fbsbx.com/*' => Http::response('FAKE_JPEG_BINARY_DATA', 200),
            'https://graph.facebook.com/v22.0/109988776655/media' => Http::response([
                'id' => 'UPLOADED_MEDIA_999',
            ], 200),
            'https://graph.facebook.com/v22.0/109988776655/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.OUT_MEDIA_1']],
            ], 200),
        ]);

        CustomerSupportAgent::fake(['وصلت الصورة، شكراً لك!']);

        // 1. Customer sends an inbound photo on WhatsApp -> stored in whatsapp_messages & local media disk, NOT in contact_messages (Email Inbox)
        $this->postJson('/api/webhooks/whatsapp', [
            'entry' => [[
                'changes' => [[
                    'value' => [
                        'contacts' => [['wa_id' => '201234567890', 'profile' => ['name' => 'Youssef Samir']]],
                        'messages' => [[
                            'from' => '201234567890',
                            'id' => 'wamid.IN_PHOTO_1',
                            'type' => 'image',
                            'image' => [
                                'id' => 'MEDIA_IMG_1',
                                'mime_type' => 'image/jpeg',
                                'caption' => 'صورة تصميم الواجهة',
                            ],
                        ]],
                    ],
                ]],
            ]],
        ])->assertOk()->assertJsonPath('processed', 1);

        $this->assertDatabaseCount('contact_messages', 0);

        $session = SupportChatSession::whatsapp()->where('external_thread_id', '201234567890')->firstOrFail();
        $this->assertSame(1, $session->unread_count);

        $inboundPhoto = WhatsappMessage::query()
            ->where('support_chat_session_id', $session->id)
            ->where('wa_message_id', 'wamid.IN_PHOTO_1')
            ->firstOrFail();

        $this->assertSame('image', $inboundPhoto->type);
        $this->assertNotNull($inboundPhoto->media_path);
        Storage::disk('local')->assertExists($inboundPhoto->media_path);

        // 2. Opening /admin/whatsapp and viewing the conversation marks unread_count = 0 and streams media securely
        $owner = $this->owner();
        $this->actingAs($owner)->get('/admin/whatsapp')->assertOk()->assertSee('Empetix WhatsApp Business Studio');

        $showJson = $this->actingAs($owner)
            ->getJson("/admin/whatsapp/conversations/{$session->id}")
            ->assertOk()
            ->json();

        $this->assertSame(0, $showJson['active_chat']['contact']['unread_count']);
        $this->assertSame(0, $session->fresh()->unread_count);

        $this->actingAs($owner)
            ->get("/admin/whatsapp/media/{$inboundPhoto->id}")
            ->assertOk();

        // 3. Admin sends a recorded voice note, a photo attachment with quoted reply, a location pin, and an emoji reaction
        $voiceBlob = UploadedFile::fake()->createWithContent('voice-note.ogg', 'FAKE_OGG_OPUS_AUDIO');
        $this->actingAs($owner)->post("/admin/whatsapp/conversations/{$session->id}/send", [
            'mode' => 'voice',
            'voice_note' => $voiceBlob,
            'duration' => 9,
            'reply_to_wamid' => 'wamid.IN_PHOTO_1',
        ], ['Accept' => 'application/json'])->assertOk();

        $sentVoice = WhatsappMessage::query()
            ->where('support_chat_session_id', $session->id)
            ->where('type', 'voice')
            ->firstOrFail();
        $this->assertSame(9, $sentVoice->media_duration);
        $this->assertSame('wamid.IN_PHOTO_1', $sentVoice->reply_to_wa_message_id);

        $this->actingAs($owner)->postJson("/admin/whatsapp/conversations/{$session->id}/send", [
            'mode' => 'location',
            'latitude' => 30.0444,
            'longitude' => 31.2357,
            'location_name' => 'Empetix HQ',
        ])->assertOk();

        $this->actingAs($owner)->postJson("/admin/whatsapp/conversations/{$session->id}/messages/{$inboundPhoto->id}/react", [
            'emoji' => '❤️',
        ])->assertOk()->assertJsonPath('reaction_emoji', '❤️');

        // 4. Meta webhook status callback ('read') updates outbound checkmark status to read
        $this->postJson('/api/webhooks/whatsapp', [
            'entry' => [[
                'changes' => [[
                    'value' => [
                        'statuses' => [[
                            'id' => 'wamid.OUT_MEDIA_1',
                            'status' => 'read',
                            'recipient_id' => '201234567890',
                        ]],
                    ],
                ]],
            ]],
        ])->assertOk();

        $this->assertSame('read', $sentVoice->fresh()->status);
        $this->assertNotNull($sentVoice->fresh()->read_at);

        // 5. Deleting conversation purges stored WhatsApp media files from disk
        $mediaPathToVerify = $inboundPhoto->media_path;
        $this->actingAs($owner)->deleteJson("/admin/whatsapp/conversations/{$session->id}")->assertOk();
        Storage::disk('local')->assertMissing($mediaPathToVerify);
    }

    public function test_unseen_whatsapp_message_sends_email_alert_while_actively_viewed_chat_does_not(): void
    {
        Mail::fake();
        Http::fake([
            'https://graph.facebook.com/*' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.AI_OUT_1']],
            ], 200),
        ]);
        CustomerSupportAgent::fake(['أهلاً بك!']);

        Setting::setValue('whatsapp_notify_unread_email', '1', 'whatsapp');
        Setting::setValue('whatsapp_notify_email', 'alerts@empetix.com', 'whatsapp');
        Setting::setValue('whatsapp_unread_delay_seconds', '60', 'whatsapp');

        // Case 1: No admin is online in WhatsApp Inbox -> Unread email alert sends immediately
        Cache::forget('whatsapp:admin_online_any');

        $this->postJson('/api/webhooks/whatsapp', [
            'entry' => [[
                'changes' => [[
                    'value' => [
                        'contacts' => [['wa_id' => '201555000111', 'profile' => ['name' => 'Hana Mahmoud']]],
                        'messages' => [[
                            'from' => '201555000111',
                            'id' => 'wamid.UNSEEN_1',
                            'type' => 'text',
                            'text' => ['body' => 'مرحبا، أحتاج عرض سعر لتطبيق موبايل'],
                        ]],
                    ],
                ]],
            ]],
        ])->assertOk();

        Mail::assertSent(UnreadWhatsappMessageMail::class, function (UnreadWhatsappMessageMail $mail): bool {
            return $mail->hasTo('alerts@empetix.com')
                && $mail->session->visitor_name === 'Hana Mahmoud';
        });

        // Case 2: Admin IS actively viewing the exact chat -> message marked read immediately & NO email sent
        Mail::fake();
        $session = SupportChatSession::whatsapp()->where('external_thread_id', '201555000111')->firstOrFail();
        Cache::put('whatsapp:admin_online_any', true, now()->addSeconds(30));
        Cache::put("whatsapp:admin_viewing:{$session->id}", true, now()->addSeconds(30));

        $this->postJson('/api/webhooks/whatsapp', [
            'entry' => [[
                'changes' => [[
                    'value' => [
                        'contacts' => [['wa_id' => '201555000111', 'profile' => ['name' => 'Hana Mahmoud']]],
                        'messages' => [[
                            'from' => '201555000111',
                            'id' => 'wamid.SEEN_LIVE_2',
                            'type' => 'text',
                            'text' => ['body' => 'أنا متواجدة الآن معكم'],
                        ]],
                    ],
                ]],
            ]],
        ])->assertOk();

        $this->assertSame(0, $session->fresh()->unread_count);
        Mail::assertNothingSent();
    }
}
