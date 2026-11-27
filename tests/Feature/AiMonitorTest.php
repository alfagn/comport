<?php

namespace Tests\Feature;

use App\Models\AiUsageLog;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AiMonitorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_ai_monitor_enforces_authentication_and_permissions(): void
    {
        $this->get('/admin/ai-monitor')->assertRedirect(route('admin.login'));

        $unprivileged = User::factory()->create();
        $this->actingAs($unprivileged)->get('/admin/ai-monitor')->assertForbidden();
        $this->actingAs($unprivileged)->post('/admin/ai-monitor/probe')->assertForbidden();

        $supportSpecialist = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'support_ops'], ['guard_name' => 'web']);
        $role->syncPermissions(['manage support chats']);
        $supportSpecialist->assignRole($role);

        $this->actingAs($supportSpecialist)
            ->get('/admin/ai-monitor')
            ->assertOk()
            ->assertSee('AI Quota, Token Usage &amp; Model Health', false);
    }

    public function test_live_503_failover_records_model_issue_and_emergency_rescue_telemetry(): void
    {
        Setting::setValue('ai_support_enabled', '1', 'ai');

        foreach (['gemini', 'gemini-support', 'gemini-copilot', 'gemini-fallback', 'deepseek', 'openai', 'anthropic', 'xai'] as $prov) {
            config(["ai.providers.{$prov}.key" => null]);
        }

        config([
            'ai.default' => 'gemini',
            'assistant.failover.enabled' => true,
            'assistant.failover.chain' => 'deepseek',
            'ai.providers.gemini-support.key' => 'AIza-primary-support',
            'ai.providers.deepseek.key' => 'sk-deepseek-emergency',
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'error' => [
                    'code' => 503,
                    'message' => 'The model is overloaded. Please try again later.',
                    'status' => 'UNAVAILABLE',
                ],
            ], 503),
            'api.deepseek.com/*' => Http::response([
                'id' => 'chatcmpl-rescue-1',
                'object' => 'chat.completion',
                'model' => 'deepseek-flash',
                'choices' => [
                    [
                        'index' => 0,
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'مرحباً بك! تم الرد فوراً عبر مزود الطوارئ DeepSeek دون أي انقطاع.',
                        ],
                        'finish_reason' => 'stop',
                    ],
                ],
                'usage' => [
                    'prompt_tokens' => 140,
                    'completion_tokens' => 55,
                    'total_tokens' => 195,
                ],
            ], 200, [
                'x-ratelimit-limit-requests' => '5000',
                'x-ratelimit-remaining-requests' => '4999',
            ]),
        ]);

        // Visitor sends a live chat message -> Gemini 503 fails over seamlessly to DeepSeek 200!
        $res = $this->postJson('/support-chat/message', [
            'message' => 'أحتاج استشارة حول بناء نظام SaaS',
        ])->assertOk()->json('session');

        $this->assertCount(2, $res['messages']);
        $this->assertStringContainsString('DeepSeek', $res['messages'][1]['content']);

        // Telemetry must record both the 503 failover on gemini-support AND the 200 success on deepseek
        $this->assertDatabaseHas('ai_usage_logs', [
            'provider' => 'gemini-support',
            'status' => AiUsageLog::STATUS_FAILOVER,
            'http_code' => 503,
            'error_category' => 'overloaded_503',
        ]);

        $this->assertDatabaseHas('ai_usage_logs', [
            'provider' => 'deepseek',
            'model' => 'deepseek-flash',
            'status' => AiUsageLog::STATUS_SUCCESS,
            'http_code' => 200,
            'input_tokens' => 140,
            'output_tokens' => 55,
            'total_tokens' => 195,
        ]);

        // Also seed a 429 Quota error & 401 Invalid Key error to verify full Per-Model Diagnosis rendering
        AiUsageLog::query()->create([
            'channel' => 'copilot',
            'agent' => 'DashboardAssistantAgent',
            'provider' => 'gemini-copilot',
            'driver' => 'gemini',
            'model' => 'gemini-3.1-flash-lite',
            'status' => AiUsageLog::STATUS_ERROR,
            'http_code' => 429,
            'error_category' => 'rate_limit_429',
            'error_message' => 'Resource has been exhausted (e.g. check quota).',
        ]);

        AiUsageLog::query()->create([
            'channel' => 'probe',
            'agent' => 'Probe',
            'provider' => 'openai',
            'driver' => 'openai',
            'model' => 'gpt-6.1-sol',
            'status' => AiUsageLog::STATUS_ERROR,
            'http_code' => 401,
            'error_category' => 'invalid_key_401',
            'error_message' => 'Incorrect API key provided.',
        ]);

        $owner = $this->owner();

        $this->actingAs($owner)
            ->get('/admin/ai-monitor?days=28')
            ->assertOk()
            ->assertSee('140')
            ->assertSee('55')
            ->assertSee('503 Overloaded')
            ->assertSee('429 Rate / Quota Limit')
            ->assertSee('401 Invalid API Key')
            ->assertSee('Rescued by Failover')
            ->assertSee('ضغط عالٍ مؤقت على سيرفرات الموديل');

        // JSON report endpoint also returns exact aggregated figures
        $json = $this->actingAs($owner)
            ->getJson('/admin/ai-monitor?days=28')
            ->assertOk()
            ->json('report');

        $this->assertSame(140, $json['kpis']['input_tokens']);
        $this->assertSame(55, $json['kpis']['output_tokens']);
        $this->assertSame(1, $json['kpis']['failover_count']);
        $this->assertSame(2, $json['kpis']['error_count']);

        // Clearing errors removes failover/error rows while keeping valid token usage intact
        $this->actingAs($owner)
            ->delete('/admin/ai-monitor/errors')
            ->assertRedirect();

        $this->assertSame(1, AiUsageLog::query()->count());
        $this->assertSame(AiUsageLog::STATUS_SUCCESS, AiUsageLog::query()->first()?->status);
    }

    public function test_live_probe_endpoint_tests_configured_provider_and_logs_health(): void
    {
        $owner = $this->owner();

        config([
            'ai.providers.deepseek.key' => 'sk-deepseek-live-probe',
        ]);

        Http::fake([
            'api.deepseek.com/*' => Http::response([
                'id' => 'chatcmpl-probe-1',
                'object' => 'chat.completion',
                'model' => 'deepseek-flash',
                'choices' => [
                    [
                        'index' => 0,
                        'message' => ['role' => 'assistant', 'content' => 'OK'],
                        'finish_reason' => 'stop',
                    ],
                ],
                'usage' => [
                    'prompt_tokens' => 12,
                    'completion_tokens' => 2,
                    'total_tokens' => 14,
                ],
            ], 200),
        ]);

        $probeRes = $this->actingAs($owner)
            ->postJson('/admin/ai-monitor/probe', ['provider' => 'deepseek'])
            ->assertOk()
            ->json();

        $this->assertTrue($probeRes['results'][0]['ok']);
        $this->assertSame('deepseek', $probeRes['results'][0]['provider']);

        $this->assertDatabaseHas('ai_usage_logs', [
            'channel' => 'probe',
            'provider' => 'deepseek',
            'status' => AiUsageLog::STATUS_SUCCESS,
            'total_tokens' => 14,
        ]);
    }
}
