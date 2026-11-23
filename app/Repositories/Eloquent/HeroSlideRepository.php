<?php

namespace App\Repositories\Eloquent;

use App\Core\Repositories\EloquentRepository;
use App\Models\HeroSlide;
use App\Repositories\Contracts\HeroSlideRepositoryInterface;

/**
 * Eloquent-backed repository for HeroSlide records.
 */
class HeroSlideRepository extends EloquentRepository implements HeroSlideRepositoryInterface
{
    protected string $model = HeroSlide::class;
}
