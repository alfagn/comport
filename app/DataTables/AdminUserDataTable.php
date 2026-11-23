<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\AdminUserRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

/**
 * Server-side Yajra table for the Admins listing. Dynamic values are escaped
 * with e(); only static pill/cell markup is rendered raw.
 */
class AdminUserDataTable extends AdminDataTable
{
    public function __construct(DataTables $datatables, HtmlBuilder $html, private AdminUserRepositoryInterface $repository)
    {
        parent::__construct($datatables, $html);
    }

    protected function query(): Builder
    {
        return $this->repository->listingQuery();
    }

    protected function routePrefix(): string
    {
        return 'admin.admins';
    }

    protected function decorate(EloquentDataTable $table): void
    {
        $table->editColumn('name', fn ($row) => '<div class="cell-title">'.e($row->name).'</div><div class="cell-sub">'.e($row->email).'</div>')
            ->addColumn('roles', fn ($row) => $row->roles->isEmpty()
                ? '<span class="pill pill--off">No role</span>'
                : $row->roles->map(fn ($role) => '<span class="pill pill--on">'.e($role->name).'</span>')->implode(' '))
            ->editColumn('created_at', fn ($row) => e($row->created_at?->format('M j, Y')))
            ->addColumn('actions', fn ($row) => $this->rowActions($row->id, 'admin'))
            ->rawColumns(['name', 'roles', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('name')->title('Admin'),
            Column::computed('roles')->title('Roles'),
            Column::make('created_at')->title('Joined')->searchable(false),
            $this->actionsColumn(),
        ];
    }

    protected function defaultOrder(): array
    {
        return [0, 'asc'];
    }
}
