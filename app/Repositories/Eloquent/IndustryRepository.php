<?php

namespace App\Repositories\Eloquent;

use App\Core\Repositories\EloquentRepository;
use App\Models\Industry;
use App\Repositories\Contracts\IndustryRepositoryInterface;

/**
 * Eloquent-backed repository for Industry records.
 */
class IndustryRepository extends EloquentRepository implements IndustryRepositoryInterface
{
    protected string $model = Industry::class;
}
