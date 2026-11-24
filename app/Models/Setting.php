<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Site-wide key/value setting.
 */
class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value'];

    /**
     * Read a setting value with an optional default.
     */
    public static function getValue(string $key, ?string $default = null): ?string
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    /**
     * Create or update a setting by key, preserving existing group or inferring
     * the right namespace so SEO, Brand, and Cookie keys never leak into General.
     */
    public static function setValue(string $key, ?string $value, ?string $group = null): void
    {
        $resolvedGroup = $group
            ?? static::query()->where('key', $key)->value('group')
            ?? static::inferGroup($key);

        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $resolvedGroup]
        );
    }

    /** Infer logical setting group from key naming conventions. */
    public static function inferGroup(string $key): string
    {
        return match (true) {
            str_starts_with($key, 'seo_') => 'seo',
            str_starts_with($key, 'brand_') => 'brand',
            str_starts_with($key, 'cookie_') => 'cookies',
            str_starts_with($key, 'sitemap_') => 'sitemap',
            in_array($key, ['email', 'partners_email', 'press_email', 'talent_email', 'phone', 'address'], true) => 'contact',
            in_array($key, ['linkedin', 'instagram', 'x', 'youtube', 'github'], true) => 'social',
            default => 'general',
        };
    }
}
