<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

/**
 * Server-side Yajra table for job applications: newest first, with a
 * "New" pill on unreviewed rows. Actions are Open (review view), CV
 * (authenticated private download) and Delete. Dynamic values are
 * escaped with e(); plain columns rely on Yajra's default escaping.
 */
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
            ->editColumn('created_at', fn ($row) => $row->created_at->diffForHumans())
            ->addColumn('actions', function ($row) {
                $open = '<a class="btn btn--ghost btn--sm" href="'.e(route('admin.applications.show', $row->id)).'">Open</a>';
                $cv = '<a class="btn btn--ghost btn--sm" href="'.e(route('admin.applications.cv', $row->id)).'">CV</a>';

                return $open.$cv.'<form method="post" action="'.e(route('admin.applications.destroy', $row->id)).'" onsubmit="return confirm(\'Delete this application and its CV?\')" style="display:inline">'
                    .csrf_field().method_field('DELETE')
                    .'<button class="btn btn--danger btn--sm" type="submit">Delete</button></form>';
            })
            ->rawColumns(['full_name', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('full_name')->title('Applicant'),
            Column::computed('job')->title('Opening'),
            Column::make('phone')->title('Phone'),
            Column::make('experience_years')->title('Experience'),
            Column::make('created_at')->title('Received')->searchable(false),
            Column::computed('actions')->title('')->orderable(false)->searchable(false)->width(230)->addClass('row-actions'),
        ];
    }

    /** Newest applications first. */
    protected function defaultOrder(): array
    {
        return [4, 'desc'];
    }
}
