<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\PartnerRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class PartnerDataTable extends AdminDataTable
{
    public function __construct(DataTables $datatables, HtmlBuilder $html, private PartnerRepositoryInterface $repository)
    {
        parent::__construct($datatables, $html);
    }

    protected function query(): Builder
    {
        return $this->repository->listingQuery();
    }

    protected function routePrefix(): string
    {
        return 'admin.partners';
    }

    protected function decorate(EloquentDataTable $table): void
    {
        $table
            ->editColumn('name', function ($row) {
                $logo = '';
                if ($row->logo) {
                    $src = str_starts_with((string) $row->logo, 'uploads/') ? asset('storage/'.$row->logo) : asset($row->logo);
                    $logo = '<img class="thumb thumb--logo" src="'.e($src).'" alt=""/>';
                }

                return '<div class="dt-media-cell">'.$logo.'<div>'
                    .'<div class="cell-title">'.e($row->name).'</div>'
                    .'<div class="cell-sub">'.e($row->url ?? '').'</div></div></div>';
            })
            ->editColumn('tier', fn ($row) => $row->tier ? '<span class="pill">'.e($row->tier).'</span>' : '<span class="cell-sub">—</span>')
            ->editColumn('sort_order', fn ($row) => $this->orderBadge($row->sort_order))
            ->editColumn('is_active', fn ($row) => $this->statusToggle('partners', $row->id, (bool) $row->is_active, 'Live', 'Hidden'))
            ->addColumn('actions', fn ($row) => $this->rowActions($row->id, 'partner'))
            ->rawColumns(['name', 'tier', 'sort_order', 'is_active', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('name')->title('Partner'),
            Column::make('tier')->title('Tier'),
            Column::make('sort_order')->title('Order')->searchable(false)->width(90),
            Column::make('is_active')->title('Status')->searchable(false)->width(130),
            $this->actionsColumn(),
        ];
    }

    protected function defaultOrder(): array
    {
        return [2, 'asc'];
    }
}
