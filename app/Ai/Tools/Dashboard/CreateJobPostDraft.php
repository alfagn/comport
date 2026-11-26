<?php

namespace App\Ai\Tools\Dashboard;

use App\DTOs\JobPostData;
use App\Models\JobPost;
use App\Services\JobPostService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Ai\Approvals\Approval;
use Laravel\Ai\Concerns\InteractsWithApprovals;
use Laravel\Ai\Contracts\Approvable;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Create a new career opening saved as hidden/inactive until an admin reviews
 * and activates it. Pauses for admin approval.
 */
class CreateJobPostDraft extends DashboardTool implements Approvable
{
    use InteractsWithApprovals;

    public function permission(): ?string
    {
        return 'manage jobs';
    }

    public function description(): Stringable|string
    {
        return 'Create a new career job opening saved as an inactive draft (is_active=false) so the hiring team can review and publish it. Requires admin approval.';
    }

    protected function needsApproval(Request $request): Approval|bool
    {
        $title = Str::limit((string) ($request['title'] ?? 'Untitled role'), 90);

        return Approval::required("Save inactive job opening draft \"{$title}\".");
    }

    protected function run(Request $request): string
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'department' => ['required', 'string', 'max:120'],
            'location' => ['required', 'string', 'max:120'],
            'type' => ['required', 'string', 'max:80'],
            'summary' => ['required', 'string', 'max:600'],
            'responsibilities' => ['required', 'array', 'min:2', 'max:15'],
            'responsibilities.*' => ['string', 'max:300'],
            'requirements' => ['required', 'array', 'min:2', 'max:15'],
            'requirements.*' => ['string', 'max:300'],
        ]);

        $job = app(JobPostService::class)->create(JobPostData::fromArray([
            'title' => $validated['title'],
            'slug' => $this->uniqueSlug($validated['title']),
            'ref_code' => 'EMP-'.strtoupper(Str::random(4)),
            'department' => $validated['department'],
            'location' => $validated['location'],
            'type' => $validated['type'],
            'summary' => $validated['summary'],
            'responsibilities' => implode("\n", $validated['responsibilities']),
            'requirements' => implode("\n", $validated['requirements']),
            'is_active' => false,
            'sort_order' => ((int) JobPost::query()->max('sort_order')) + 1,
        ]));

        return $this->json([
            'result' => 'Job opening draft saved (hidden until activated).',
            'id' => $job->getKey(),
            'title' => $job->getAttribute('title'),
            'admin_url' => route('admin.jobs.edit', $job->getKey()),
        ]);
    }

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'role';
        $slug = $base;
        $suffix = 2;

        while (JobPost::query()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()->required()->description('Role title, e.g. "Senior Laravel Engineer".'),
            'department' => $schema->string()->required()->description('Department, e.g. "Engineering", "Product", "Cloud & DevOps".'),
            'location' => $schema->string()->required()->description('Location, e.g. "New Cairo (Hybrid)" or "Remote".'),
            'type' => $schema->string()->required()->description('Employment type, e.g. "Full-time".'),
            'summary' => $schema->string()->required()->description('Short 1-3 sentence role overview.'),
            'responsibilities' => $schema->array()->items($schema->string())->required()->description('Bullet list of key responsibilities.'),
            'requirements' => $schema->array()->items($schema->string())->required()->description('Bullet list of candidate requirements.'),
        ];
    }
}
