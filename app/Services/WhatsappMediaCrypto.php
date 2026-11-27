<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Encrypts & decrypts WhatsApp media binaries (voice notes, photos, videos, documents)
 * at rest on local private storage using Laravel's AES-256 authenticated encryption,
 * while remaining backward-compatible with pre-existing unencrypted files.
 */
class WhatsappMediaCrypto
{
    public const MAGIC_HEADER = 'EMPWAENC1:';

    /**
     * Encrypt raw binary bytes for at-rest storage.
     */
    public function encryptBytes(string $plainBytes): string
    {
        if ($plainBytes === '' || str_starts_with($plainBytes, self::MAGIC_HEADER)) {
            return $plainBytes;
        }

        return self::MAGIC_HEADER.Crypt::encryptString(base64_encode($plainBytes));
    }

    /**
     * Decrypt stored bytes (or return raw bytes if file predates encryption).
     */
    public function decryptBytes(string $storedBytes): string
    {
        if (! str_starts_with($storedBytes, self::MAGIC_HEADER)) {
            return $storedBytes;
        }

        try {
            $b64 = Crypt::decryptString(substr($storedBytes, strlen(self::MAGIC_HEADER)));
            $decoded = base64_decode($b64, true);

            return is_string($decoded) ? $decoded : '';
        } catch (Throwable) {
            return '';
        }
    }

    /**
     * Store binary media encrypted on the given disk.
     */
    public function putEncrypted(string $disk, string $path, string $plainBytes): void
    {
        Storage::disk($disk)->put($path, $this->encryptBytes($plainBytes));
    }

    /**
     * Read and decrypt binary media from the given disk.
     */
    public function getDecrypted(string $disk, string $path): ?string
    {
        if (! Storage::disk($disk)->exists($path)) {
            return null;
        }

        $raw = (string) Storage::disk($disk)->get($path);

        return $this->decryptBytes($raw);
    }
}
