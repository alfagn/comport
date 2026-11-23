<?php

namespace App\Repositories\Eloquent;

use App\Core\Repositories\EloquentRepository;
use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Eloquent-backed repository for Project records, including the
 * public-site finders (featured home-page set, full active list).
 */
class ProjectRepository extends EloquentRepository implements ProjectRepositoryInterface
{
    protected string $model = Project::class;

    /** Active projects pinned to the home page, with their tags. */
    public function featured(): Collection
    {
        return $this->query()->with('tags')->active()->featured()->get();
    }

    /** All active projects for the work page, with their tags. */
    public function activeWithTags(): Collection
    {
        return $this->query()->with('tags')->active()->get();
    }

    /** An active project by slug for the public case-study page. */
    public function nextAfter(Project $project): ?Project
    {
        return Project::query()
            ->where('is_active', true)
            ->where(fn ($q) => $q->where('sort_order', '>', $project->sort_order)
                ->orWhere(fn ($q2) => $q2->where('sort_order', $project->sort_order)->where('id', '>', $project->id)))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first()
            ?? Project::query()->where('is_active', true)->where('id', '!=', $project->id)->orderBy('sort_order')->orderBy('id')->first();
    }

    public function findActiveBySlug(string $slug): Project
    {
        return $this->query()->with('tags')->active()->where('slug', $slug)->firstOrFail();
    }
}
