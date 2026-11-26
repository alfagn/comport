<?php

namespace Tests\Feature;

use App\Ai\Agents\CustomerSupportAgent;
use App\Ai\Tools\Support\GetCompanyOverview;
use App\Ai\Tools\Support\ListOpenCareers;
use App\Ai\Tools\Support\SearchPortfolioAndInsights;
use App\Ai\Tools\Support\SubmitProjectInquiry;
use App\Models\ContactMessage;
use App\Models\JobPost;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\SupportChatSession;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Tools\Request as ToolRequest;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CustomerSupportChatbotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_public_widget_renders_when_enabled_and_hides_when_disabled(): void
    {
        Setting::setValue('ai_support_enabled', '1', 'ai');
        Setting::setValue('ai_support_greeting', 'Welcome to Empetix AI Concierge!', 'ai');
        Setting::setValue('whatsapp_public_number', '+20 115 094 9593', 'whatsapp');

        $this->get('/')
            ->assertOk()
            ->assertSee('id="empetix-support-widget"', false)
            ->assertSee('emp-dock-btn--ai', false)
            ->assertSee('emp-dock-btn--whatsapp', false)
            ->assertSee('https://wa.me/201150949593', false)
            ->assertSee('Welcome to Empetix AI Concierge!')
            ->assertSee('support-chatbot.js', false);

        // Session hydration does not create empty DB rows before the first message
        $this->getJson('/support-chat/session')
            ->assertOk()
            ->assertJsonPath('session.greeting', 'Welcome to Empetix AI Concierge!');
        $this->assertDatabaseCount('support_chat_sessions', 0);

        // Disabling via dashboard setting hides AI widget while keeping public WhatsApp icon
        Setting::setValue('ai_support_enabled', '0', 'ai');

        $this->get('/')
            ->assertOk()
            ->assertDontSee('id="empetix-support-widget"', false)
            ->assertSee('emp-dock-btn--whatsapp', false);
        $this->getJson('/support-chat/session')->assertNotFound();
        $this->postJson('/support-chat/message', ['message' => 'Hello'])->assertStatus(503);
    }

    public function test_visitor_can_chat_across_multiple_turns_with_persistent_token(): void
    {
        Setting::setValue('ai_support_enabled', '1', 'ai');

        CustomerSupportAgent::fake([
            'We build enterprise SaaS, mobile apps, and AI systems.',
            'Great! Please share your name and email so our engineering leads can follow up.',
        ]);

        $turn1 = $this->postJson('/support-chat/message', [
            'message' => 'What services do you provide?',
            'landing_page' => '/work',
        ])->assertOk()->json('session');

        $token = $turn1['token'];
        $this->assertNotEmpty($token);
        $this->assertCount(2, $turn1['messages']);

        $turn2 = $this->postJson('/support-chat/message', [
            'message' => 'I want to build a logistics platform.',
            'token' => $token,
        ])->assertOk()->json('session');

        $this->assertSame($token, $turn2['token']);
        $this->assertCount(4, $turn2['messages']);

        $session = SupportChatSession::query()->where('token', $token)->firstOrFail();
        $this->assertSame(2, $session->message_count);
        $this->assertSame('/work', $session->landing_page);
    }

    public function test_support_tools_expose_only_published_data_and_handoff_creates_inbox_brief(): void
    {
        Service::query()->create([
            'num' => '01',
            'title' => 'AI Automation & Agents',
            'slug' => 'ai-automation',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Service::query()->create([
            'num' => '02',
            'title' => 'Stealth Internal Lab',
            'slug' => 'stealth-lab',
            'is_active' => false,
            'sort_order' => 2,
        ]);

        Project::query()->create([
            'title' => 'Public FinTech Core',
            'slug' => 'public-fintech-core',
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);
        Project::query()->create([
            'title' => 'Hidden NDA Project',
            'slug' => 'hidden-nda-project',
            'is_active' => false,
            'is_featured' => false,
            'sort_order' => 2,
        ]);

        Post::query()->create([
            'title' => 'Published Architecture Guide',
            'slug' => 'published-guide',
            'type' => 'article',
            'is_published' => true,
            'published_at' => now(),
        ]);
        Post::query()->create([
            'title' => 'Secret Draft Post',
            'slug' => 'secret-draft',
            'type' => 'article',
            'is_published' => false,
        ]);

        JobPost::query()->create([
            'title' => 'Senior Flutter Engineer',
            'slug' => 'senior-flutter',
            'department' => 'Mobile',
            'location' => 'Cairo',
            'type' => 'Full-time',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        JobPost::query()->create([
            'title' => 'Paused Role',
            'slug' => 'paused-role',
            'department' => 'Ops',
            'location' => 'Cairo',
            'type' => 'Full-time',
            'is_active' => false,
            'sort_order' => 2,
        ]);

        $overview = (string) (new GetCompanyOverview)->handle(new ToolRequest);
        $this->assertStringContainsString('AI Automation & Agents', $overview);
        $this->assertStringNotContainsString('Stealth Internal Lab', $overview);

        $portfolio = (string) (new SearchPortfolioAndInsights)->handle(new ToolRequest);
        $this->assertStringContainsString('Public FinTech Core', $portfolio);
        $this->assertStringNotContainsString('Hidden NDA Project', $portfolio);
        $this->assertStringContainsString('Published Architecture Guide', $portfolio);
        $this->assertStringNotContainsString('Secret Draft Post', $portfolio);

        $careers = (string) (new ListOpenCareers)->handle(new ToolRequest);
        $this->assertStringContainsString('Senior Flutter Engineer', $careers);
        $this->assertStringNotContainsString('Paused Role', $careers);

        // Lead handoff creates Inbox ContactMessage & updates SupportChatSession
        $session = SupportChatSession::factory()->create();
        $handoff = new SubmitProjectInquiry($session);

        $result = json_decode((string) $handoff->handle(new ToolRequest([
            'name' => 'Salma Tarek',
            'email' => 'salma@enterprise.io',
            'company' => 'Enterprise IO',
            'services' => ['AI Automation & Agents'],
            'budget_tier' => '$25k–$50k',
            'timeline' => 'Q2',
            'summary' => 'Need a bilingual customer support agent and ERP integration.',
        ])), true);

        $this->assertTrue($result['ok']);
        $session->refresh();
        $this->assertSame(SupportChatSession::STATUS_HANDED_OFF, $session->status);
        $this->assertSame('Salma Tarek', $session->visitor_name);
        $this->assertNotNull($session->contact_message_id);
        // Handoff pauses AI auto-replies so a human takes over the chat
        $this->assertTrue((bool) $session->ai_paused);

        // Repeat call updates existing Inbox brief instead of creating duplicates
        $handoff->handle(new ToolRequest([
            'name' => 'Salma Tarek',
            'email' => 'salma@enterprise.io',
            'summary' => 'Updated scope: bilingual AI agent + ERP + WhatsApp webhook.',
        ]));
        $this->assertSame(1, ContactMessage::query()->count());
        $this->assertStringContainsString('WhatsApp webhook', (string) ContactMessage::query()->first()?->brief);
    }

    public function test_admin_can_manage_support_chats_and_update_ai_settings(): void
    {
        $unprivileged = User::factory()->create();
        $this->actingAs($unprivileged)->get('/admin/support-chats')->assertForbidden();

        $owner = $this->owner();
        CustomerSupportAgent::fake(['Hello from support!']);

        $payload = $this->postJson('/support-chat/message', ['message' => 'Hi team!'])
            ->assertOk()
            ->json('session');

        $session = SupportChatSession::query()->where('token', $payload['token'])->firstOrFail();

        // Index, dedicated sub-pages & AJAX DataTable
        $this->actingAs($owner)->get('/admin/support-chats')->assertOk()->assertSee('Support Conversations');
        $this->actingAs($owner)->get('/admin/support-chats/dispatch')->assertOk()->assertSee('Outbound WhatsApp Customer Dispatch');
        $this->actingAs($owner)->get('/admin/support-chats/whatsapp')->assertOk()->assertSee('Meta Graph API Credentials');
        $this->actingAs($owner)->get('/admin/support-chats/settings')->assertOk()->assertSee('AI Model &amp; Provider Routing', false);
        $this->actingAs($owner)->getJson('/admin/support-chats', ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonStructure(['data']);

        // Transcript view
        $this->actingAs($owner)->get("/admin/support-chats/{$session->id}")
            ->assertOk()
            ->assertSee('Hi team!')
            ->assertSee('Hello from support!');

        // Update AI Settings
        $this->actingAs($owner)->put('/admin/support-chats/settings', [
            'ai_support_enabled' => '1',
            'ai_support_greeting' => 'مرحبا بك في إمبتكس!',
            'ai_support_knowledge' => 'Discovery calls are free of charge.',
            'ai_support_provider' => 'gemini',
            'ai_dashboard_provider' => 'openai',
        ])->assertRedirect();

        $this->assertSame('مرحبا بك في إمبتكس!', Setting::getValue('ai_support_greeting'));
        $this->assertSame('gemini', Setting::getValue('ai_support_provider'));

        // Delete session removes conversation records too
        $convId = $session->conversation_id;
        $this->actingAs($owner)->delete("/admin/support-chats/{$session->id}")
            ->assertRedirect(route('admin.support-chats.index'));

        $this->assertDatabaseMissing('support_chat_sessions', ['id' => $session->id]);
        $this->assertDatabaseMissing('agent_conversations', ['id' => $convId]);
    }

    public function test_realtime_typing_presence_live_stream_and_human_takeover(): void
    {
        Setting::setValue('ai_support_enabled', '1', 'ai');
        $owner = $this->owner();

        // 1. Visitor starts typing on the website -> creates live session token & typing presence
        $typingRes = $this->postJson('/support-chat/typing', [
            'is_typing' => true,
            'preview' => 'عايز اسأل عن تكلفة بناء منصة',
            'landing_page' => '/services',
        ])->assertOk()->json();

        $token = $typingRes['token'];
        $this->assertNotEmpty($token);
        $this->assertTrue($typingRes['presence']['customer_typing']);

        $session = SupportChatSession::query()->where('token', $token)->firstOrFail();

        // 2. Admin dashboard live-pulse and live session endpoint immediately reflect customer typing
        $this->actingAs($owner)->getJson('/admin/support-chats/live-pulse')
            ->assertOk()
            ->assertJsonPath('active_typing.0.id', $session->id)
            ->assertJsonPath('active_typing.0.customer_typing', true);

        $this->actingAs($owner)->getJson("/admin/support-chats/{$session->id}/live")
            ->assertOk()
            ->assertJsonPath('presence.customer_typing', true)
            ->assertJsonPath('presence.customer_preview', 'عايز اسأل عن تكلفة بناء منصة');

        // 3. Admin types and sends a live human reply from the dashboard -> pauses AI & streams to visitor
        $this->actingAs($owner)->postJson("/admin/support-chats/{$session->id}/typing", [
            'is_typing' => true,
        ])->assertOk()->assertJsonPath('presence.admin_typing', true);

        $this->actingAs($owner)->postJson("/admin/support-chats/{$session->id}/reply", [
            'message' => 'أهلاً بك! أنا معك مباشرة من فريق الهندسة في Empetix.',
            'pause_ai' => true,
        ])->assertOk()
            ->assertJsonPath('session.ai_paused', true)
            ->assertJsonCount(1, 'messages');

        // 4. Visitor polling /support-chat/session receives the human specialist reply immediately
        $this->getJson("/support-chat/session?token={$token}")
            ->assertOk()
            ->assertJsonPath('session.ai_paused', true)
            ->assertJsonCount(1, 'session.messages')
            ->assertJsonPath('session.messages.0.source', 'human_agent');
    }
}
