<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\ContentListItemRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

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
        $table
            ->editColumn('title', fn ($row) => '<div class="cell-title">'.e($row->title).'</div>'.($row->meta ? '<div class="cell-sub">'.e($row->meta).'</div>' : ''))
            ->editColumn('group', fn ($row) => '<span class="pill">'.e(\App\Services\ContentListItemService::GROUPS[$row->group] ?? $row->group).'</span>')
            ->editColumn('sort_order', fn ($row) => $this->orderBadge($row->sort_order))
            ->editColumn('is_active', fn ($row) => $this->statusToggle('content-lists', $row->id, (bool) $row->is_active, 'Live', 'Hidden'))
            ->addColumn('actions', fn ($row) => $this->rowActions($row->id, 'list item'))
            ->rawColumns(['title', 'group', 'sort_order', 'is_active', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('title')->title('Card Title'),
            Column::make('group')->title('Section List'),
            Column::make('sort_order')->title('Order')->searchable(false)->width(90),
            Column::make('is_active')->title('Status')->searchable(false)->width(130),
            $this->actionsColumn(),
        ];
    }

    protected function defaultOrder(): array
    {
        return [1, 'asc'];
    }
}
