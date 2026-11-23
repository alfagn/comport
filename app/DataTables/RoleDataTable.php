<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

/**
 * Server-side Yajra table for the Roles listing. Dynamic values are escaped
 * with e(); only static pill/cell markup is rendered raw.
 */
class RoleDataTable extends AdminDataTable
{
    protected function query(): Builder
    {
        return Role::query()->withCount(['users', 'permissions']);
    }

    protected function routePrefix(): string
    {
        return 'admin.roles';
    }

    protected function decorate(EloquentDataTable $table): void
    {
        $table->editColumn('name', fn ($row) => '<div class="cell-title">'.e($row->name).'</div>')
            ->addColumn('usage', fn ($row) => '<span class="pill pill--on">'.e($row->users_count).' admins</span> '
                .'<span class="pill">'.e($row->permissions_count).' permissions</span>')
            ->addColumn('actions', fn ($row) => $row->name === \App\Support\Permissions::OWNER_ROLE
                ? '<span class="cell-sub">Structural role</span>'
                : $this->rowActions($row->id, 'role'))
            ->rawColumns(['name', 'usage', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('name')->title('Role'),
            Column::computed('usage')->title('Usage')->searchable(false),
            $this->actionsColumn(),
        ];
    }
}
