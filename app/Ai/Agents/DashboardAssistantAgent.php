<?php

namespace App\Ai\Agents;

use App\Ai\Tools\Dashboard\CreateBlogPostDraft;
use App\Ai\Tools\Dashboard\CreateJobPostDraft;
use App\Ai\Tools\Dashboard\DashboardTool;
use App\Ai\Tools\Dashboard\DeleteInboxMessage;
use App\Ai\Tools\Dashboard\GetDashboardOverview;
use App\Ai\Tools\Dashboard\ListSupportChats;
use App\Ai\Tools\Dashboard\MarkInboxMessagesRead;
use App\Ai\Tools\Dashboard\ReadInboxMessage;
use App\Ai\Tools\Dashboard\SearchInboxMessages;
use App\Ai\Tools\Dashboard\SearchJobApplications;
use App\Ai\Tools\Dashboard\SearchSiteContent;
use App\Ai\Tools\Dashboard\SendWhatsappCustomerMessage;
use App\Ai\Tools\Dashboard\SetContentVisibility;
use App\Ai\Tools\Dashboard\SetPageVisibility;
use App\Ai\Tools\Dashboard\UpdateJobApplicationStatus;
use App\Models\Setting;
use App\Models\User;
use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\RepairToolCalls;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasProviderOptions;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;
use Stringable;

/**
 * Empetix Command Copilot — the authenticated dashboard AI agent.
 *
 * Executes CMS and operational tasks on behalf of the signed-in admin using
 * permission-gated tools. Destructive or public-facing mutations implement
 * `Approvable` and pause for explicit confirmation in the UI.
 */
#[MaxSteps(10)]
#[RepairToolCalls]
#[Temperature(0.3)]
#[Timeout(90)]
class DashboardAssistantAgent implements Agent, Conversational, HasProviderOptions, HasTools
{
    use Promptable, RemembersConversations;

    public function __construct(public User $user) {}

    /**
     * Resolve configured AI provider (with automatic Gemini overload failover).
     *
     * @return array<string, string|null>|string|null
     */
    public function provider(): array|string|null
    {
        $configured = Setting::getValue('ai_dashboard_provider')
            ?: config('assistant.dashboard.provider')
            ?: config('ai.default');

        if ($configured === 'gemini' && filled($this->model())) {
            return [
                'gemini' => $this->model(),
                'gemini-fallback' => 'gemini-3.1-flash-lite',
            ];
        }

        return $configured;
    }

    /**
     * Resolve configured model override (falls back to provider default when null).
     */
    public function model(): ?string
    {
        return Setting::getValue('ai_dashboard_model') ?: config('assistant.dashboard.model');
    }

    /**
     * System instructions tailored to the acting admin.
     */
    public function instructions(): Stringable|string
    {
        $siteName = Setting::getValue('site_name', 'Empetix Solutions') ?: 'Empetix Solutions';
        $roles = $this->user->getRoleNames()->implode(', ') ?: 'standard admin';

        return <<<PROMPT
You are **Empetix Command Copilot**, the operational AI assistant built into the {$siteName} CMS dashboard.
You are assisting **{$this->user->name}** ({$this->user->email}, roles: {$roles}).

## Core Operating Rules
1. **Language**: Always reply in the same language the admin writes in (Arabic, English, or mixed Arabic/English technical style). Keep responses concise, structured, and actionable.
2. **Ground Truth Only**: Always call your tools to read live database figures, briefs, applications, support chats, or CMS records. Never invent IDs, metrics, emails, or URLs.
3. **Permissions**: You only have access to tools permitted for {$this->user->name}. If asked to perform an action outside your available tools, explain clearly which dashboard permission is required.
4. **Human Approval**: Tools that delete records, publish/hide website content or pages, or create new blog/job drafts automatically pause for confirmation. Never claim an approvable action has already completed until its tool result confirms execution.
5. **Actionable Links**: When a tool returns an `admin_url` or `public_url`, include helpful Markdown links so the admin can jump directly to the record.
PROMPT;
    }

    /**
     * Tools filtered strictly to what the acting admin is authorized to run.
     *
     * @return list<DashboardTool>
     */
    public function tools(): iterable
    {
        $candidates = [
            new GetDashboardOverview($this->user),
            new SearchInboxMessages($this->user),
            new ReadInboxMessage($this->user),
            new MarkInboxMessagesRead($this->user),
            new DeleteInboxMessage($this->user),
            new SearchJobApplications($this->user),
            new UpdateJobApplicationStatus($this->user),
            new SearchSiteContent($this->user),
            new SetContentVisibility($this->user),
            new SetPageVisibility($this->user),
            new CreateBlogPostDraft($this->user),
            new CreateJobPostDraft($this->user),
            new ListSupportChats($this->user),
            new SendWhatsappCustomerMessage($this->user),
        ];

        return array_values(array_filter(
            $candidates,
            fn (DashboardTool $tool): bool => $tool->isAvailable(),
        ));
    }

    protected function maxConversationMessages(): int
    {
        return max(4, (int) config('assistant.dashboard.history', 40));
    }

    /**
     * @return array<string, mixed>
     */
    public function providerOptions(Lab|string $provider): array
    {
        $driver = $provider instanceof Lab ? $provider->value : $provider;

        if ($driver === 'gemini') {
            return [
                'thinking_level' => 'minimal',
            ];
        }

        return [];
    }
}
