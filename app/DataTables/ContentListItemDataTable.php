<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\ContentListItemRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

/**
 * Server-side Yajra table for the Content Lists listing. Dynamic values are escaped
 * with e(); only static pill/cell markup is rendered raw.
 */
class ContentListItemDataTable extends AdminDataTable
{
    public function __construct(DataTables $datatables, HtmlBuilder $html, private ContentListItemRepositoryInterface $repository)
    {
        parent::__construct($datatables, $html);
    }

    protected function query(): Builder
    {
        return $this->repository->listingQuery();
    }

    protected function routePrefix(): string
    {
        return 'admin.content-lists';
    }

    protected function decorate(EloquentDataTable $table): void
    {
        $table->editColumn('title', fn ($row) => '<div class="cell-title">'.e($row->title).'</div>')
            ->editColumn('group', fn ($row) => '<span class="pill">'.e(\App\Services\ContentListItemService::GROUPS[$row->group] ?? $row->group).'</span>')
            ->editColumn('is_active', fn ($row) => $row->is_active
                ? '<span class="pill pill--on">Live</span>' : '<span class="pill pill--off">Hidden</span>')
            ->addColumn('actions', fn ($row) => $this->rowActions($row->id, 'item'))
            ->rawColumns(['title', 'group', 'is_active', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('title')->title('Title'),
            Column::make('group')->title('List'),
            Column::make('sort_order')->title('Order')->searchable(false),
            Column::make('is_active')->title('Status')->searchable(false),
            $this->actionsColumn(),
        ];
    }

    protected function defaultOrder(): array
    {
        return [1, 'asc'];
    }

}
