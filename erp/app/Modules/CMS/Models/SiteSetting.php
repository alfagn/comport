<?php

declare(strict_types=1);

namespace App\Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SiteSetting
 *
 * Key-value configuration for the company's public website and branding.
 * Stored on landlord connection with in-memory request caching for zero N+1 queries.
 */
class SiteSetting extends Model
{
    protected $connection = 'landlord';

    protected $table = 'site_settings';

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    /**
     * In-memory cache of settings keyed by setting key.
     *
     * @var array<string, mixed>|null
     */
    protected static ?array $cachedSettings = null;

    /**
     * Load all settings into memory in a single query.
     *
     * @return array<string, mixed>
     */
    public static function allCached(): array
    {
        if (static::$cachedSettings === null) {
            static::$cachedSettings = [];
            try {
                $rows = static::query()->get();
                foreach ($rows as $row) {
                    static::$cachedSettings[$row->key] = is_array($row->value) && array_key_exists('val', $row->value)
                        ? $row->value['val']
                        : $row->value;
                }
            } catch (\Throwable) {
                static::$cachedSettings = [];
            }
        }

        return static::$cachedSettings;
    }

    /**
     * Clear the in-memory settings cache.
     */
    public static function flushCache(): void
    {
        static::$cachedSettings = null;
    }

    /**
     * Retrieve a setting value with fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::allCached();

        if (array_key_exists($key, $all) && $all[$key] !== null && $all[$key] !== '') {
            return $all[$key];
        }

        return $default;
    }

    /**
     * Set a setting value and update in-memory cache.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): self
    {
        $record = static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => ['val' => $value], 'group' => $group]
        );

        if (static::$cachedSettings !== null) {
            static::$cachedSettings[$key] = $value;
        }

        return $record;
    }
}
