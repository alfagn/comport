<?php

declare(strict_types=1);

/**
 * Polyfill for PHP's ext-fileinfo extension (finfo class and constants).
 *
 * aaPanel / VPS environments sometimes run PHP without ext-fileinfo compiled or enabled.
 * Without this polyfill, Flysystem's FinfoMimeTypeDetector and Symfony's MimeTypes crash
 * with fatal error: Class "finfo" not found.
 */

if (! defined('FILEINFO_NONE')) {
    define('FILEINFO_NONE', 0);
}
if (! defined('FILEINFO_SYMLINK')) {
    define('FILEINFO_SYMLINK', 2);
}
if (! defined('FILEINFO_MIME_TYPE')) {
    define('FILEINFO_MIME_TYPE', 16);
}
if (! defined('FILEINFO_MIME_ENCODING')) {
    define('FILEINFO_MIME_ENCODING', 1024);
}
if (! defined('FILEINFO_MIME')) {
    define('FILEINFO_MIME', 1040);
}

if (! class_exists('finfo')) {
    class finfo
    {
        public function __construct(
            public int $flags = FILEINFO_MIME_TYPE,
            public ?string $magicFile = null
        ) {}

        public function file(string $filename, int $flags = FILEINFO_NONE, mixed $context = null): string|false
        {
            return \App\Support\FinfoGuesser::detectFromFile($filename);
        }

        public function buffer(string $string, int $flags = FILEINFO_NONE, mixed $context = null): string|false
        {
            return \App\Support\FinfoGuesser::detectFromBuffer($string);
        }
    }
}

if (! function_exists('finfo_open')) {
    function finfo_open(int $flags = FILEINFO_MIME_TYPE, ?string $magicFile = null): finfo|false
    {
        return new finfo($flags, $magicFile);
    }
}

if (! function_exists('finfo_file')) {
    function finfo_file(finfo $finfo, string $filename, int $flags = FILEINFO_NONE, mixed $context = null): string|false
    {
        return $finfo->file($filename, $flags, $context);
    }
}

if (! function_exists('finfo_buffer')) {
    function finfo_buffer(finfo $finfo, string $string, int $flags = FILEINFO_NONE, mixed $context = null): string|false
    {
        return $finfo->buffer($string, $flags, $context);
    }
}

if (! function_exists('finfo_close')) {
    function finfo_close(finfo $finfo): bool
    {
        return true;
    }
}
