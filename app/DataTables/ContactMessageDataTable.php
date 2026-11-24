<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class ContactMessageDataTable extends AdminDataTable
{
    public function __construct(DataTables $datatables, HtmlBuilder $html, private ContactMessageRepositoryInterface $repository)
    {
        parent::__construct($datatables, $html);
    }

    protected function query(): Builder
    {
        return $this->repository->listingQuery();
    }

    protected function routePrefix(): string
    {
        return 'admin.inbox';
    }

    protected function decorate(EloquentDataTable $table): void
    {
        $table
            ->editColumn('name', function ($row) {
                $pill = $row->read_at ? '' : ' <span class="pill pill--accent">New</span>';

                return '<div class="cell-title">'.e($row->name).$pill.'</div>'
                    .'<div class="cell-sub">'.e($row->email).'</div>';
            })
            ->editColumn('services', function ($row) {
                // Plain text: Yajra applies its default output escaping to this column.
                return implode(', ', $row->services ?? []) ?: '—';
            })
            ->editColumn('read_at', fn ($row) => $this->statusToggle('inbox', $row->id, $row->read_at !== null, 'Read', 'Unread'))
            ->editColumn('created_at', fn ($row) => $row->created_at->diffForHumans())
            ->addColumn('actions', function ($row) {
                $open = '<a class="btn btn--ghost btn--sm dt-btn" href="'.e(route('admin.inbox.show', $row->id)).'">Open</a>';

                return '<div class="dt-actions">'.$open
                    .'<form method="post" action="'.e(route('admin.inbox.destroy', $row->id)).'" data-confirm="Delete this brief?" data-confirm-title="Delete Brief" class="dt-inline-form">'
                    .csrf_field().method_field('DELETE')
                    .'<button class="btn btn--danger btn--sm dt-btn" type="submit">Delete</button></form></div>';
            })
            ->rawColumns(['name', 'read_at', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('name')->title('From'),
            Column::make('company')->title('Company'),
            Column::make('services')->title('Services')->orderable(false),
            Column::make('budget_tier')->title('Budget'),
            Column::make('created_at')->title('Received')->searchable(false),
            Column::make('read_at')->title('Status')->searchable(false)->width(130),
            $this->actionsColumn(165),
        ];
    }

    protected function defaultOrder(): array
    {
        return [4, 'desc'];
    }
}
