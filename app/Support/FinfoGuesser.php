<?php

declare(strict_types=1);

namespace App\Support;

class FinfoGuesser
{
    public static function detectFromFile(string $path): string
    {
        if (! is_file($path) || ! is_readable($path)) {
            return self::detectFromExtension(pathinfo($path, PATHINFO_EXTENSION));
        }

        // 1. Try getimagesize() for image uploads (standard in PHP GD, works on temp files without extension)
        $img = @getimagesize($path);
        if (is_array($img) && ! empty($img['mime']) && is_string($img['mime'])) {
            return $img['mime'];
        }

        // 2. Read first 512 bytes and check binary magic bytes
        $fh = @fopen($path, 'rb');
        if ($fh) {
            $bytes = (string) @fread($fh, 512);
            @fclose($fh);
            $mime = self::detectFromBuffer($bytes);
            if ($mime !== 'application/octet-stream') {
                return $mime;
            }
        }

        // 3. Fallback to extension
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        return self::detectFromExtension($ext);
    }

    public static function detectFromBuffer(string $bytes): string
    {
        if ($bytes === '') {
            return 'application/octet-stream';
        }

        // PNG: 89 50 4E 47 0D 0A 1A 0A
        if (str_starts_with($bytes, "\x89PNG\r\n\x1a\n")) {
            return 'image/png';
        }

        // JPEG: FF D8 FF
        if (str_starts_with($bytes, "\xFF\xD8\xFF")) {
            return 'image/jpeg';
        }

        // GIF: GIF87a or GIF89a
        if (str_starts_with($bytes, 'GIF87a') || str_starts_with($bytes, 'GIF89a')) {
            return 'image/gif';
        }

        // WebP: RIFF....WEBP
        if (strlen($bytes) >= 12 && str_starts_with($bytes, 'RIFF') && substr($bytes, 8, 4) === 'WEBP') {
            return 'image/webp';
        }

        // AVIF: ....ftypavif
        if (strlen($bytes) >= 12 && substr($bytes, 4, 4) === 'ftyp' && substr($bytes, 8, 4) === 'avif') {
            return 'image/avif';
        }

        // MP4 / MOV: ....ftyp
        if (strlen($bytes) >= 12 && substr($bytes, 4, 4) === 'ftyp') {
            $brand = substr($bytes, 8, 4);
            return $brand === 'qt  ' ? 'video/quicktime' : 'video/mp4';
        }

        // WebM / MKV: 1A 45 DF A3
        if (str_starts_with($bytes, "\x1A\x45\xDF\xA3")) {
            return 'video/webm';
        }

        // PDF: %PDF-
        if (str_starts_with($bytes, '%PDF-')) {
            return 'application/pdf';
        }

        // ZIP: PK\x03\x04
        if (str_starts_with($bytes, "PK\x03\x04")) {
            return 'application/zip';
        }

        // ICO: \x00\x00\x01\x00
        if (str_starts_with($bytes, "\x00\x00\x01\x00")) {
            return 'image/x-icon';
        }

        // SVG: XML or <svg
        $sample = strtolower(substr($bytes, 0, 300));
        if (str_contains($sample, '<svg') || (str_contains($sample, '<?xml') && str_contains($sample, '<svg'))) {
            return 'image/svg+xml';
        }

        return 'application/octet-stream';
    }

    public static function detectFromExtension(string $ext): string
    {
        $ext = strtolower(trim($ext, '.'));

        return match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'avif' => 'image/avif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'pdf' => 'application/pdf',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'mov' => 'video/quicktime',
            'zip' => 'application/zip',
            'json' => 'application/json',
            'txt' => 'text/plain',
            'csv' => 'text/csv',
            default => 'application/octet-stream',
        };
    }
}
