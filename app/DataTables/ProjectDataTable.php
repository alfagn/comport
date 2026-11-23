<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

/**
 * Server-side table for the Projects listing: thumbnail + title cell,
 * tag pills, featured flag, status pill and row actions. All dynamic
 * values are escaped with e(); only static markup is rendered raw.
 */
class ProjectDataTable extends AdminDataTable
{
    public function __construct(DataTables $datatables, HtmlBuilder $html, private ProjectRepositoryInterface $repository)
    {
        parent::__construct($datatables, $html);
    }

    protected function query(): Builder
    {
        // Tags eager-loaded so the pills column stays one query.
        return $this->repository->listingQuery()->with('tags');
    }

    protected function routePrefix(): string
    {
        return 'admin.projects';
    }

    protected function decorate(EloquentDataTable $table): void
    {
        $table
            ->editColumn('title', function ($row) {
                $img = '';
                if ($row->image) {
                    $src = str_starts_with($row->image, 'uploads/') ? asset('storage/'.$row->image) : asset($row->image);
                    $img = '<img class="thumb" src="'.e($src).'" alt=""/>';
                }

                return '<div style="display:flex;align-items:center;gap:14px">'.$img.'<div>'
                    .'<div class="cell-title">'.e($row->title).'</div>'
                    .'<div class="cell-sub">'.e($row->kicker).'</div></div></div>';
            })
            ->addColumn('tags', function ($row) {
                return $row->tags->map(fn ($tag) => '<span class="pill pill--accent" style="margin:1px">'.e($tag->name).'</span>')->implode(' ');
            })
            ->editColumn('is_featured', fn ($row) => $row->is_featured ? '<span class="pill pill--warn">Featured</span>' : '—')
            ->editColumn('is_active', fn ($row) => $row->is_active
                ? '<span class="pill pill--on">Live</span>' : '<span class="pill pill--off">Hidden</span>')
            ->addColumn('actions', fn ($row) => $this->rowActions($row->id, 'project'))
            ->rawColumns(['title', 'tags', 'is_featured', 'is_active', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('title')->title('Project'),
            Column::computed('tags')->title('Tags')->orderable(false)->searchable(false),
            Column::make('is_featured')->title('Featured')->searchable(false),
            Column::make('is_active')->title('Status')->searchable(false),
            $this->actionsColumn(),
        ];
    }
}
