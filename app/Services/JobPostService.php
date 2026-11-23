<?php

namespace App\Services;

use App\Core\Services\BaseService;
use App\DTOs\JobPostData;
use App\Repositories\Contracts\JobPostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Business logic for the JobPost entity.
 *
 * Thin orchestration layer: validates nothing (FormRequests did), queries
 * nothing directly (the repository does); it generates slugs, handles
 * uploads and delegates persistence.
 */
class JobPostService extends BaseService
{
    public function __construct(JobPostRepositoryInterface $repository)
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

    /**
     * Section lines arrive as "Heading || item || item" text; convert to
     * structured arrays before JSON storage.
     */
    private function normalizeSections(array $attributes): array
    {
        if (isset($attributes['sections']) && is_array($attributes['sections'])) {
            $attributes['sections'] = array_values(array_map(function (string $line) {
                $parts = array_values(array_filter(array_map('trim', explode('||', $line))));

                return ['heading' => $parts[0] ?? '', 'items' => array_slice($parts, 1)];
            }, $attributes['sections']));
        }

        return $attributes;
    }

    /** Create a record from validated input. */
    public function create(JobPostData $data, ?UploadedFile $noFile = null): Model
    {
        $attributes = $this->normalizeSections($this->normalizeJsonLines($data->toArray()));
        $attributes['slug'] = $attributes['slug'] ?? Str::slug($attributes['title'] ?? $attributes['name'] ?? '');

        return $this->repository->create($attributes);
    }

    /** Update a record from validated input. */
    public function update(int $id, JobPostData $data, ?UploadedFile $noFile = null): Model
    {
        $record = $this->repository->find($id);
        $attributes = $this->normalizeSections($this->normalizeJsonLines($data->toArray()));
        $attributes['slug'] = $attributes['slug'] ?? $record->slug ?? null;

        return $this->repository->update($id, $attributes);
    }

    /** Delete a record. */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
