<?php

use App\Models\Setting;
use App\Services\ContentListItemService;
use App\Services\NavItemService;
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

if (! function_exists('nav_items')) {
    /**
     * Active links of one public menu (utility bar, mobile drawer),
     * dashboard-editable and cached by NavItemService.
     */
    function nav_items(string $menu): \Illuminate\Support\Collection
    {
        return app(NavItemService::class)->menu($menu);
    }
}

if (! function_exists('content_list')) {
    /**
     * Active cards of one repeating content list (principles, timeline,
     * benefits, ...), dashboard-editable and cached by ContentListItemService.
     */
    function content_list(string $group): \Illuminate\Support\Collection
    {
        return app(ContentListItemService::class)->list($group);
    }
}

if (! function_exists('nav_mega')) {
    /**
     * One mega-menu panel (columns with child links / feature cards),
     * dashboard-editable under Navigation and cached by NavItemService.
     */
    function nav_mega(string $menu): \Illuminate\Support\Collection
    {
        return app(NavItemService::class)->mega($menu);
    }
}

if (! function_exists('brand_favicon')) {
    /**
     * Dashboard-uploaded favicon (Settings > Brand assets), falling back to
     * the shipped Empetix mark until one is uploaded.
     */
    function brand_favicon(): string
    {
        $path = setting('brand_favicon');

        return $path ? asset('storage/'.$path) : asset('assets/img/favicon.svg');
    }
}

if (! function_exists('brand_logo')) {
    /**
     * Dashboard-uploaded site logo (Settings > Brand assets), falling back
     * to the shipped Empetix logo until one is uploaded.
     */
    function brand_logo(bool $dark = false): string
    {
        $path = setting($dark ? 'brand_logo_dark' : 'brand_logo');
        $default = $dark ? 'assets/img/logo/empetix-logo-dark.png' : 'assets/img/logo/empetix-logo.png';

        return $path ? asset('storage/'.$path) : asset($default);
    }
}
