<?php

namespace App\Core\Services;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

/**
 * Shared service behavior.
 *
 * Services hold business logic: they orchestrate repositories, file
 * uploads and any cross-entity work. They never see Request objects;
 * controllers hand them DTOs (Single Responsibility + testability).
 */
abstract class BaseService
{
    public function __construct(protected RepositoryInterface $repository) {}

    /**
     * JSON-backed list fields arrive from dashboard forms as one-per-line
     * text. Convert those strings to arrays before persistence so models
     * can cast them to JSON.
     */
    protected function normalizeJsonLines(array $attributes): array
    {
        $listFields = ['deliverables', 'meta', 'pills', 'responsibilities', 'requirements', 'bio', 'services', 'tags'];

        foreach ($listFields as $field) {
            if (isset($attributes[$field]) && is_string($attributes[$field])) {
                $lines = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $attributes[$field]))));
                $attributes[$field] = $lines;
            }
        }

        return $attributes;
    }

    /**
     * Store an uploaded file on the public disk and return its path,
     * or return the current value when no new file was sent.
     */
    protected function storeUpload(?UploadedFile $file, string $directory, ?string $current = null): ?string
    {
        if (! $file) {
            return $current;
        }

        // Remove the replaced file so the disk does not fill with orphans.
        if ($current && str_starts_with($current, 'uploads/')) {
            Storage::disk('public')->delete($current);
        }

        return $file->store($directory, 'public');
    }
}
