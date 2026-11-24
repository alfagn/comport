<?php

namespace App\Core\DataTables;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

/**
 * Shared spine for every dashboard listing table.
 */
abstract class AdminDataTable
{
    /** Whether bulk selection checkboxes and bulk actions are enabled for this table. */
    protected bool $enableBulkSelect = true;

    public function __construct(
        protected DataTables $datatables,
        protected HtmlBuilder $html,
    ) {}

    abstract protected function query(): Builder;

    /** @return array<int, Column> */
    abstract protected function columns(): array;

    abstract protected function routePrefix(): string;

    public function entityKey(): string
    {
        $prefix = str_replace('admin.', '', $this->routePrefix());
        if ($prefix === 'inbox.subscribers') {
            return 'subscribers';
        }

        return $prefix;
    }

    protected function selectColumn(): Column
    {
        return Column::computed('select_row')
            ->title('<div class="dt-checkbox-cell"><input type="checkbox" class="dt-select-all" aria-label="Select all rows" /></div>')
            ->titleAttr('Select all rows')
            ->orderable(false)
            ->searchable(false)
            ->exportable(false)
            ->printable(false)
            ->width(42)
            ->addClass('dt-col-select text-center');
    }

    public function getColumns(): array
    {
        $cols = $this->columns();
        if ($this->enableBulkSelect) {
            array_unshift($cols, $this->selectColumn());
        }

        return $cols;
    }

    public function handle(string $view, array $data = []): View|JsonResponse
    {
        if (request()->ajax()) {
            return $this->ajax();
        }

        return view($view, $data + ['datatable' => $this->tableBuilder()]);
    }

    public function ajax(): JsonResponse
    {
        $table = $this->datatables->eloquent($this->query());

        $this->decorate($table);

        if ($this->enableBulkSelect) {
            $table->addColumn('select_row', function ($row) {
                return '<div class="dt-checkbox-cell"><input type="checkbox" class="dt-row-select" value="'.$row->id.'" data-id="'.$row->id.'" aria-label="Select row '.$row->id.'" /></div>';
            });

            (function () {
                $raw = $this->columnDef['raw'] ?? [];
                if (! in_array('select_row', $raw, true)) {
                    $raw[] = 'select_row';
                }
                $this->columnDef['raw'] = $raw;
            })->call($table);
        }

        return $table->toJson();
    }

    protected function decorate(EloquentDataTable $table): void
    {
        //
    }

    protected function tableBuilder(): HtmlBuilder
    {
        $order = $this->defaultOrder();
        if ($this->enableBulkSelect && isset($order[0]) && is_int($order[0])) {
            $order[0] = $order[0] + 1;
        }

        $builder = $this->html
            ->columns($this->getColumns())
            ->ajax(route($this->ajaxRoute()))
            ->orderBy(...$order)
            ->parameters([
                'pageLength' => 15,
                'lengthMenu' => [15, 30, 50],
                'autoWidth' => false,
                'language' => [
                    'search' => '',
                    'searchPlaceholder' => 'Search records…',
                    'lengthMenu' => 'Show _MENU_ rows',
                    'emptyTable' => 'Nothing here yet.',
                    'zeroRecords' => 'No matching records found.',
                ],
            ]);

        if ($this->enableBulkSelect) {
            $builder->setTableAttribute('data-entity', $this->entityKey());
            $builder->setTableAttribute('data-bulk-url', route('admin.bulk.delete', $this->entityKey()));
        }

        return $builder;
    }

    protected function ajaxRoute(): string
    {
        return $this->routePrefix().'.index';
    }

    protected function defaultOrder(): array
    {
        return [0, 'asc'];
    }

    protected function actionsColumn(int $width = 176): Column
    {
        return Column::computed('actions')
            ->title('Actions')
            ->orderable(false)
            ->searchable(false)
            ->width($width)
            ->addClass('row-actions');
    }

    protected function rowActions(int $id, string $label = 'record'): string
    {
        return view('admin._partials.row-actions', [
            'edit' => route($this->routePrefix().'.edit', $id),
            'delete' => route($this->routePrefix().'.destroy', $id),
            'label' => $label,
        ])->render();
    }

    /** Render an interactive one-click status switch directly inside a table cell. */
    protected function statusToggle(string $entity, int $id, bool $active, string $labelOn = 'Live', string $labelOff = 'Hidden'): string
    {
        return view('admin._partials.status-toggle', [
            'entity' => $entity,
            'id' => $id,
            'active' => $active,
            'labelOn' => $labelOn,
            'labelOff' => $labelOff,
        ])->render();
    }

    /** Render a clean monospace order chip (#01, #02...). */
    protected function orderBadge(mixed $order): string
    {
        return '<span class="dt-order-badge">#'.str_pad((string) ((int) $order), 2, '0', STR_PAD_LEFT).'</span>';
    }
}
