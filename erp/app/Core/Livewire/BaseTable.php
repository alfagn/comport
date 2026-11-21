<?php

declare(strict_types=1);

namespace App\Core\Livewire;

use Livewire\WithPagination;

/**
 * Class BaseTable
 *
 * Base table component providing reactive search, multi-column sorting,
 * dynamic pagination, per-page selector, and bulk selection.
 */
abstract class BaseTable extends BaseComponent
{
    use WithPagination;

    /**
     * Search term with automatic live debouncing.
     */
    public string $search = '';

    /**
     * Active sort column.
     */
    public string $sortField = 'id';

    /**
     * Sort direction.
     */
    public string $sortDirection = 'desc';

    /**
     * Number of items per page.
     */
    public int $perPage = 15;

    /**
     * Selected items for bulk operations.
     *
     * @var array<int|string, bool>
     */
    public array $selectedRows = [];

    /**
     * Select all checkbox toggle on current page.
     */
    public bool $selectAll = false;

    /**
     * Query string persistence.
     *
     * @var array<string, mixed>
     */
    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 15],
    ];

    /**
     * Reset pagination when search input changes.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when perPage changes.
     */
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Toggle or update column sorting.
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Reset all active filters.
     */
    public function resetFilters(): void
    {
        $this->reset(['search', 'selectedRows', 'selectAll']);
        $this->resetPage();
    }
}
