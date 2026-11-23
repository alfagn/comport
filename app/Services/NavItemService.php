<?php

namespace App\Services;

use App\Core\Services\BaseService;
use App\DTOs\NavItemData;
use App\Repositories\Contracts\NavItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\NavItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Business logic for navigation links.
 *
 * Public menus are read through a short-lived cache so the header never
 * adds a query per page; every write flushes that cache here.
 */
class NavItemService extends BaseService
{
    public const CACHE_PREFIX = 'nav.menu.';

    public function __construct(NavItemRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /** Paginated list for the dashboard index page. */
    public function paginateForAdmin(int $perPage = 20): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    /** Load one record for the edit form. */
    public function findForEdit(int $id): Model
    {
        return $this->repository->find($id);
    }

    /**
     * Active links of one menu for the public header, cached for 5 minutes.
     *
     * Plain arrays are cached (never Eloquent models) so the cache payload
     * stays a safe, framework-version-independent serialization.
     */
    public function menu(string $menu): Collection
    {
        $links = Cache::remember(
            self::CACHE_PREFIX.$menu,
            300,
            fn () => $this->repository->activeForMenu($menu)
                ->map(fn (NavItem $item) => ['label' => $item->label, 'url' => $item->url])
                ->all()
        );

        return collect($links)->map(fn (array $link) => (object) $link);
    }

    /** Create a link from validated input and refresh the cached menus. */
    public function create(NavItemData $data): Model
    {
        $item = $this->repository->create($data->toArray());
        $this->flushMenuCache();

        return $item;
    }

    /** Update a link from validated input and refresh the cached menus. */
    public function update(int $id, NavItemData $data): Model
    {
        $item = $this->repository->update($id, $data->toArray());
        $this->flushMenuCache();

        return $item;
    }

    /** Delete a link and refresh the cached menus. */
    public function delete(int $id): bool
    {
        $deleted = $this->repository->delete($id);
        $this->flushMenuCache();

        return $deleted;
    }

    /** @inheritDoc */
    protected function attributeDefaults(): array
    {
        return ['is_active' => true, 'sort_order' => 0];
    }

    /** Drop every cached menu after a write. */
    private function flushMenuCache(): void
    {
        foreach (['utility', 'drawer'] as $menu) {
            Cache::forget(self::CACHE_PREFIX.$menu);
        }
    }
}
