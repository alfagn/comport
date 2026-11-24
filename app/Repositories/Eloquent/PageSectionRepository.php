<?php

namespace App\Repositories\Eloquent;

use App\Core\Repositories\EloquentRepository;
use App\Models\PageSection;
use App\Repositories\Contracts\PageSectionRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Eloquent-backed repository for editable page sections.
 */
class PageSectionRepository extends EloquentRepository implements PageSectionRepositoryInterface
{
    protected string $model = PageSection::class;

    /** {@inheritdoc} */
    public function forPage(string $page): Collection
    {
        return $this->query()
            ->where('page', $page)
            ->where('type', '!=', 'internal')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /** {@inheritdoc} */
    public function groupedByPage(): Collection
    {
        return $this->query()
            ->where('type', '!=', 'internal')
            ->orderBy('page')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('page');
    }
}
