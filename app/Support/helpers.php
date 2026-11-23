<?php

use App\Services\SectionService;

if (! function_exists('sec')) {
    /**
     * Read an editable page section for a Blade template.
     *
     * sec('home', 'hero_title', 'Default copy') returns the dashboard-edited
     * value, or the given default when the section was never saved.
     */
    function sec(string $page, string $key, ?string $default = null): ?string
    {
        return app(SectionService::class)->get($page, $key, $default);
    }
}
