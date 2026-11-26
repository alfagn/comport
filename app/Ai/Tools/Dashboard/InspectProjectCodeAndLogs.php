<?php

namespace App\Ai\Tools\Dashboard;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Laravel\Ai\Tools\Request;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Stringable;

/**
 * Allows the Dashboard AI Copilot to inspect safe project source code files,
 * search for classes/functions across the codebase, and read recent redacted
 * entries from storage/logs/laravel.log to diagnose technical issues.
 */
class InspectProjectCodeAndLogs extends DashboardTool
{
    /**
     * Allowed top-level project directories for code inspection.
     *
     * @var list<string>
     */
    private const ALLOWED_PREFIXES = [
        'app',
        'bootstrap',
        'config',
        'database',
        'public/assets',
        'resources/views',
        'routes',
        'tests',
        'composer.json',
        'package.json',
    ];

    public function permission(): ?string
    {
        return null;
    }

    public function description(): Stringable|string
    {
        return 'Read project source code files, list directory contents, search for classes/functions across the codebase, or inspect recent redacted entries in storage/logs/laravel.log to answer technical questions and debug errors.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'action' => $schema->string()
                ->enum(['read_file', 'list_files', 'search_code', 'recent_logs'])
                ->description('Action to perform: "read_file", "list_files", "search_code", or "recent_logs".')
                ->required(),
            'path' => $schema->string()
                ->description('Relative project path for "read_file" or "list_files" (e.g. "routes/web.php", "app/Services", "resources/views/admin").')
                ->nullable(),
            'query' => $schema->string()
                ->description('Search keyword or class/function name for "search_code" or optional log filter for "recent_logs".')
                ->nullable(),
            'start_line' => $schema->integer()
                ->description('1-based start line for "read_file" (default 1).')
                ->nullable(),
            'end_line' => $schema->integer()
                ->description('1-based end line for "read_file" (max 160 lines per read).')
                ->nullable(),
        ];
    }

    protected function run(Request $request): string
    {
        $action = (string) ($request['action'] ?? 'recent_logs');
        $path = trim((string) ($request['path'] ?? ''), " \t\n\r\0\x0B/\\");
        $query = trim((string) ($request['query'] ?? ''));

        return match ($action) {
            'read_file' => $this->readFile(
                $path,
                max(1, (int) ($request['start_line'] ?? 1)),
                max(1, (int) ($request['end_line'] ?? 140)),
            ),
            'list_files' => $this->listFiles($path ?: 'app'),
            'search_code' => $this->searchCode($query, $path ?: 'app'),
            'recent_logs' => $this->readRecentLogs($query),
            default => $this->json(['error' => 'Unsupported action.']),
        };
    }

    protected function readFile(string $relativePath, int $startLine, int $endLine): string
    {
        $normalized = str_replace('\\', '/', $relativePath);

        if (! $this->isPathAllowed($normalized)) {
            return $this->json([
                'error' => 'Access denied: only safe source directories (app, routes, config, database, resources/views, public/assets, composer.json) may be read.',
            ]);
        }

        $fullPath = base_path($normalized);
        $realBase = realpath(base_path());
        $realTarget = realpath($fullPath);

        if (! $realBase || ! $realTarget || ! str_starts_with($realTarget, $realBase) || ! is_file($realTarget)) {
            return $this->json(['error' => "File not found: {$normalized}"]);
        }

        $lines = @file($realTarget, FILE_IGNORE_NEW_LINES);
        if (! is_array($lines)) {
            return $this->json(['error' => "Unable to read file: {$normalized}"]);
        }

        $totalLines = count($lines);
        $startLine = min($startLine, max(1, $totalLines));
        $endLine = min($totalLines, min($endLine, $startLine + 159));

        $slice = [];
        for ($i = $startLine; $i <= $endLine; $i++) {
            $slice[] = $i.': '.$this->redactSecrets((string) ($lines[$i - 1] ?? ''));
        }

        return $this->json([
            'path' => $normalized,
            'total_lines' => $totalLines,
            'showing_lines' => "{$startLine}-{$endLine}",
            'content' => implode("\n", $slice),
        ]);
    }

    protected function listFiles(string $relativeDir): string
    {
        $normalized = str_replace('\\', '/', $relativeDir);

        if (! $this->isPathAllowed($normalized)) {
            return $this->json([
                'error' => 'Access denied: directory must be within app, routes, config, database, resources/views, or public/assets.',
            ]);
        }

        $fullPath = base_path($normalized);
        $realBase = realpath(base_path());
        $realTarget = realpath($fullPath);

        if (! $realBase || ! $realTarget || ! str_starts_with($realTarget, $realBase) || ! is_dir($realTarget)) {
            return $this->json(['error' => "Directory not found: {$normalized}"]);
        }

        $items = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($realTarget, RecursiveDirectoryIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $rel = str_replace('\\', '/', substr($file->getPathname(), strlen($realBase) + 1));
            $items[] = $rel;
            if (count($items) >= 120) {
                break;
            }
        }

        sort($items);

        return $this->json([
            'directory' => $normalized,
            'count' => count($items),
            'files' => $items,
        ]);
    }

    protected function searchCode(string $query, string $relativeDir): string
    {
        if (mb_strlen($query) < 2) {
            return $this->json(['error' => 'Please provide a search query of at least 2 characters.']);
        }

        $normalized = str_replace('\\', '/', $relativeDir);
        if (! $this->isPathAllowed($normalized)) {
            $normalized = 'app';
        }

        $fullPath = base_path($normalized);
        $realBase = realpath(base_path());
        $realTarget = realpath($fullPath);

        if (! $realBase || ! $realTarget || ! str_starts_with($realTarget, $realBase) || ! is_dir($realTarget)) {
            return $this->json(['error' => "Directory not found: {$normalized}"]);
        }

        $matches = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($realTarget, RecursiveDirectoryIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getSize() > 512 * 1024) {
                continue;
            }

            $ext = strtolower($file->getExtension());
            if (! in_array($ext, ['php', 'js', 'css', 'json', 'md'], true)) {
                continue;
            }

            $lines = @file($file->getPathname(), FILE_IGNORE_NEW_LINES);
            if (! is_array($lines)) {
                continue;
            }

            $rel = str_replace('\\', '/', substr($file->getPathname(), strlen($realBase) + 1));
            foreach ($lines as $idx => $line) {
                if (stripos($line, $query) !== false) {
                    $matches[] = [
                        'file' => $rel,
                        'line' => $idx + 1,
                        'snippet' => Str::limit(trim($this->redactSecrets($line)), 180),
                    ];
                    if (count($matches) >= 35) {
                        break 2;
                    }
                }
            }
        }

        return $this->json([
            'query' => $query,
            'directory' => $normalized,
            'matches_count' => count($matches),
            'matches' => $matches,
        ]);
    }

    protected function readRecentLogs(string $filter): string
    {
        $logPath = storage_path('logs/laravel.log');
        if (! File::exists($logPath)) {
            return $this->json([
                'status' => 'clean',
                'message' => 'No laravel.log file exists yet (zero recorded errors).',
            ]);
        }

        $size = File::size($logPath);
        $readBytes = min($size, 48 * 1024);
        $handle = @fopen($logPath, 'rb');
        if (! $handle) {
            return $this->json(['error' => 'Could not open laravel.log.']);
        }

        if ($size > $readBytes) {
            fseek($handle, -$readBytes, SEEK_END);
        }
        $raw = (string) fread($handle, $readBytes);
        fclose($handle);

        $lines = array_values(array_filter(
            explode("\n", $raw),
            fn (string $l): bool => trim($l) !== '',
        ));

        if ($filter !== '') {
            $lines = array_values(array_filter(
                $lines,
                fn (string $l): bool => stripos($l, $filter) !== false,
            ));
        }

        $tail = array_slice($lines, -60);
        $redacted = array_map(fn (string $l): string => Str::limit($this->redactSecrets($l), 350), $tail);

        return $this->json([
            'log_file' => 'storage/logs/laravel.log',
            'file_size_kb' => round($size / 1024, 1),
            'entries_returned' => count($redacted),
            'lines' => $redacted,
        ]);
    }

    protected function isPathAllowed(string $normalizedPath): bool
    {
        if ($normalizedPath === '' || str_contains($normalizedPath, '..') || str_starts_with($normalizedPath, '.env')) {
            return false;
        }

        foreach (self::ALLOWED_PREFIXES as $prefix) {
            if ($normalizedPath === $prefix || str_starts_with($normalizedPath, $prefix.'/')) {
                return true;
            }
        }

        return false;
    }

    protected function redactSecrets(string $text): string
    {
        return (string) preg_replace(
            '/(GEMINI_API_KEY|OPENAI_API_KEY|ANTHROPIC_API_KEY|WHATSAPP_ACCESS_TOKEN|WHATSAPP_APP_SECRET|APP_KEY|DB_PASSWORD|Bearer\s+)[=:\s]*[^\s"\',]+/i',
            '$1=[REDACTED]',
            $text,
        );
    }
}
