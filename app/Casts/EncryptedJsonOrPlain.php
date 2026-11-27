<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Throwable;

/**
 * Transparent AES-256 authenticated encryption cast (`APP_KEY`) for structured
 * array/JSON columns, with seamless fallback for pre-existing JSON rows.
 *
 * @implements CastsAttributes<array<string, mixed>|null, array<string, mixed>|string|null>
 */
class EncryptedJsonOrPlain implements CastsAttributes
{
    public const PREFIX = 'enc:v1:';

    /**
     * @return array<string, mixed>|null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        $raw = (string) $value;

        if (str_starts_with($raw, self::PREFIX)) {
            try {
                $json = Crypt::decryptString(substr($raw, strlen(self::PREFIX)));
                $decoded = json_decode($json, true);

                return is_array($decoded) ? $decoded : null;
            } catch (Throwable) {
                return null;
            }
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === []) {
            return null;
        }

        if (is_string($value) && str_starts_with($value, self::PREFIX)) {
            return $value;
        }

        $json = is_string($value)
            ? $value
            : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        return self::PREFIX.Crypt::encryptString((string) $json);
    }
}
