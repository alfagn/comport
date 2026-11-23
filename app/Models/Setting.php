<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Site-wide key/value setting. See the settings migration for grouping.
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
     * Create or update a setting by key.
     */
    public static function setValue(string $key, ?string $value, string $group = 'general'): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
    }
}
