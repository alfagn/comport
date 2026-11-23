<?php

namespace App\Repositories\Eloquent;

use App\Core\Repositories\EloquentRepository;
use App\Models\JobPost;
use App\Repositories\Contracts\JobPostRepositoryInterface;

/**
 * Eloquent-backed repository for JobPost records.
 */
class JobPostRepository extends EloquentRepository implements JobPostRepositoryInterface
{
    protected string $model = JobPost::class;
}
