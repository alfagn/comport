<?php

namespace App\Ai\Tools\Support;

use App\Models\Member;
use App\Models\Post;
use App\Models\Project;
use App\Models\ServicePage;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Searches published case studies, detailed service pages, leadership profiles,
 * and engineering blog articles on the public website.
 */
class SearchPortfolioAndInsights implements Tool
{
    public function description(): Stringable|string
    {
        return 'Search Empetix published case studies / portfolio projects, detailed service pages, founders & leadership team, and blog articles. Leave query empty to get featured highlights.';
    }

    public function handle(Request $request): Stringable|string
    {
        $validated = $request->validate([
            'query' => ['nullable', 'string', 'max:120'],
        ]);

        $term = trim((string) ($validated['query'] ?? ''));

        $projects = [];
        if (page_active('work')) {
            $projects = Project::active()
                ->when($term !== '', function ($query) use ($term): void {
                    $query->where(function ($inner) use ($term): void {
                        foreach (['title', 'client', 'kicker', 'excerpt', 'body'] as $col) {
                            $inner->orWhere($col, 'like', "%{$term}%");
                        }
                    });
                })
                ->orderByDesc('is_featured')
                ->limit(6)
                ->get()
                ->map(fn (Project $project): array => [
                    'title' => $project->title,
                    'client' => $project->client,
                    'year' => $project->year,
                    'excerpt' => Str::limit((string) $project->excerpt, 220),
                    'live_url' => $project->live_url,
                    'case_study_url' => route('work.show', $project->slug),
                ])->all();
        }

        $servicePages = [];
        if (page_active('services')) {
            $servicePages = ServicePage::query()
                ->where('locale', 'en')
                ->where('is_published', true)
                ->when($term !== '', function ($query) use ($term): void {
                    $query->where(function ($inner) use ($term): void {
                        foreach (['title', 'summary', 'body'] as $col) {
                            $inner->orWhere($col, 'like', "%{$term}%");
                        }
                    });
                })
                ->limit(5)
                ->get()
                ->map(fn (ServicePage $page): array => [
                    'title' => $page->title,
                    'summary' => Str::limit((string) $page->summary, 220),
                    'url' => url($page->publicPath()),
                ])->all();
        }

        $posts = [];
        if (page_active('blog')) {
            $posts = Post::published()
                ->when($term !== '', function ($query) use ($term): void {
                    $query->where(function ($inner) use ($term): void {
                        foreach (['title', 'category', 'excerpt', 'body'] as $col) {
                            $inner->orWhere($col, 'like', "%{$term}%");
                        }
                    });
                })
                ->limit(5)
                ->get()
                ->map(fn (Post $post): array => [
                    'title' => $post->title,
                    'category' => $post->category,
                    'excerpt' => Str::limit((string) $post->excerpt, 180),
                    'url' => route('blog.show', $post->slug),
                ])->all();
        }

        $leaders = [];
        if (page_active('team') || page_active('about')) {
            $leaders = Member::query()
                ->where('is_active', true)
                ->when($term !== '', function ($query) use ($term): void {
                    $query->where(function ($inner) use ($term): void {
                        foreach (['name', 'role', 'department', 'focus', 'lead'] as $col) {
                            $inner->orWhere($col, 'like', "%{$term}%");
                        }
                    });
                })
                ->orderBy('sort_order')
                ->limit(6)
                ->get()
                ->map(fn (Member $member): array => [
                    'name' => $member->name,
                    'role' => $member->role,
                    'kind' => $member->kind,
                    'focus' => $member->focus ?: $member->lead,
                    'profile_url' => ($member->kind === 'founder' && $member->slug && page_active('team'))
                        ? route('founders.show', $member->slug)
                        : (page_active('team') ? route('team') : null),
                ])->all();
        }

        return (string) json_encode([
            'query' => $term !== '' ? $term : null,
            'projects' => $projects,
            'service_pages' => $servicePages,
            'blog_posts' => $posts,
            'team' => $leaders,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->description('Optional keyword (e.g. "fintech", "ERP", "Flutter", "AI", founder name).'),
        ];
    }
}
