<?php

namespace App\Ai\Tools\Dashboard;

use App\Models\JobApplication;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Search job applications received through the careers pages.
 */
class SearchJobApplications extends DashboardTool
{
    public function permission(): ?string
    {
        return 'manage applications';
    }

    public function description(): Stringable|string
    {
        return 'Search job applications. Filter by review status (new or reviewed), by role title, or by keyword (candidate name, email, location). Returns newest first with experience, notice period and a short excerpt of their achievement.';
    }

    protected function run(Request $request): string
    {
        $validated = $request->validate([
            'query' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:new,reviewed,any'],
            'role' => ['nullable', 'string', 'max:120'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:25'],
        ]);

        $term = trim((string) ($validated['query'] ?? ''));
        $role = trim((string) ($validated['role'] ?? ''));
        $status = $validated['status'] ?? 'any';

        $applications = JobApplication::query()
            ->with('jobPost:id,title')
            ->when($status !== 'any', fn ($query) => $query->where('status', $status))
            ->when($role !== '', fn ($query) => $query->whereHas('jobPost', fn ($job) => $job->where('title', 'like', "%{$role}%")))
            ->when($term !== '', function ($query) use ($term) {
                $query->where(function ($inner) use ($term) {
                    foreach (['full_name', 'email', 'location'] as $column) {
                        $inner->orWhere($column, 'like', "%{$term}%");
                    }
                });
            })
            ->latest()
            ->limit($validated['limit'] ?? 10)
            ->get();

        return $this->json([
            'count' => $applications->count(),
            'applications' => $applications->map(fn (JobApplication $application): array => [
                'id' => $application->id,
                'candidate' => $application->full_name,
                'email' => $application->email,
                'location' => $application->location,
                'role' => $application->jobPost?->title,
                'experience_years' => $application->experience_years,
                'notice_period' => $application->notice_period,
                'linkedin' => $application->linkedin,
                'portfolio' => $application->portfolio,
                'achievement_excerpt' => Str::limit((string) $application->achievement, 220),
                'status' => $application->status,
                'applied_at' => $application->created_at?->toDateTimeString(),
                'admin_url' => route('admin.applications.show', $application->id),
            ])->all(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->description('Optional keyword: candidate name, email or location.'),
            'status' => $schema->string()->enum(['new', 'reviewed', 'any'])->description('Review status filter (default any).'),
            'role' => $schema->string()->description('Optional job title to filter by, e.g. "Laravel".'),
            'limit' => $schema->integer()->min(1)->max(25)->description('Maximum results (default 10).'),
        ];
    }
}
