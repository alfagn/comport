<?php

use App\Models\Setting;
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

if (! function_exists('setting')) {
    /**
     * Read a site setting with a template default, for editable global
     * values such as the public email, phone, address and social links.
     */
    function setting(string $key, ?string $default = null): ?string
    {
        return Setting::getValue($key, $default);
    }
}
