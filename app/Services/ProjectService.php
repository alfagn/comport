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

    /**
     * Dashboard textareas carry the structured case-study blocks as
     * delimited lines; convert them to arrays before JSON storage.
     */
    private function normalizeCaseStudy(array $attributes): array
    {
        foreach (['facts', 'stats', 'sections'] as $field) {
            if (isset($attributes[$field]) && is_string($attributes[$field])) {
                $attributes[$field] = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $attributes[$field]))));
            }
        }
        if (isset($attributes['facts']) && is_array($attributes['facts'])) {
            $attributes['facts'] = array_values(array_map(function (string $line) {
                $p = array_map('trim', explode('|', $line, 2));

                return ['label' => $p[0], 'value' => $p[1] ?? ''];
            }, $attributes['facts']));
        }
        if (isset($attributes['stats']) && is_array($attributes['stats'])) {
            $attributes['stats'] = array_values(array_map(function (string $line) {
                $p = array_map('trim', explode('|', $line, 4));

                return [
                    'target' => $p[0] ?? '0',
                    'decimals' => (int) ($p[1] ?? 0),
                    'suffix' => $p[2] ?? '',
                    'label' => $p[3] ?? '',
                ];
            }, $attributes['stats']));
        }
        if (isset($attributes['sections']) && is_array($attributes['sections'])) {
            $attributes['sections'] = array_values(array_map(function (string $line) {
                $p = array_map('trim', explode('||', $line));

                return ['eyebrow' => $p[0] ?? '', 'heading' => $p[1] ?? '', 'items' => array_values(array_filter(array_slice($p, 2)))];
            }, $attributes['sections']));
        }

        return $attributes;
    }

    public function create(ProjectData $data, ?UploadedFile $imageFile = null, array $galleryFiles = [], ?UploadedFile $logoFile = null): Project
    {
        $attributes = $this->normalizeCaseStudy($this->normalizeJsonLines($data->toArray()));
        $attributes['slug'] = $attributes['slug'] ?? Str::slug($attributes['title']);
        $attributes['image'] = $this->storeUpload($imageFile, 'uploads/projects');
        $attributes['logo'] = $this->storeUpload($logoFile, 'uploads/projects/logos', is_string($attributes['logo'] ?? null) ? $attributes['logo'] : null);

        $gallery = [];
        if (isset($attributes['gallery'])) {
            if (is_string($attributes['gallery'])) {
                $gallery = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $attributes['gallery']))));
            } elseif (is_array($attributes['gallery'])) {
                $gallery = $attributes['gallery'];
            }
        }
        foreach ($galleryFiles as $gFile) {
            if ($gFile instanceof UploadedFile) {
                $path = $this->storeUpload($gFile, 'uploads/projects/gallery');
                if ($path) {
                    $gallery[] = $path;
                }
            }
        }
        $attributes['gallery'] = !empty($gallery) ? array_values(array_unique($gallery)) : null;

        /** @var Project $project */
        $project = $this->repository->create($attributes);
        $this->syncTags($project, $data->tags);

        return $project;
    }

    /** Update a project and re-sync its tags. */
    public function update(int $id, ProjectData $data, ?UploadedFile $imageFile = null, array $galleryFiles = [], ?UploadedFile $logoFile = null): Project
    {
        $record = $this->repository->find($id);
        $attributes = $this->normalizeCaseStudy($this->normalizeJsonLines($data->toArray()));
        $attributes['slug'] = $attributes['slug'] ?? $record->slug;
        $attributes['image'] = $this->storeUpload($imageFile, 'uploads/projects', $record->image);
        $attributes['logo'] = $this->storeUpload($logoFile, 'uploads/projects/logos', $record->logo);

        $gallery = [];
        if (isset($attributes['gallery'])) {
            if (is_string($attributes['gallery'])) {
                $gallery = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $attributes['gallery']))));
            } elseif (is_array($attributes['gallery'])) {
                $gallery = $attributes['gallery'];
            }
        } elseif ($record->gallery) {
            $gallery = (array) $record->gallery;
        }

        foreach ($galleryFiles as $gFile) {
            if ($gFile instanceof UploadedFile) {
                $path = $this->storeUpload($gFile, 'uploads/projects/gallery');
                if ($path) {
                    $gallery[] = $path;
                }
            }
        }
        $attributes['gallery'] = !empty($gallery) ? array_values(array_unique($gallery)) : null;

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

    /** @inheritDoc */
    protected function attributeDefaults(): array
    {
        return ['is_active' => true, 'is_featured' => false, 'sort_order' => 0];
    }
}
