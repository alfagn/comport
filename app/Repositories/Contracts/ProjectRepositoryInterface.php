<?php

namespace App\Repositories\Contracts;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Persistence contract for the Project entity.
 */
interface ProjectRepositoryInterface extends RepositoryInterface
{
    /** Active projects pinned to the home page, with their tags. */
    public function featured(): Collection;

    /** All active projects for the work page, with their tags. */
    public function activeWithTags(): Collection;

    /** An active project by slug for the public case-study page. */
    public function findActiveBySlug(string $slug): \App\Models\Project;

    /** The next live project after the given one (sort order, wrapping to the first). */
    public function nextAfter(\App\Models\Project $project): ?\App\Models\Project;
}
