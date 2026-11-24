<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\IndustryRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class IndustryDataTable extends AdminDataTable
{
    public function __construct(DataTables $datatables, HtmlBuilder $html, private IndustryRepositoryInterface $repository)
    {
        parent::__construct($datatables, $html);
    }

    protected function query(): Builder
    {
        return $this->repository->listingQuery();
    }

    protected function routePrefix(): string
    {
        return 'admin.industries';
    }

    protected function decorate(EloquentDataTable $table): void
    {
        $table
            ->editColumn('title', fn ($row) => '<div class="cell-title">'.e($row->title).'</div><div class="cell-sub">'.e($row->subtitle ?? '').'</div>')
            ->editColumn('sort_order', fn ($row) => $this->orderBadge($row->sort_order))
            ->editColumn('is_active', fn ($row) => $this->statusToggle('industries', $row->id, (bool) $row->is_active, 'Live', 'Hidden'))
            ->addColumn('actions', fn ($row) => $this->rowActions($row->id, 'industry'))
            ->rawColumns(['title', 'sort_order', 'is_active', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('title')->title('Industry'),
            Column::make('sort_order')->title('Order')->searchable(false)->width(100),
            Column::make('is_active')->title('Status')->searchable(false)->width(140),
            $this->actionsColumn(),
        ];
    }

    protected function defaultOrder(): array
    {
        return [1, 'asc'];
    }
}
