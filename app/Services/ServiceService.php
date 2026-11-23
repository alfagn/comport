<?php

namespace App\Services;

use App\Core\Services\BaseService;
use App\DTOs\ServiceData;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Business logic for the Service entity.
 *
 * Thin orchestration layer: validates nothing (FormRequests did), queries
 * nothing directly (the repository does); it generates slugs, handles
 * uploads and delegates persistence.
 */
class ServiceService extends BaseService
{
    public function __construct(ServiceRepositoryInterface $repository)
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
    public function create(ServiceData $data, ?UploadedFile $imageFile = null): Model
    {
        $attributes = $this->normalizeJsonLines($data->toArray());
        $attributes['slug'] = $attributes['slug'] ?? Str::slug($attributes['title'] ?? $attributes['name'] ?? '');
        $attributes['image'] = $this->storeUpload($imageFile, 'uploads/services');

        return $this->repository->create($attributes);
    }

    /** Update a record from validated input. */
    public function update(int $id, ServiceData $data, ?UploadedFile $imageFile = null): Model
    {
        $record = $this->repository->find($id);
        $attributes = $this->normalizeJsonLines($data->toArray());
        $attributes['slug'] = $attributes['slug'] ?? $record->slug ?? null;
        $attributes['image'] = $this->storeUpload($imageFile, 'uploads/services', $record->image);

        return $this->repository->update($id, $attributes);
    }

    /** Delete a record. */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /** @inheritDoc */
    protected function attributeDefaults(): array
    {
        return ['is_active' => true, 'sort_order' => 0];
    }
}
