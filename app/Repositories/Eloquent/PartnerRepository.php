<?php

namespace App\Repositories\Eloquent;

use App\Core\Repositories\EloquentRepository;
use App\Models\Partner;
use App\Repositories\Contracts\PartnerRepositoryInterface;

/**
 * Eloquent-backed repository for Partner records.
 */
class PartnerRepository extends EloquentRepository implements PartnerRepositoryInterface
{
    protected string $model = Partner::class;
}
