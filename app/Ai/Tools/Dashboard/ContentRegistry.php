<?php

namespace App\Ai\Tools\Dashboard;

use App\Models\Industry;
use App\Models\JobPost;
use App\Models\Member;
use App\Models\Partner;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Services\SeoService;
use Illuminate\Database\Eloquent\Model;

/**
 * The CMS entities the dashboard assistant may search and publish/hide.
 *
 * Mirrors the permission and visibility columns StatusToggleController uses
 * for the one-click toggles, so the assistant follows the same rules.
 */
final class ContentRegistry
{
    /**
     * entity => [model, title column, visibility column, permission, admin edit route, public route, flushes SEO caches].
     *
     * @var array<string, array{model: class-string<Model>, title: string, visibility: string, permission: string, admin_route: string, public_route: ?string, seo: bool}>
     */
    private const ENTITIES = [
        'projects' => ['model' => Project::class, 'title' => 'title', 'visibility' => 'is_active', 'permission' => 'manage projects', 'admin_route' => 'admin.projects.edit', 'public_route' => 'work.show', 'seo' => true],
        'services' => ['model' => Service::class, 'title' => 'title', 'visibility' => 'is_active', 'permission' => 'manage services', 'admin_route' => 'admin.services.edit', 'public_route' => null, 'seo' => true],
        'posts' => ['model' => Post::class, 'title' => 'title', 'visibility' => 'is_published', 'permission' => 'manage posts', 'admin_route' => 'admin.posts.edit', 'public_route' => 'blog.show', 'seo' => true],
        'jobs' => ['model' => JobPost::class, 'title' => 'title', 'visibility' => 'is_active', 'permission' => 'manage jobs', 'admin_route' => 'admin.jobs.edit', 'public_route' => 'jobs.show', 'seo' => true],
        'members' => ['model' => Member::class, 'title' => 'name', 'visibility' => 'is_active', 'permission' => 'manage members', 'admin_route' => 'admin.members.edit', 'public_route' => null, 'seo' => true],
        'partners' => ['model' => Partner::class, 'title' => 'name', 'visibility' => 'is_active', 'permission' => 'manage partners', 'admin_route' => 'admin.partners.edit', 'public_route' => null, 'seo' => false],
        'industries' => ['model' => Industry::class, 'title' => 'title', 'visibility' => 'is_active', 'permission' => 'manage industries', 'admin_route' => 'admin.industries.edit', 'public_route' => null, 'seo' => false],
    ];

    /**
     * Every supported entity key.
     *
     * @return list<string>
     */
    public static function keys(): array
    {
        return array_keys(self::ENTITIES);
    }

    /**
     * Configuration of one entity, or null when unsupported.
     *
     * @return array{model: class-string<Model>, title: string, visibility: string, permission: string, admin_route: string, public_route: ?string, seo: bool}|null
     */
    public static function get(string $entity): ?array
    {
        return self::ENTITIES[$entity] ?? null;
    }

    /**
     * Compact summary of a record for tool results.
     *
     * @return array<string, mixed>
     */
    public static function summarize(string $entity, Model $record): array
    {
        $config = self::ENTITIES[$entity];
        $publicUrl = null;

        if ($config['public_route'] && $record->getAttribute('slug')) {
            $publicUrl = route($config['public_route'], $record->getAttribute('slug'));
        }

        return [
            'entity' => $entity,
            'id' => $record->getKey(),
            'title' => $record->getAttribute($config['title']),
            'visible_on_site' => (bool) $record->getAttribute($config['visibility']),
            'updated_at' => $record->getAttribute('updated_at')?->toDateTimeString(),
            'admin_url' => route($config['admin_route'], $record->getKey()),
            'public_url' => $publicUrl,
        ];
    }

    /**
     * Clear the public caches that depend on the entity after a change.
     */
    public static function flushCaches(string $entity): void
    {
        if (self::ENTITIES[$entity]['seo'] ?? false) {
            app(SeoService::class)->flushCaches();
        }
    }
}
