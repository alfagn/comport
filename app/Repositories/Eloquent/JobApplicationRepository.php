<?php

namespace App\Repositories\Eloquent;

use App\Core\Repositories\EloquentRepository;
use App\Models\JobApplication;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;

/**
 * Eloquent-backed repository for job applications.
 */
class JobApplicationRepository extends EloquentRepository implements JobApplicationRepositoryInterface
{
    protected string $model = JobApplication::class;

    /** Applications still awaiting review, shown as the sidebar badge. */
    public function newCount(): int
    {
        return $this->query()->new()->count();
    }

    /** Newest first, with the opening eager-loaded for the listing. */
    public function listingQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return JobApplication::query()->with('jobPost:id,title,ref_code')->latest();
    }
}
