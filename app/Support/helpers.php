<?php

use App\Models\Setting;
use App\Services\ContentListItemService;
use App\Services\NavItemService;
use App\Services\SectionService;
use App\Support\SeoUrl;
use Illuminate\Support\Collection;

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
    function nav_items(string $menu): Collection
    {
        return app(NavItemService::class)->menu($menu);
    }
}

if (! function_exists('content_list')) {
    /**
     * Active cards of one repeating content list (principles, timeline,
     * benefits, ...), dashboard-editable and cached by ContentListItemService.
     */
    function content_list(string $group): Collection
    {
        return app(ContentListItemService::class)->list($group);
    }
}

if (! function_exists('nav_mega')) {
    /**
     * One mega-menu panel (columns with child links / feature cards),
     * dashboard-editable under Navigation and cached by NavItemService.
     */
    function nav_mega(string $menu): Collection
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

        return $path ? asset('storage/'.$path) : asset('assets/img/favicon.png');
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

if (! function_exists('content_asset')) {
    /** Resolve a CMS media path, supporting private-storage videos, public uploads, and HTTPS media. */
    function content_asset(?string $path): string
    {
        $path = trim($path ?? '');
        if (str_starts_with($path, 'private:videos/')) {
            return route('media.video', ['path' => substr($path, strlen('private:'))]);
        }
        if (str_starts_with($path, 'https://')) {
            return $path;
        }
        if (str_contains($path, ':') || str_starts_with($path, '//')) {
            return '';
        }

        return asset(str_starts_with($path, 'uploads/') ? 'storage/'.$path : $path);
    }
}

if (! function_exists('video_mime')) {
    /** Infer HTML5 <source type="..."> MIME from a CMS video reference or URL. */
    function video_mime(?string $path): string
    {
        $clean = strtolower((string) parse_url((string) $path, PHP_URL_PATH));

        return match (true) {
            str_ends_with($clean, '.webm') => 'video/webm',
            str_ends_with($clean, '.ogg'), str_ends_with($clean, '.ogv') => 'video/ogg',
            str_ends_with($clean, '.mov') => 'video/quicktime',
            default => 'video/mp4',
        };
    }
}

if (! function_exists('seo_meta_title')) {
    /**
     * Read the configured site title in the requested language (or fallback).
     */
    function seo_meta_title(string $lang = 'en'): string
    {
        $key = $lang === 'ar' ? 'seo_meta_title_ar' : 'seo_meta_title_en';
        $default = $lang === 'ar'
            ? 'حلول إمبيتكس — استوديو هندسة البرمجيات والساس والتحول الرقمي'
            : 'Empetix Solutions — Software, SaaS & Digital Growth Studio';

        return setting($key, $default);
    }
}

if (! function_exists('seo_meta_description')) {
    /**
     * Read the configured meta description in the requested language.
     */
    function seo_meta_description(string $lang = 'en'): string
    {
        $key = $lang === 'ar' ? 'seo_meta_description_ar' : 'seo_meta_description_en';
        $default = $lang === 'ar'
            ? 'إمبيتكس (Empetix Solutions) هو استوديو هندسي متخصص في بناء وتطوير برمجيات الساس (SaaS)، المنصات السحابية، والتطبيقات الرقمية المتقدمة بتجربة مستخدم عربية وعالمية فائقة السرعة.'
            : 'Empetix Solutions is a premier digital engineering studio architecting enterprise SaaS platforms, custom software, FinTech solutions, and high-conversion web & mobile apps.';

        return setting($key, $default);
    }
}

if (! function_exists('seo_keywords')) {
    /**
     * Read combined bilingual SEO and AI search keywords.
     */
    function seo_keywords(): string
    {
        $en = setting('seo_keywords_en', 'Empetix, Empetix Solution, SaaS, Custom Software, Web Development, Mobile Apps, Flutter, Laravel, FinTech, Cairo, Riyadh, AI Engineering');
        $ar = setting('seo_keywords_ar', 'إمبيتكس, حلول إمبيتكس, شركة إمبيتكس, شركة برمجة, تطوير ساس, برمجة مواقع, تصميم تطبيقات, تحول رقمي, مصر, السعودية');

        return trim("{$en}, {$ar}", ', ');
    }
}

if (! function_exists('seo_og_image')) {
    /**
     * Resolve default OpenGraph social sharing preview card image.
     */
    function seo_og_image(): string
    {
        $path = setting('seo_og_image');
        if ($path) {
            return asset('storage/'.$path);
        }

        return content_asset(sec('global', 'social_preview', 'assets/img/og/empetix-share.jpg'));
    }
}

if (! function_exists('seo_url')) {
    /** Canonical origin plus an explicit public path. */
    function seo_url(string $path = '/'): string
    {
        return SeoUrl::to($path);
    }
}
if (! function_exists('seo_current_url')) {
    /** Query strings never create a second canonical page. */
    function seo_current_url(): string
    {
        return SeoUrl::current();
    }
}

if (! function_exists('versioned_asset')) {
    /** Append file modification timestamp so CDNs (Cloudflare/Nginx) and browsers never serve stale CSS/JS. */
    function versioned_asset(string $path): string
    {
        $full = public_path($path);
        $ver = is_file($full) ? filemtime($full) : null;

        return asset($path).($ver ? '?v='.$ver : '');
    }
}
