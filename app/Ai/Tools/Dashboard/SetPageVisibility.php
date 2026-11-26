<?php

namespace App\Ai\Tools\Dashboard;

use App\Services\PageStatusService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Approvals\Approval;
use Laravel\Ai\Concerns\InteractsWithApprovals;
use Laravel\Ai\Contracts\Approvable;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Enable or hide a top-level public page (e.g. careers, partners, blog).
 * Always pauses for admin approval because hidden pages return 404 to visitors.
 */
class SetPageVisibility extends DashboardTool implements Approvable
{
    use InteractsWithApprovals;

    public function permission(): ?string
    {
        return 'manage pages';
    }

    public function description(): Stringable|string
    {
        return 'Enable (live) or disable (hide with 404) a top-level public website page: '.implode(', ', array_keys(PageStatusService::PAGES)).'. Requires admin approval before execution.';
    }

    protected function needsApproval(Request $request): Approval|bool
    {
        $pageKey = (string) ($request['page'] ?? '');
        $title = PageStatusService::PAGES[$pageKey] ?? $pageKey;
        $action = filter_var($request['active'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 'Publish / enable' : 'Hide (404)';

        return Approval::required("{$action} the \"{$title}\" public page.");
    }

    protected function run(Request $request): string
    {
        $pages = array_keys(PageStatusService::PAGES);
        $validated = $request->validate([
            'page' => ['required', 'in:'.implode(',', $pages)],
            'active' => ['required', 'boolean'],
        ]);

        $pageKey = $validated['page'];
        $active = (bool) $validated['active'];

        app(PageStatusService::class)->setActive($pageKey, $active);

        return $this->json([
            'page' => $pageKey,
            'title' => PageStatusService::PAGES[$pageKey],
            'active' => $active,
            'result' => $active ? 'Page is now live.' : 'Page is now hidden from menus and returns 404.',
            'admin_url' => route('admin.pages.index'),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'page' => $schema->string()->enum(array_keys(PageStatusService::PAGES))->required()->description('Page key to enable or hide.'),
            'active' => $schema->boolean()->required()->description('true = live, false = hidden.'),
        ];
    }
}
