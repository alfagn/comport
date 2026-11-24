<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Manages the public visibility / active status of site pages.
 * Supports toggling pages on/off from the CMS, hiding them from
 * menus and sitemaps, and forcing a 404 response if visited directly.
 */
class PageStatusService
{
    /** Supported configurable public pages: [slug => human title]. */
    public const PAGES = [
        'home' => 'Home',
        'about' => 'About',
        'work' => 'Work & Case Studies',
        'team' => 'Team & Founders',
        'careers' => 'Careers Portal',
        'partners' => 'Corporate Partners',
        'blog' => 'Blog & Media',
        'contact' => 'Contact',
        'services' => 'Services',
    ];

    private const CACHE_KEY = 'cms_page_statuses';

    /**
     * Check whether a public page is active/live.
     * Defaults to true if no explicit setting has been saved.
     */
    public function isActive(string $page): bool
    {
        $statuses = $this->all();

        return (bool) ($statuses[$page] ?? true);
    }

    /**
     * Set a page's status (true for live, false for hidden/disabled).
     */
    public function setActive(string $page, bool $active): void
    {
        abort_unless(isset(self::PAGES[$page]), 404, "Invalid page key: {$page}");

        Setting::setValue("page_status_{$page}", $active ? '1' : '0', 'pages');

        $this->forgetCache();
    }

    /**
     * Get all page statuses as an associative array [page => bool].
     */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $settings = Setting::where('group', 'pages')
                ->where('key', 'like', 'page_status_%')
                ->pluck('value', 'key');

            $result = [];
            foreach (array_keys(self::PAGES) as $page) {
                $raw = $settings->get("page_status_{$page}");
                $result[$page] = $raw === null ? true : ($raw === '1' || $raw === 'true');
            }

            return $result;
        });
    }

    /**
     * Invalidate cached page statuses.
     */
    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        try {
            app(SeoService::class)->forgetPublicCaches();
        } catch (\Throwable) {
            // SeoService might not be bound during certain early boot cycles.
        }
    }
}
