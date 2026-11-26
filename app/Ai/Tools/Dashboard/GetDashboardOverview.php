<?php

namespace App\Ai\Tools\Dashboard;

use App\Models\ContactMessage;
use App\Models\JobApplication;
use App\Models\SupportChatSession;
use App\Services\DashboardService;
use App\Services\PageStatusService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Headline numbers for the whole dashboard: content totals, unread briefs,
 * new job applications and AI support chat activity.
 */
class GetDashboardOverview extends DashboardTool
{
    public function permission(): ?string
    {
        return null;
    }

    public function description(): Stringable|string
    {
        return 'Get the current dashboard overview: totals for projects, services, team members, open roles, blog posts and partners, plus unread inbox briefs, new job applications and AI support chat activity. Use it for any "how are we doing / what needs attention" question.';
    }

    protected function run(Request $request): string
    {
        return $this->json([
            'generated_at' => now()->toDateTimeString(),
            'content_totals' => app(DashboardService::class)->stats(),
            'inbox' => [
                'unread_briefs' => ContactMessage::unread()->count(),
                'briefs_last_7_days' => ContactMessage::query()->where('created_at', '>=', now()->subDays(7))->count(),
            ],
            'applications' => [
                'new' => JobApplication::new()->count(),
                'received_last_7_days' => JobApplication::query()->where('created_at', '>=', now()->subDays(7))->count(),
            ],
            'support_chats' => [
                'today' => SupportChatSession::query()->whereDate('created_at', today())->count(),
                'handed_off_last_7_days' => SupportChatSession::handedOff()->where('updated_at', '>=', now()->subDays(7))->count(),
            ],
            'page_statuses' => app(PageStatusService::class)->all(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
