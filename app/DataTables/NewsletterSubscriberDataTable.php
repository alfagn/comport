<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\NewsletterSubscriberRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

/**
 * Server-side Yajra table for newsletter subscribers. Read-only listing:
 * no edit or delete actions exist for subscribers, so there is no
 * actions column at all.
 */
class NewsletterSubscriberDataTable extends AdminDataTable
{
    public function __construct(DataTables $datatables, HtmlBuilder $html, private NewsletterSubscriberRepositoryInterface $repository)
    {
        parent::__construct($datatables, $html);
    }

    protected function query(): Builder
    {
        return $this->repository->listingQuery();
    }

    protected function routePrefix(): string
    {
        return 'admin.inbox.subscribers';
    }

    protected function decorate(EloquentDataTable $table): void
    {
        $table->editColumn('created_at', fn ($row) => e($row->created_at->format('M j, Y')));
    }

    protected function columns(): array
    {
        return [
            Column::make('email')->title('Email'),
            Column::make('created_at')->title('Subscribed')->searchable(false),
        ];
    }

    /** Newest subscribers first. */
    protected function defaultOrder(): array
    {
        return [1, 'desc'];
    }
}
