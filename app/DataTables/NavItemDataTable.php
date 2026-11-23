<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\NavItemRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

/**
 * Server-side Yajra table for the Navigation listing. Dynamic values are escaped
 * with e(); only static pill/cell markup is rendered raw.
 */
class NavItemDataTable extends AdminDataTable
{
    public function __construct(DataTables $datatables, HtmlBuilder $html, private NavItemRepositoryInterface $repository)
    {
        parent::__construct($datatables, $html);
    }

    protected function query(): Builder
    {
        return $this->repository->listingQuery()->with('parent');
    }

    protected function routePrefix(): string
    {
        return 'admin.navigation';
    }

    protected function decorate(EloquentDataTable $table): void
    {
        $table->editColumn('label', fn ($row) => '<div class="cell-title">'.e($row->label).'</div><div class="cell-sub">'.e($row->sub ?? '').'</div>')
            ->editColumn('menu', function ($row) {
                // Friendly menu names; a child link also shows its column.
                $names = ['utility' => 'Utility bar', 'drawer' => 'Mobile drawer', 'mega-capabilities' => 'Mega · Capabilities', 'mega-journal' => 'Mega · Journal'];
                $pill = '<span class="pill">'.e($names[$row->menu] ?? $row->menu).'</span>';
                if ($row->parent) {
                    $pill .= ' <span class="pill">in '.e($row->parent->label).'</span>';
                }

                return $pill;
            })
            ->editColumn('is_active', fn ($row) => $row->is_active
            ? '<span class="pill pill--on">Live</span>' : '<span class="pill pill--off">Hidden</span>')
            ->addColumn('actions', fn ($row) => $this->rowActions($row->id, 'record'))
            ->rawColumns(['label', 'menu', 'is_active', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('label')->title('Label'),
            Column::make('menu')->title('Menu'),
            Column::make('url')->title('URL'),
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
