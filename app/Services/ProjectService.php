<?php

namespace App\Services;

use App\Core\Services\BaseService;
use App\DTOs\ProjectData;
use App\Models\Project;
use App\Models\Tag;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Business logic for portfolio projects, including tag synchronization
 * (the one relationship this entity owns).
 */
class ProjectService extends BaseService
{
    public function __construct(ProjectRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /** Paginated list for the dashboard index page. */
    public function paginateForAdmin(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    /** Load one project with its tags for the edit form. */
    public function findForEdit(int $id): Project
    {
        return $this->repository->find($id)->load('tags');
    }

    /** Create a project and sync its tags. */
    public function create(ProjectData $data, ?UploadedFile $imageFile = null): Project
    {
        $attributes = $this->normalizeCaseStudy($this->normalizeJsonLines($data->toArray()));
        $attributes['slug'] = $attributes['slug'] ?? Str::slug($attributes['title']);
        $attributes['image'] = $this->storeUpload($imageFile, 'uploads/projects');

        /** @var Project $project */
        $project = $this->repository->create($attributes);
        $this->syncTags($project, $data->tags);

        return $project;
    }

    /** Update a project and re-sync its tags. */
    public function update(int $id, ProjectData $data, ?UploadedFile $imageFile = null): Project
    {
        $record = $this->repository->find($id);
        $attributes = $this->normalizeCaseStudy($this->normalizeJsonLines($data->toArray()));
        $attributes['slug'] = $attributes['slug'] ?? $record->slug;
        $attributes['image'] = $this->storeUpload($imageFile, 'uploads/projects', $record->image);

        /** @var Project $project */
        $project = $this->repository->update($id, $attributes);
        $this->syncTags($project, $data->tags);

        return $project;
    }

    /** Delete a project (its pivot rows cascade). */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Sync tags from a comma-separated string or array of tag names,
     * creating missing tags on the fly.
     */
    private function syncTags(Project $project, mixed $tags): void
    {
        if (is_string($tags)) {
            $tags = array_filter(array_map('trim', explode(',', $tags)));
        }

        if (empty($tags)) {
            $project->tags()->sync([]);

            return;
        }

        $ids = collect($tags)->map(
            fn (string $name) => Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id
        );

        $project->tags()->sync($ids);
    }
}
