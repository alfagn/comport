<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class JobApplicationDataTable extends AdminDataTable
{
    public function __construct(DataTables $datatables, HtmlBuilder $html, private JobApplicationRepositoryInterface $repository)
    {
        parent::__construct($datatables, $html);
    }

    protected function query(): Builder
    {
        return $this->repository->listingQuery();
    }

    protected function routePrefix(): string
    {
        return 'admin.applications';
    }

    protected function decorate(EloquentDataTable $table): void
    {
        $table
            ->editColumn('full_name', function ($row) {
                $pill = $row->status === 'new' ? ' <span class="pill pill--accent">New</span>' : '';

                return '<div class="cell-title">'.e($row->full_name).$pill.'</div>'
                    .'<div class="cell-sub">'.e($row->email).'</div>';
            })
            ->addColumn('job', fn ($row) => $row->jobPost?->title ?? '—')
            ->editColumn('status', fn ($row) => $this->statusToggle('applications', $row->id, $row->status !== 'new', 'Reviewed', 'New'))
            ->editColumn('created_at', fn ($row) => $row->created_at->diffForHumans())
            ->addColumn('actions', function ($row) {
                $open = '<a class="btn btn--ghost btn--sm dt-btn" href="'.e(route('admin.applications.show', $row->id)).'">Open</a>';
                $cv = '<a class="btn btn--ghost btn--sm dt-btn" href="'.e(route('admin.applications.cv', $row->id)).'">CV</a>';

                return '<div class="dt-actions">'.$open.$cv
                    .'<form method="post" action="'.e(route('admin.applications.destroy', $row->id)).'" data-confirm="Delete this application and its CV?" data-confirm-title="Delete Application" class="dt-inline-form">'
                    .csrf_field().method_field('DELETE')
                    .'<button class="btn btn--danger btn--sm dt-btn" type="submit">Delete</button></form></div>';
            })
            ->rawColumns(['full_name', 'status', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('full_name')->title('Applicant'),
            Column::computed('job')->title('Opening'),
            Column::make('phone')->title('Phone'),
            Column::make('experience_years')->title('Experience'),
            Column::make('created_at')->title('Received')->searchable(false),
            Column::make('status')->title('Status')->searchable(false)->width(140),
            $this->actionsColumn(225),
        ];
    }

    protected function defaultOrder(): array
    {
        return [4, 'desc'];
    }
}
