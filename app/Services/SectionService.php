<?php

namespace App\Services;

use App\Models\PageSection;
use App\Repositories\Contracts\PageSectionRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

/**
 * Reads and writes editable page sections.
 *
 * Values are cached as one map so public pages never query per section;
 * any dashboard edit flushes the cache. Templates call the sec() helper,
 * which falls back to the hard-coded default when a section was never
 * saved — so new template copy works before it is registered in the CMS.
 */
class SectionService
{
    /** Cache key for the flattened "page.key => value" map. */
    private const CACHE_KEY = 'page_sections.map';

    public function __construct(private PageSectionRepositoryInterface $repository) {}

    /**
     * Resolve one section value for templates.
     */
    public function get(string $page, string $key, ?string $default = null): ?string
    {
        return $this->map()["{$page}.{$key}"] ?? $default;
    }

    /** All sections of a page for the dashboard editor. */
    public function forPage(string $page): Collection
    {
        $defaults = collect(json_decode(file_get_contents(database_path('seeders/data/template-defaults.json')), true))
            ->where('page', $page)->keyBy('key');
        return $this->repository->forPage($page)->each(function (PageSection $section) use ($defaults) {
            // Show the exact template fallback without writing over the database.
            if ($section->value === null && isset($defaults[$section->key])) {
                $section->value = $defaults[$section->key]['value'];
            }
        });
    }

    /** Sections grouped by page for the dashboard index. */
    public function groupedByPage(): Collection
    {
        return $this->repository->groupedByPage();
    }

    /**
     * Persist a page editor submission and flush the cache so the public
     * site reflects the change on the next request.
     */
    public function savePage(string $page, array $values): void
    {
        foreach ($values as $key => $value) {
            PageSection::query()->where('page', $page)->where('key', $key)->update(['value' => $value]);
        }

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Register the template defaults as editable rows. Existing rows keep
     * their edited values; only missing keys are inserted (idempotent).
     */
    public function registerDefaults(array $defaults): void
    {
        foreach ($defaults as $row) {
            PageSection::query()->firstOrCreate(
                ['page' => $row['page'], 'key' => $row['key']],
                ['label' => $row['label'], 'type' => $row['type'] ?? 'text', 'value' => $row['value'] ?? null, 'sort_order' => $row['sort_order'] ?? 0],
            );
        }

        Cache::forget(self::CACHE_KEY);
    }

    /** The cached "page.key => value" lookup map. */
    private function map(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return PageSection::query()
                ->whereNotNull('value')
                ->get()
                ->mapWithKeys(fn (PageSection $s) => ["{$s->page}.{$s->key}" => $s->value])
                ->all();
        });
    }
}
