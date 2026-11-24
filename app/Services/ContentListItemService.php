<?php

namespace App\Services;

use App\Core\Services\BaseService;
use App\DTOs\ContentListItemData;
use App\Models\ContentListItem;
use App\Repositories\Contracts\ContentListItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Business logic for repeating content lists.
 *
 * Public lists are read through a short-lived cache of plain arrays so the
 * pages never add a query per list; every write flushes that cache here.
 */
class ContentListItemService extends BaseService
{
    public const CACHE_PREFIX = 'content-list.';

    /** The lists the dashboard manages, with friendly labels. */
    public const GROUPS = [
        'about.pillars' => 'About — Core pillars',
        'about.principles' => 'About — Principles',
        'about.directory' => 'About — Leadership directory',
        'about.timeline' => 'About — Studio timeline',
        'team.culture' => 'Team — Culture cards',
        'careers.tracks' => 'Careers — Practice area tracks',
        'careers.benefits' => 'Careers — Benefits',
        'contact.channels' => 'Contact — Practice channels',
        'contact.promise' => 'Contact — Process promise',
        'contact.faq' => 'Contact — FAQ',
        'careers.process' => 'Careers — Hiring steps',
    ];

    public function __construct(ContentListItemRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /** Paginated list for the dashboard index page. */
    public function paginateForAdmin(int $perPage = 25): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    /** Load one record for the edit form. */
    public function findForEdit(int $id): Model
    {
        return $this->repository->find($id);
    }

    /** Active cards of one list for the public site, cached for 5 minutes. */
    public function list(string $group): Collection
    {
        $cards = Cache::remember(
            self::CACHE_PREFIX.$group,
            300,
            fn () => $this->repository->activeForGroup($group)
                ->map(fn (ContentListItem $item) => $item->only(['title', 'body', 'meta', 'badge', 'cta', 'url']))
                ->all()
        );

        return collect($cards)->map(fn (array $card) => (object) $card);
    }

    /** Create a card from validated input and refresh the cached lists. */
    public function create(ContentListItemData $data): Model
    {
        $item = $this->repository->create(array_replace($data->toArray(), ['sort_order' => $data->sort_order ?? 0, 'is_active' => $data->is_active ?? true]));
        $this->flushListCache();

        return $item;
    }

    /** Update a card from validated input and refresh the cached lists. */
    public function update(int $id, ContentListItemData $data): Model
    {
        $item = $this->repository->update($id, array_replace($data->toArray(), ['sort_order' => $data->sort_order ?? 0, 'is_active' => $data->is_active ?? true]));
        $this->flushListCache();

        return $item;
    }

    /** Delete a card and refresh the cached lists. */
    public function delete(int $id): bool
    {
        $deleted = $this->repository->delete($id);
        $this->flushListCache();

        return $deleted;
    }

    /** @inheritDoc */
    protected function attributeDefaults(): array
    {
        return ['is_active' => true, 'sort_order' => 0];
    }

    /** Drop every cached list after a write. */
    public function flushListCache(): void
    {
        foreach (array_keys(self::GROUPS) as $group) {
            Cache::forget(self::CACHE_PREFIX.$group);
        }
    }
}
