<?php

namespace Tests\Feature;

use App\Ai\Agents\DashboardAssistantAgent;
use App\Ai\Tools\Dashboard\CreateBlogPostDraft;
use App\Ai\Tools\Dashboard\CreateJobPostDraft;
use App\Ai\Tools\Dashboard\DeleteInboxMessage;
use App\Ai\Tools\Dashboard\GetDashboardOverview;
use App\Ai\Tools\Dashboard\MarkInboxMessagesRead;
use App\Ai\Tools\Dashboard\ReadInboxMessage;
use App\Ai\Tools\Dashboard\SearchInboxMessages;
use App\Ai\Tools\Dashboard\SearchJobApplications;
use App\Ai\Tools\Dashboard\SearchSiteContent;
use App\Ai\Tools\Dashboard\SetContentVisibility;
use App\Ai\Tools\Dashboard\SetPageVisibility;
use App\Ai\Tools\Dashboard\UpdateJobApplicationStatus;
use App\Models\ContactMessage;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\Post;
use App\Models\Project;
use App\Models\User;
use App\Services\PageStatusService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Approvals\PendingApproval;
use Laravel\Ai\Prompts\AgentPrompt;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Tools\Request as ToolRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DashboardAssistantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_copilot_routes_require_permission(): void
    {
        $this->get('/admin/assistant')->assertRedirect(route('admin.login'));

        $unprivileged = User::factory()->create();
        $this->actingAs($unprivileged)->get('/admin/assistant')->assertForbidden();
        $this->actingAs($unprivileged)->postJson('/admin/assistant/message', ['prompt' => 'Hello'])->assertForbidden();

        $owner = $this->owner();
        $this->actingAs($owner)->get('/admin/assistant')->assertOk()->assertSee('Empetix Command Copilot');
    }

    public function test_agent_exposes_only_tools_matching_admin_permissions(): void
    {
        $inboxAdmin = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'inbox_specialist'], ['guard_name' => 'web']);
        $role->syncPermissions(['use ai assistant', 'manage inbox']);
        $inboxAdmin->assignRole($role);

        $agent = new DashboardAssistantAgent($inboxAdmin);
        $toolNames = array_map(fn (object $tool): string => class_basename($tool), [...$agent->tools()]);

        $this->assertContains('GetDashboardOverview', $toolNames);
        $this->assertContains('SearchInboxMessages', $toolNames);
        $this->assertContains('ReadInboxMessage', $toolNames);
        $this->assertContains('MarkInboxMessagesRead', $toolNames);
        $this->assertContains('DeleteInboxMessage', $toolNames);
        $this->assertNotContains('CreateBlogPostDraft', $toolNames);
        $this->assertNotContains('CreateJobPostDraft', $toolNames);
        $this->assertNotContains('SearchJobApplications', $toolNames);
        $this->assertNotContains('SetPageVisibility', $toolNames);

        // Direct tool execution also enforces permissions server-side.
        $denied = (new CreateBlogPostDraft($inboxAdmin))->handle(new ToolRequest([
            'title' => 'Unauthorized',
            'body_markdown' => 'Should not save.',
        ]));
        $this->assertStringContainsString('Permission denied', (string) $denied);
        $this->assertDatabaseMissing('posts', ['title' => 'Unauthorized']);
    }

    public function test_dashboard_tools_execute_cms_tasks_accurately(): void
    {
        $owner = $this->owner();

        $brief = ContactMessage::query()->create([
            'name' => 'Nour Hassan',
            'email' => 'nour@acme.eg',
            'company' => 'Acme FinTech',
            'services' => ['AI Automation'],
            'brief' => 'We want an AI copilot integrated with our ERP.',
        ]);

        $project = Project::query()->create([
            'title' => 'Nebula Cloud Core',
            'slug' => 'nebula-cloud-core',
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 1,
        ]);

        $job = JobPost::query()->create([
            'title' => 'Principal Backend Engineer',
            'slug' => 'principal-backend-engineer',
            'department' => 'Engineering',
            'location' => 'Cairo',
            'type' => 'Full-time',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $application = JobApplication::query()->create([
            'job_post_id' => $job->id,
            'full_name' => 'Karim Adel',
            'email' => 'karim@example.com',
            'phone' => '+201000000001',
            'location' => 'Cairo',
            'experience_years' => '6+',
            'notice_period' => '30 days',
            'achievement' => 'Scaled payment gateway to 50k TPS.',
            'cv_path' => 'cvs/karim.pdf',
            'status' => 'new',
        ]);

        // 1. Overview
        $overview = json_decode((string) (new GetDashboardOverview($owner))->handle(new ToolRequest), true);
        $this->assertSame(1, $overview['inbox']['unread_briefs']);
        $this->assertSame(1, $overview['applications']['new']);

        // 2. Inbox search, read, mark read, and delete
        $foundBriefs = json_decode((string) (new SearchInboxMessages($owner))->handle(new ToolRequest(['query' => 'Acme', 'unread_only' => true])), true);
        $this->assertSame(1, $foundBriefs['count']);

        $fullBrief = json_decode((string) (new ReadInboxMessage($owner))->handle(new ToolRequest(['id' => $brief->id])), true);
        $this->assertStringContainsString('AI copilot', $fullBrief['brief']);
        $this->assertNull($brief->fresh()->read_at);

        (new MarkInboxMessagesRead($owner))->handle(new ToolRequest(['ids' => [$brief->id], 'read' => true]));
        $this->assertNotNull($brief->fresh()->read_at);

        $deleteTool = new DeleteInboxMessage($owner);
        $this->assertNotNull($deleteTool->shouldRequestApproval(new ToolRequest(['id' => $brief->id])));
        $deleteTool->handle(new ToolRequest(['id' => $brief->id]));
        $this->assertDatabaseMissing('contact_messages', ['id' => $brief->id]);

        // 3. Applications search & status update
        $apps = json_decode((string) (new SearchJobApplications($owner))->handle(new ToolRequest(['status' => 'new'])), true);
        $this->assertSame(1, $apps['count']);
        (new UpdateJobApplicationStatus($owner))->handle(new ToolRequest(['ids' => [$application->id], 'status' => 'reviewed']));
        $this->assertSame('reviewed', $application->fresh()->status);

        // 4. Search content & toggle visibility
        $foundProjects = json_decode((string) (new SearchSiteContent($owner))->handle(new ToolRequest(['entity' => 'projects', 'query' => 'Nebula'])), true);
        $this->assertSame(1, $foundProjects['count']);

        (new SetContentVisibility($owner))->handle(new ToolRequest(['entity' => 'projects', 'id' => $project->id, 'visible' => false]));
        $this->assertFalse($project->fresh()->is_active);

        // 5. Page visibility
        (new SetPageVisibility($owner))->handle(new ToolRequest(['page' => 'partners', 'active' => false]));
        $this->assertFalse(app(PageStatusService::class)->isActive('partners'));

        // 6. Create unpublished blog draft & inactive job draft
        $draftResult = json_decode((string) (new CreateBlogPostDraft($owner))->handle(new ToolRequest([
            'title' => 'Event-Driven Laravel Architectures',
            'excerpt' => 'Practical patterns for reliable queues.',
            'body_markdown' => "## Introduction\n\nQueues keep requests fast.",
            'category' => 'Engineering',
        ])), true);

        $savedPost = Post::query()->findOrFail($draftResult['id']);
        $this->assertFalse($savedPost->is_published);
        $this->assertStringContainsString('<h2>Introduction</h2>', (string) $savedPost->body);

        $jobResult = json_decode((string) (new CreateJobPostDraft($owner))->handle(new ToolRequest([
            'title' => 'AI Platform Engineer',
            'department' => 'AI & Data',
            'location' => 'New Cairo',
            'type' => 'Full-time',
            'summary' => 'Build agentic workflows and RAG pipelines.',
            'responsibilities' => ['Design LLM tools', 'Monitor latency and evals'],
            'requirements' => ['Strong PHP/Laravel', 'Production LLM experience'],
        ])), true);

        $savedJob = JobPost::query()->findOrFail($jobResult['id']);
        $this->assertFalse($savedJob->is_active);
    }

    public function test_prompt_conversation_persistence_ownership_and_human_approval_resume(): void
    {
        $adminA = $this->owner();
        $adminB = $this->owner();

        DashboardAssistantAgent::fake([
            'You currently have 2 unread client briefs.',
            AgentResponse::fakeWithPendingApprovals([
                new PendingApproval(
                    id: 'call_del_42',
                    tool: 'DeleteInboxMessage',
                    arguments: ['id' => 42],
                    reason: 'Permanently delete inbox brief #42.',
                ),
            ]),
            'Inbox brief #42 was declined and kept safe.',
        ]);

        // Turn 1: Start conversation
        $res1 = $this->actingAs($adminA)->postJson('/admin/assistant/message', [
            'prompt' => 'How many unread briefs do we have?',
        ])->assertOk()->json();

        $conversationId = $res1['conversation']['id'];
        $this->assertNotEmpty($conversationId);
        $this->assertCount(2, $res1['conversation']['messages']);
        $this->assertSame([], $res1['conversation']['pending_approvals']);

        // Ownership isolation: Admin B cannot access, continue, or delete Admin A's conversation
        $this->actingAs($adminB)->getJson("/admin/assistant/conversations/{$conversationId}")->assertNotFound();
        $this->actingAs($adminB)->postJson('/admin/assistant/message', [
            'prompt' => 'Hijack',
            'conversation_id' => $conversationId,
        ])->assertNotFound();
        $this->actingAs($adminB)->deleteJson("/admin/assistant/conversations/{$conversationId}")->assertNotFound();

        // Turn 2: Tool pauses for human approval
        $res2 = $this->actingAs($adminA)->postJson('/admin/assistant/message', [
            'prompt' => 'Delete brief #42',
            'conversation_id' => $conversationId,
        ])->assertOk()->json();

        $this->assertCount(1, $res2['conversation']['pending_approvals']);
        $this->assertSame('call_del_42', $res2['conversation']['pending_approvals'][0]['id']);

        // Reloading conversation also surfaces the pending approval
        $loaded = $this->actingAs($adminA)->getJson("/admin/assistant/conversations/{$conversationId}")->assertOk()->json();
        $this->assertCount(1, $loaded['conversation']['pending_approvals']);

        // Turn 3: Admin submits approval/rejection decision
        $res3 = $this->actingAs($adminA)->postJson("/admin/assistant/conversations/{$conversationId}/approve", [
            'decisions' => ['call_del_42' => false],
        ])->assertOk()->json();

        $this->assertSame([], $res3['conversation']['pending_approvals']);

        DashboardAssistantAgent::assertPrompted(function (AgentPrompt $prompt): bool {
            return $prompt->hasApprovalDecisions()
                && $prompt->approvalDecisions->get('call_del_42')?->isRejected() === true;
        });

        // Delete conversation
        $this->actingAs($adminA)->deleteJson("/admin/assistant/conversations/{$conversationId}")->assertOk();
        $this->assertDatabaseMissing('agent_conversations', ['id' => $conversationId]);
    }
}
