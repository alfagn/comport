<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Throwable;

/**
 * Transparent AES-256 authenticated encryption cast (`APP_KEY`) for text columns,
 * while remaining backward-compatible with pre-existing plaintext rows.
 *
 * @implements CastsAttributes<string|null, string|null>
 */
class EncryptedTextOrPlain implements CastsAttributes
{
    public const PREFIX = 'enc:v1:';

    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return $value === '' ? '' : null;
        }

        $raw = (string) $value;

        if (! str_starts_with($raw, self::PREFIX)) {
            return $raw;
        }

        try {
            return Crypt::decryptString(substr($raw, strlen(self::PREFIX)));
        } catch (Throwable) {
            return null;
        }
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        $str = (string) $value;
        if ($str === '') {
            return '';
        }

        if (str_starts_with($str, self::PREFIX)) {
            return $str;
        }

        return self::PREFIX.Crypt::encryptString($str);
    }
}
