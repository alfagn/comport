<?php

namespace App\Repositories\Eloquent;

use App\Core\Repositories\EloquentRepository;
use App\Models\ContentListItem;
use App\Repositories\Contracts\ContentListItemRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Eloquent-backed repository for content list cards.
 */
class ContentListItemRepository extends EloquentRepository implements ContentListItemRepositoryInterface
{
    protected string $model = ContentListItem::class;

    /** @inheritDoc */
    public function activeForGroup(string $group): Collection
    {
        return $this->query()
            ->where('group', $group)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
