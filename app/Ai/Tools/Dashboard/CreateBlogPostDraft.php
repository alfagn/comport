<?php

namespace App\Ai\Tools\Dashboard;

use App\DTOs\PostData;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Ai\Approvals\Approval;
use Laravel\Ai\Concerns\InteractsWithApprovals;
use Laravel\Ai\Contracts\Approvable;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Save an AI-written article as an unpublished blog draft for an editor to
 * review. Creates content, so it pauses for admin approval.
 */
class CreateBlogPostDraft extends DashboardTool implements Approvable
{
    use InteractsWithApprovals;

    public function permission(): ?string
    {
        return 'manage posts';
    }

    public function description(): Stringable|string
    {
        return 'Save a new blog article as an unpublished DRAFT (never published automatically). Write the body in Markdown with headings and paragraphs. The admin must approve before it is saved.';
    }

    protected function needsApproval(Request $request): Approval|bool
    {
        return Approval::required('Save the unpublished blog draft "'.Str::limit((string) ($request['title'] ?? 'Untitled'), 90).'".');
    }

    protected function run(Request $request): string
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:220'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body_markdown' => ['required', 'string', 'max:30000'],
            'category' => ['nullable', 'string', 'max:120'],
        ]);

        $post = app(PostService::class)->create(PostData::fromArray([
            'title' => $validated['title'],
            'slug' => $this->uniqueSlug($validated['title']),
            'type' => 'article',
            'category' => $validated['category'] ?? null,
            'excerpt' => $validated['excerpt'] ?? null,
            'body' => Str::markdown($validated['body_markdown'], ['html_input' => 'strip', 'allow_unsafe_links' => false]),
            'is_published' => false,
        ]));

        return $this->json([
            'result' => 'Draft saved (not published).',
            'id' => $post->getKey(),
            'title' => $post->getAttribute('title'),
            'admin_url' => route('admin.posts.edit', $post->getKey()),
        ]);
    }

    /**
     * Slug from the title, suffixed until no post uses it.
     */
    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'draft';
        $slug = $base;
        $suffix = 2;

        while (Post::query()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()->required()->description('Article headline.'),
            'excerpt' => $schema->string()->description('One or two sentence summary for cards and SEO.'),
            'body_markdown' => $schema->string()->required()->description('Full article body in Markdown.'),
            'category' => $schema->string()->description('Optional category label, e.g. "Engineering".'),
        ];
    }
}
