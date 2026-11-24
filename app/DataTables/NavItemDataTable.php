<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\NavItemRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

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
        $table
            ->editColumn('label', fn ($row) => '<div class="cell-title">'.e($row->label).'</div><div class="cell-sub">'.e($row->sub ?? '').'</div>')
            ->editColumn('menu', function ($row) {
                $names = [
                    'utility' => 'Utility bar',
                    'drawer' => 'Mobile drawer',
                    'mega-capabilities' => 'Mega · Capabilities',
                    'mega-journal' => 'Mega · Journal',
                ];
                $pill = '<span class="pill">'.e($names[$row->menu] ?? $row->menu).'</span>';
                if ($row->parent) {
                    $pill .= ' <span class="pill pill--accent">in '.e($row->parent->label).'</span>';
                }

                return '<div class="dt-pills-wrap">'.$pill.'</div>';
            })
            ->editColumn('url', fn ($row) => $row->url ? '<code class="dt-code">'.e($row->url).'</code>' : '<span class="cell-sub">—</span>')
            ->editColumn('sort_order', fn ($row) => $this->orderBadge($row->sort_order))
            ->editColumn('is_active', fn ($row) => $this->statusToggle('navigation', $row->id, (bool) $row->is_active, 'Live', 'Hidden'))
            ->addColumn('actions', fn ($row) => $this->rowActions($row->id, 'navigation link'))
            ->rawColumns(['label', 'menu', 'url', 'sort_order', 'is_active', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('label')->title('Label'),
            Column::make('menu')->title('Menu'),
            Column::make('url')->title('URL'),
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
