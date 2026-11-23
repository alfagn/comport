<?php

namespace App\Core\DataTables;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

/**
 * Shared spine for every dashboard listing table.
 *
 * Concrete DataTables declare three things — the Eloquent query (through the
 * entity's repository), the column definitions, and the entity's route
 * prefix — and inherit the whole server-side flow:
 *
 *   browser GET          -> handle() renders the view with an HTML builder
 *   DataTables XHR (GET) -> handle() answers with Yajra's JSON payload
 *
 * Search, sort and pagination all run in SQL through Yajra's
 * EloquentDataTable, so large listings stay fast and the browser only ever
 * receives one page of rows.
 */
abstract class AdminDataTable
{
    public function __construct(
        protected DataTables $datatables,
        protected HtmlBuilder $html,
    ) {}

    /**
     * The listing query, sourced from the entity's repository so the
     * persistence layer stays behind its contract.
     */
    abstract protected function query(): Builder;

    /**
     * DataTables.net column definitions (Yajra Html\Column objects).
     *
     * @return array<int, Column>
     */
    abstract protected function columns(): array;

    /**
     * The entity's admin route prefix, e.g. "admin.projects". Used to build
     * the ajax URL and the per-row edit/delete links.
     */
    abstract protected function routePrefix(): string;

    /**
     * Entry point used by controllers: JSON for the DataTables XHR, the
     * Blade view otherwise.
     */
    public function handle(string $view, array $data = []): View|JsonResponse
    {
        if (request()->ajax()) {
            return $this->ajax();
        }

        return view($view, $data + ['datatable' => $this->tableBuilder()]);
    }

    /**
     * Yajra's server-side JSON answer: paging, global search and column
     * sorting are handled by EloquentDataTable; decorate() adds the
     * entity-specific computed columns (thumbnails, pills, actions).
     */
    public function ajax(): JsonResponse
    {
        $table = $this->datatables->eloquent($this->query());

        $this->decorate($table);

        return $table->toJson();
    }

    /**
     * Entity hook for addColumn/editColumn/rawColumns calls.
     */
    protected function decorate(EloquentDataTable $table): void
    {
        //
    }

    /**
     * The HTML builder: emits the <table> markup and the initialisation
     * script. Ajax points back at the same index route.
     */
    protected function tableBuilder(): HtmlBuilder
    {
        return $this->html
            ->columns($this->columns())
            ->ajax(route($this->ajaxRoute()))
            ->orderBy(...$this->defaultOrder())
            ->parameters([
                'pageLength' => 15,
                'lengthMenu' => [15, 30, 50],
                'autoWidth' => false,
                'language' => [
                    'search' => '',
                    'searchPlaceholder' => 'Filter…',
                    'emptyTable' => 'Nothing here yet.',
                    'zeroRecords' => 'No rows match that filter.',
                ],
            ]);
    }

    /**
     * Route name the DataTables XHR hits. Usually the resource index;
     * listings living on a custom route (e.g. subscribers) override it.
     */
    protected function ajaxRoute(): string
    {
        return $this->routePrefix() . '.index';
    }

    /**
     * Default sort applied when the visitor hasn't clicked a header:
     * [column index, direction]. Content tables override this to their
     * natural display order.
     */
    protected function defaultOrder(): array
    {
        return [0, 'asc'];
    }

    /**
     * The standard trailing actions column: Edit + Delete per row.
     */
    protected function actionsColumn(): Column
    {
        return Column::computed('actions')
            ->title('')
            ->orderable(false)
            ->searchable(false)
            ->width(150)
            ->addClass('row-actions');
    }

    /**
     * Render the Edit/Delete controls for one row through the shared partial.
     */
    protected function rowActions(int $id, string $label = 'record'): string
    {
        return view('admin._partials.row-actions', [
            'edit' => route($this->routePrefix() . '.edit', $id),
            'delete' => route($this->routePrefix() . '.destroy', $id),
            'label' => $label,
        ])->render();
    }
}
