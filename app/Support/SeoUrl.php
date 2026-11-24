<?php

namespace App\Support;

use App\Models\Setting;

/** One origin for canonical links and discovery URLs, independent of request hosts. */
final class SeoUrl
{
    public static function validOrigin(?string $value): bool
    {
        $parts = parse_url(trim($value ?? ''));

        return is_array($parts)
            && in_array(strtolower($parts['scheme'] ?? ''), ['https', 'http'], true)
            && ! empty($parts['host'])
            && ! isset($parts['user']) && ! isset($parts['pass'])
            && ! isset($parts['query']) && ! isset($parts['fragment'])
            && in_array($parts['path'] ?? '', ['', '/'], true);
    }

    public static function to(string $path = '/'): string
    {
        $saved = trim(Setting::getValue('seo_canonical_base') ?? '');
        $base = self::validOrigin($saved) ? $saved : config('app.url');

        return rtrim($base, '/').'/'.ltrim($path, '/');
    }

    public static function current(): string
    {
        return self::to(request()->path() === '/' ? '/' : request()->path());
    }
}
