<?php

declare(strict_types=1);

namespace App\Modules\TenantCore\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CompanySetting
 *
 * Configurable company profile stored in each tenant's database.
 */
class CompanySetting extends Model
{
    protected $connection = 'tenant';

    protected $table = 'company_settings';

    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::on('tenant')->where('key', $key)->first();

        return $setting ? ($setting->value['val'] ?? $setting->value) : $default;
    }

    public static function set(string $key, mixed $value): self
    {
        return static::on('tenant')->updateOrCreate(
            ['key' => $key],
            ['value' => ['val' => $value]]
        );
    }
}
