<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class ProjectDataTable extends AdminDataTable
{
    public function __construct(DataTables $datatables, HtmlBuilder $html, private ProjectRepositoryInterface $repository)
    {
        parent::__construct($datatables, $html);
    }

    protected function query(): Builder
    {
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

                return '<div class="dt-media-cell">'.$img.'<div>'
                    .'<div class="cell-title">'.e($row->title).'</div>'
                    .'<div class="cell-sub">'.e($row->kicker).'</div></div></div>';
            })
            ->addColumn('tags', function ($row) {
                if ($row->tags->isEmpty()) {
                    return '<span class="cell-sub">—</span>';
                }

                return '<div class="dt-pills-wrap">'.$row->tags->map(fn ($tag) => '<span class="pill pill--accent">'.e($tag->name).'</span>')->implode('').'</div>';
            })
            ->editColumn('is_featured', fn ($row) => $this->statusToggle('projects-featured', $row->id, (bool) $row->is_featured, 'Featured', 'Standard'))
            ->editColumn('is_active', fn ($row) => $this->statusToggle('projects', $row->id, (bool) $row->is_active, 'Live', 'Hidden'))
            ->addColumn('actions', fn ($row) => $this->rowActions($row->id, 'project'))
            ->rawColumns(['title', 'tags', 'is_featured', 'is_active', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('title')->title('Project'),
            Column::computed('tags')->title('Tags')->orderable(false)->searchable(false),
            Column::make('is_featured')->title('Featured')->searchable(false)->width(135),
            Column::make('is_active')->title('Status')->searchable(false)->width(130),
            $this->actionsColumn(),
        ];
    }
}
