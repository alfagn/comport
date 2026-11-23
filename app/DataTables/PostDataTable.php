<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

/**
 * Server-side Yajra table for the Blog & Media listing. Dynamic values are escaped
 * with e(); only static pill/cell markup is rendered raw.
 */
class PostDataTable extends AdminDataTable
{
    public function __construct(DataTables $datatables, HtmlBuilder $html, private PostRepositoryInterface $repository)
    {
        parent::__construct($datatables, $html);
    }

    protected function query(): Builder
    {
        return $this->repository->listingQuery();
    }

    protected function routePrefix(): string
    {
        return 'admin.posts';
    }

    protected function decorate(EloquentDataTable $table): void
    {
        $table->editColumn('title', fn ($row) => '<div class="cell-title">'.e($row->title).'</div><div class="cell-sub">'.e($row->category ?? '').'</div>')
            ->editColumn('published_at', fn ($row) => $row->published_at?->format('M j, Y') ?? '—')
            ->editColumn('is_published', fn ($row) => $row->is_published
                ? '<span class="pill pill--on">Live</span>' : '<span class="pill pill--off">Draft</span>')
            ->addColumn('actions', fn ($row) => $this->rowActions($row->id, 'post'))
            ->rawColumns(['title', 'is_published', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('title')->title('Post'),
            Column::make('type')->title('Type'),
            Column::make('published_at')->title('Published')->searchable(false),
            Column::make('is_published')->title('Status')->searchable(false),
            $this->actionsColumn(),
        ];
    }

    protected function defaultOrder(): array
    {
        return [2, 'desc'];
    }

}
