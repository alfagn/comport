<?php

namespace App\Ai\Tools\Dashboard;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Find CMS records (projects, services, posts, roles, team, partners,
 * industries) the admin is allowed to manage.
 */
class SearchSiteContent extends DashboardTool
{
    public function permission(): ?string
    {
        return null;
    }

    /**
     * Available whenever the admin manages at least one content entity.
     */
    public function isAvailable(): bool
    {
        return $this->allowedEntities() !== [];
    }

    public function description(): Stringable|string
    {
        return 'Search website content records by keyword in one entity: '.implode(', ', ContentRegistry::keys()).'. Returns id, title, whether it is visible on the public site, and admin/public links. Use it to find the id before changing visibility.';
    }

    protected function run(Request $request): string
    {
        $validated = $request->validate([
            'entity' => ['required', 'in:'.implode(',', ContentRegistry::keys())],
            'query' => ['nullable', 'string', 'max:120'],
            'visibility' => ['nullable', 'in:visible,hidden,any'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        $entity = $validated['entity'];

        if (! in_array($entity, $this->allowedEntities(), true)) {
            return "Permission denied: your account cannot manage {$entity}.";
        }

        $config = ContentRegistry::get($entity);
        $term = trim((string) ($validated['query'] ?? ''));
        $visibility = $validated['visibility'] ?? 'any';

        $records = $config['model']::query()
            ->when($term !== '', fn ($query) => $query->where($config['title'], 'like', "%{$term}%"))
            ->when($visibility !== 'any', fn ($query) => $query->where($config['visibility'], $visibility === 'visible'))
            ->latest('updated_at')
            ->limit($validated['limit'] ?? 15)
            ->get();

        return $this->json([
            'entity' => $entity,
            'count' => $records->count(),
            'records' => $records->map(fn ($record): array => ContentRegistry::summarize($entity, $record))->all(),
        ]);
    }

    /**
     * Entities whose permission the admin holds.
     *
     * @return list<string>
     */
    protected function allowedEntities(): array
    {
        return array_values(array_filter(
            ContentRegistry::keys(),
            fn (string $entity): bool => $this->user->can(ContentRegistry::get($entity)['permission']),
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'entity' => $schema->string()->enum(ContentRegistry::keys())->required()->description('Which content type to search.'),
            'query' => $schema->string()->description('Optional keyword matched against the title/name.'),
            'visibility' => $schema->string()->enum(['visible', 'hidden', 'any'])->description('Filter by public visibility (default any).'),
            'limit' => $schema->integer()->min(1)->max(30)->description('Maximum results (default 15).'),
        ];
    }
}
