<?php

namespace App\Repositories\Eloquent;

use App\Core\Repositories\EloquentRepository;
use App\Models\NavItem;
use App\Repositories\Contracts\NavItemRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Eloquent-backed repository for navigation links.
 */
class NavItemRepository extends EloquentRepository implements NavItemRepositoryInterface
{
    protected string $model = NavItem::class;

    /** @inheritDoc */
    public function activeForMenu(string $menu): Collection
    {
        return $this->query()
            ->where('menu', $menu)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
