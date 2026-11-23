<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\MemberRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

/**
 * Server-side Yajra table for the Team & Founders listing. Dynamic values are escaped
 * with e(); only static pill/cell markup is rendered raw.
 */
class MemberDataTable extends AdminDataTable
{
    public function __construct(DataTables $datatables, HtmlBuilder $html, private MemberRepositoryInterface $repository)
    {
        parent::__construct($datatables, $html);
    }

    protected function query(): Builder
    {
        return $this->repository->listingQuery();
    }

    protected function routePrefix(): string
    {
        return 'admin.members';
    }

    protected function decorate(EloquentDataTable $table): void
    {
        $table->editColumn('name', fn ($row) => '<div class="cell-title">'.e($row->name).'</div><div class="cell-sub">'.e($row->role ?? '').'</div>')
            ->editColumn('is_active', fn ($row) => $row->is_active
            ? '<span class="pill pill--on">Live</span>' : '<span class="pill pill--off">Hidden</span>')
            ->addColumn('actions', fn ($row) => $this->rowActions($row->id, 'record'))
            ->rawColumns(['name', 'is_active', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('name')->title('Person'),
            Column::make('kind')->title('Kind'),
            Column::make('sort_order')->title('Order')->searchable(false),
            Column::make('is_active')->title('Status')->searchable(false),
            $this->actionsColumn(),
        ];
    }

    protected function defaultOrder(): array
    {
        return [2, 'asc'];
    }

}
