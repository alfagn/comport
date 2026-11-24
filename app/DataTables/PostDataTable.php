<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

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
        $table
            ->editColumn('title', function ($row) {
                $img = '';
                if ($row->cover_image) {
                    $src = str_starts_with((string) $row->cover_image, 'uploads/') ? asset('storage/'.$row->cover_image) : asset($row->cover_image);
                    $img = '<img class="thumb" src="'.e($src).'" alt=""/>';
                }

                return '<div class="dt-media-cell">'.$img.'<div>'
                    .'<div class="cell-title">'.e($row->title).'</div>'
                    .'<div class="cell-sub">'.e($row->category ?? '').'</div></div></div>';
            })
            ->editColumn('type', fn ($row) => '<span class="pill">'.e(ucfirst((string) $row->type)).'</span>')
            ->editColumn('published_at', fn ($row) => $row->published_at?->format('M j, Y') ?? '—')
            ->editColumn('is_published', fn ($row) => $this->statusToggle('posts', $row->id, (bool) $row->is_published, 'Published', 'Draft'))
            ->addColumn('actions', fn ($row) => $this->rowActions($row->id, 'post'))
            ->rawColumns(['title', 'type', 'is_published', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('title')->title('Post'),
            Column::make('type')->title('Format')->width(115),
            Column::make('published_at')->title('Published')->searchable(false)->width(125),
            Column::make('is_published')->title('Status')->searchable(false)->width(140),
            $this->actionsColumn(),
        ];
    }

    protected function defaultOrder(): array
    {
        return [2, 'desc'];
    }
}
