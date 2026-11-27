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
use Illuminate\Support\Facades\DB;
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

        config(['assistant.whatsapp.app_secret' => null]);
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

    public function test_whatsapp_inbox_enforces_idempotency_aes256_encryption_webm_voice_remux_and_contact_profiles(): void
    {
        Storage::fake('local');

        Http::fake([
            'https://graph.facebook.com/v22.0/109988776655/media' => Http::response([
                'id' => 'UPLOADED_OGG_VOICE_1',
            ], 200),
            'https://graph.facebook.com/v22.0/109988776655/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.IDEMPOTENT_OUT_1']],
            ], 200),
        ]);

        CustomerSupportAgent::fake()->preventStrayPrompts();
        $owner = $this->owner();

        // 1. Inbound message with @username while AI is paused -> increments unread_count and stores AES-256 encrypted body
        $session = SupportChatSession::query()->create([
            'token' => 'wa-test-token-1',
            'channel' => SupportChatSession::CHANNEL_WHATSAPP,
            'external_thread_id' => '201150949593',
            'visitor_phone' => '+201150949593',
            'ai_paused' => true,
            'status' => SupportChatSession::STATUS_OPEN,
        ]);

        $this->postJson('/api/webhooks/whatsapp', [
            'entry' => [[
                'changes' => [[
                    'value' => [
                        'contacts' => [[
                            'wa_id' => '201150949593',
                            'profile' => [
                                'name' => 'Mohanad',
                                'username' => 'mohanad_empetix',
                            ],
                        ]],
                        'messages' => [[
                            'from' => '201150949593',
                            'id' => 'wamid.ENC_IN_1',
                            'type' => 'text',
                            'text' => ['body' => 'رسالة سرية جداً للمشروع'],
                        ]],
                    ],
                ]],
            ]],
        ])->assertOk();

        $session->refresh();
        $this->assertSame(1, $session->unread_count);
        $this->assertSame('mohanad_empetix', $session->visitor_username);

        // Verify raw database values are AES-256 encrypted (`enc:v1:...`), never plaintext
        $rawMsgBody = (string) DB::table('whatsapp_messages')
            ->where('wa_message_id', 'wamid.ENC_IN_1')
            ->value('body');
        $rawPreview = (string) DB::table('support_chat_sessions')
            ->where('id', $session->id)
            ->value('last_message_preview');

        $this->assertStringStartsWith('enc:v1:', $rawMsgBody);
        $this->assertStringNotContainsString('رسالة سرية', $rawMsgBody);
        $this->assertStringStartsWith('enc:v1:', $rawPreview);

        // Encrypted search in poll endpoint still finds the contact by decrypted message content & exposes @username + formatted phone
        $pollRes = $this->actingAs($owner)
            ->getJson('/admin/whatsapp/poll?q='.urlencode('رسالة سرية'))
            ->assertOk()
            ->json();

        $this->assertCount(1, $pollRes['contacts']);
        $this->assertSame('@mohanad_empetix', $pollRes['contacts'][0]['username']);
        $this->assertSame('+20 11 5094 9593', $pollRes['contacts'][0]['phone']);
        $this->assertSame(1, $pollRes['contacts'][0]['unread_count']);

        // 2. Double-clicking Send with the same idempotency_key sends ONLY ONE message to Meta
        $idemKey = 'wa_double_click_key_001';

        $firstSend = $this->actingAs($owner)->postJson("/admin/whatsapp/conversations/{$session->id}/send", [
            'message' => 'تم استلام الرسالة فوراً',
            'idempotency_key' => $idemKey,
        ], ['X-Idempotency-Key' => $idemKey])->assertOk()->json();

        $secondSend = $this->actingAs($owner)->postJson("/admin/whatsapp/conversations/{$session->id}/send", [
            'message' => 'تم استلام الرسالة فوراً',
            'idempotency_key' => $idemKey,
        ], ['X-Idempotency-Key' => $idemKey])->assertOk()->json();

        $this->assertArrayNotHasKey('duplicate', $firstSend);
        $this->assertTrue($secondSend['duplicate']);
        $this->assertSame(1, WhatsappMessage::query()->where('idempotency_key', $idemKey)->count());

        // 3. Browser WebM Opus voice recording (`\x1A\x45\xDF\xA3`) remuxes cleanly to `OggS` (`audio/ogg`) with encrypted disk storage
        $opusTocPacket = "\xFC\x01\x02\x03\x04\x05\x06\x07"; // 20ms CELT FB mono Opus packet
        $simpleBlockPayload = "\x81\x00\x00\x80".$opusTocPacket;
        $minimalWebmOpus = "\x1A\x45\xDF\xA3\x84\x42\x86\x81\x01" // EBML header
            ."\x18\x53\x80\x67\x01\xFF\xFF\xFF\xFF\xFF\xFF\xFF"   // Segment (streaming size)
            ."\x1F\x43\xB6\x75\x01\xFF\xFF\xFF\xFF\xFF\xFF\xFF"   // Cluster (streaming size)
            ."\xA3".chr(0x80 | strlen($simpleBlockPayload)).$simpleBlockPayload;

        $webmVoiceFile = UploadedFile::fake()->createWithContent('voice-note.webm', $minimalWebmOpus);

        $this->actingAs($owner)->post("/admin/whatsapp/conversations/{$session->id}/send", [
            'mode' => 'voice',
            'voice_note' => $webmVoiceFile,
            'duration' => 10,
            'idempotency_key' => 'wa_voice_webm_001',
        ], ['Accept' => 'application/json'])->assertOk();

        $voiceMsg = WhatsappMessage::query()->where('idempotency_key', 'wa_voice_webm_001')->firstOrFail();
        $this->assertSame('audio/ogg', $voiceMsg->media_mime);
        $this->assertSame('sent', $voiceMsg->status);

        // Stored media file on disk is AES-256 encrypted (`EMPWAENC1:`), while streaming endpoint returns valid decrypted `OggS` bytes & supports HTTP Range
        $rawDiskBytes = Storage::disk('local')->get($voiceMsg->media_path);
        $this->assertStringStartsWith('EMPWAENC1:', (string) $rawDiskBytes);

        $streamRes = $this->actingAs($owner)->get("/admin/whatsapp/media/{$voiceMsg->id}")->assertOk();
        $this->assertStringStartsWith('OggS', $streamRes->getContent());

        $rangeRes = $this->actingAs($owner)
            ->withHeaders(['Range' => 'bytes=0-3'])
            ->get("/admin/whatsapp/media/{$voiceMsg->id}")
            ->assertStatus(206);
        $this->assertSame('OggS', $rangeRes->getContent());

        // Verify Meta received `voice: true` on the converted `audio/ogg` payload
        Http::assertSent(function (HttpClientRequest $req): bool {
            return str_contains($req->url(), '/messages')
                && ($req['type'] ?? null) === 'audio'
                && ($req['audio']['voice'] ?? false) === true
                && ($req['audio']['id'] ?? null) === 'UPLOADED_OGG_VOICE_1';
        });

        // 4. Uploading contact profile photo encrypts on disk and exposes `avatar_url`
        $avatarImg = UploadedFile::fake()->image('client-avatar.jpg', 120, 120);
        $contactUpdate = $this->actingAs($owner)->post("/admin/whatsapp/conversations/{$session->id}", [
            'avatar' => $avatarImg,
            'contact_notes' => 'عميل VIP مهتم بمنصة SaaS',
        ], ['Accept' => 'application/json'])->assertOk()->json();

        $this->assertNotEmpty($contactUpdate['contact']['avatar_url']);
        $this->actingAs($owner)->get("/admin/whatsapp/conversations/{$session->id}/avatar")->assertOk();
    }

    public function test_bsuid_username_only_replies_use_recipient_field_and_archive_plus_mark_unread_work(): void
    {
        Http::fake([
            'https://graph.facebook.com/*' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.BSUID_OUT_1']],
            ], 200),
        ]);

        CustomerSupportAgent::fake(['أهلاً يا أحمد! كيف أساعدك؟']);
        $owner = $this->owner();

        // 1. Customer with hidden phone number sends inbound message via BSUID (`EG.2124566744990207`)
        $this->postJson('/api/webhooks/whatsapp', [
            'entry' => [[
                'changes' => [[
                    'value' => [
                        'contacts' => [[
                            'wa_id' => '',
                            'user_id' => 'EG.2124566744990207',
                            'profile' => [
                                'name' => 'أحمد',
                                'username' => 'ahmed_dev',
                            ],
                        ]],
                        'messages' => [[
                            'from' => 'EG.2124566744990207',
                            'user_id' => 'EG.2124566744990207',
                            'id' => 'wamid.BSUID_IN_1',
                            'type' => 'text',
                            'text' => ['body' => 'اهلا'],
                        ]],
                    ],
                ]],
            ]],
        ])->assertOk()->assertJsonPath('processed', 1);

        $session = SupportChatSession::whatsapp()->where('external_thread_id', 'EG.2124566744990207')->firstOrFail();
        $this->assertNull($session->visitor_phone);
        $this->assertSame('ahmed_dev', $session->visitor_username);

        // Verify Meta Graph API received `"recipient": "EG.2124566744990207"` (NOT `"to": "2124566744990207"`)
        Http::assertSent(function (HttpClientRequest $req): bool {
            return str_contains($req->url(), '/messages')
                && ($req['type'] ?? null) === 'text'
                && ($req['recipient'] ?? null) === 'EG.2124566744990207'
                && ! isset($req['to']);
        });

        // 2. Right-click Archive moves chat out of main list into `filter=archived` and updates `archived_count`
        $this->actingAs($owner)->patchJson("/admin/whatsapp/conversations/{$session->id}", [
            'is_archived' => true,
        ])->assertOk()->assertJsonPath('archived_count', 1);

        $mainPoll = $this->actingAs($owner)->getJson('/admin/whatsapp/poll?filter=all')->assertOk()->json();
        $this->assertCount(0, $mainPoll['contacts']);
        $this->assertSame(1, $mainPoll['archived_count']);

        $archPoll = $this->actingAs($owner)->getJson('/admin/whatsapp/poll?filter=archived')->assertOk()->json();
        $this->assertCount(1, $archPoll['contacts']);

        // 3. Right-click Mark as unread & Unarchive + Delete single message
        $this->actingAs($owner)->patchJson("/admin/whatsapp/conversations/{$session->id}", [
            'is_archived' => false,
            'mark_unread' => true,
        ])->assertOk()
            ->assertJsonPath('archived_count', 0)
            ->assertJsonPath('contact.unread_count', 1);

        $firstMsg = WhatsappMessage::query()->where('support_chat_session_id', $session->id)->firstOrFail();
        $this->actingAs($owner)->deleteJson("/admin/whatsapp/conversations/{$session->id}/messages/{$firstMsg->id}")
            ->assertOk();
        $this->assertDatabaseMissing('whatsapp_messages', ['id' => $firstMsg->id]);
    }
}
