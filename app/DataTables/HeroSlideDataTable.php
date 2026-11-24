<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\HeroSlideRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class HeroSlideDataTable extends AdminDataTable
{
    public function __construct(DataTables $datatables, HtmlBuilder $html, private HeroSlideRepositoryInterface $repository)
    {
        parent::__construct($datatables, $html);
    }

    protected function query(): Builder
    {
        return $this->repository->listingQuery();
    }

    protected function routePrefix(): string
    {
        return 'admin.hero-slides';
    }

    protected function decorate(EloquentDataTable $table): void
    {
        $table
            ->editColumn('title', function ($row) {
                $img = '';
                if ($row->image) {
                    $src = str_starts_with((string) $row->image, 'uploads/') ? asset('storage/'.$row->image) : asset($row->image);
                    $img = '<img class="thumb" src="'.e($src).'" alt=""/>';
                }

                return '<div class="dt-media-cell">'.$img.'<div>'
                    .'<div class="cell-title">'.e($row->title).'</div>'
                    .'<div class="cell-sub">'.e($row->tab_label ?? '').'</div></div></div>';
            })
            ->editColumn('tag', fn ($row) => $row->tag ? '<span class="pill pill--accent">'.e($row->tag).'</span>' : '<span class="cell-sub">—</span>')
            ->editColumn('sort_order', fn ($row) => $this->orderBadge($row->sort_order))
            ->editColumn('is_active', fn ($row) => $this->statusToggle('hero-slides', $row->id, (bool) $row->is_active, 'Live', 'Hidden'))
            ->addColumn('actions', fn ($row) => $this->rowActions($row->id, 'slide'))
            ->rawColumns(['title', 'tag', 'sort_order', 'is_active', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('title')->title('Slide'),
            Column::make('tag')->title('Tag'),
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
