<?php

namespace App\Repositories\Eloquent;

use App\Core\Repositories\EloquentRepository;
use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;

/**
 * Eloquent-backed repository for Service records.
 */
class ServiceRepository extends EloquentRepository implements ServiceRepositoryInterface
{
    protected string $model = Service::class;
}
