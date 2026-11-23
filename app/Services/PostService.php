<?php

namespace App\Services;

use App\Core\Services\BaseService;
use App\DTOs\PostData;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Business logic for the Post entity.
 *
 * Thin orchestration layer: validates nothing (FormRequests did), queries
 * nothing directly (the repository does); it generates slugs, handles
 * uploads and delegates persistence.
 */
class PostService extends BaseService
{
    public function __construct(PostRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /** Paginated list for the dashboard index page. */
    public function paginateForAdmin(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    /** Load one record for the edit form. */
    public function findForEdit(int $id): Model
    {
        return $this->repository->find($id);
    }

    /** Create a record from validated input. */
    public function create(PostData $data, ?UploadedFile $cover_imageFile = null): Model
    {
        $attributes = $this->normalizeJsonLines($data->toArray());
        $attributes['slug'] = $attributes['slug'] ?? Str::slug($attributes['title'] ?? $attributes['name'] ?? '');
        $attributes['cover_image'] = $this->storeUpload($cover_imageFile, 'uploads/posts');

        if (array_key_exists('meta', $attributes)) {
            $attributes['meta'] = $this->mergePostMeta([], (array) ($attributes['meta'] ?? []));
        }

        return $this->repository->create($attributes);
    }

    /** Update a record from validated input. */
    public function update(int $id, PostData $data, ?UploadedFile $cover_imageFile = null): Model
    {
        $record = $this->repository->find($id);
        $attributes = $this->normalizeJsonLines($data->toArray());
        $attributes['slug'] = $attributes['slug'] ?? $record->slug ?? null;
        $attributes['cover_image'] = $this->storeUpload($cover_imageFile, 'uploads/posts', $record->cover_image);

        if (array_key_exists('meta', $attributes)) {
            $existing = is_array($record->meta) ? $record->meta : [];
            $attributes['meta'] = $this->mergePostMeta($existing, (array) ($attributes['meta'] ?? []));
        }

        return $this->repository->update($id, $attributes);
    }

    /**
     * Merge submitted meta lines into existing structured post metadata.
     * Preserves existing structured keys (author_name, author_role, author_avatar, toc, etc.)
     * while updating card_label and read_time.
     */
    protected function mergePostMeta(array $existing, array $lines): array
    {
        $meta = $existing;
        $cleanLines = array_values(array_filter(array_map('trim', $lines), fn($l) => $l !== ''));

        if (empty($cleanLines)) {
            $meta['card_label'] = null;
            $meta['read_time'] = null;
            return $meta;
        }

        $hasKeyValues = false;
        foreach ($cleanLines as $line) {
            if (str_contains($line, ':')) {
                [$k, $v] = explode(':', $line, 2);
                $meta[trim($k)] = trim($v);
                $hasKeyValues = true;
            }
        }

        if (!$hasKeyValues) {
            if (count($cleanLines) === 1) {
                if (str_contains(strtoupper($cleanLines[0]), 'MIN READ') || str_contains(strtoupper($cleanLines[0]), 'MIN')) {
                    $meta['read_time'] = $cleanLines[0];
                } else {
                    $meta['card_label'] = $cleanLines[0];
                }
            } else {
                $meta['card_label'] = $cleanLines[0];
                $meta['read_time'] = $cleanLines[1];
            }
        }

        return $meta;
    }

    /** Delete a record. */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /** @inheritDoc */
    protected function attributeDefaults(): array
    {
        return ['is_published' => false];
    }
}
