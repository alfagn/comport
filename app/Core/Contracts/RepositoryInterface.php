<?php

namespace App\Core\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Base contract every repository in the app implements.
 *
 * Controllers and services never talk to Eloquent directly; they depend on
 * these contracts (Dependency Inversion). Swapping the persistence layer
 * means writing a new implementation and rebinding it in the container,
 * without touching business logic.
 */
interface RepositoryInterface
{
    /** Paginate records, newest first, for dashboard listings. */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /** Return every record (ordered by the entity's natural order). */
    public function all(): Collection;

    /** Find a record by primary key or fail with ModelNotFoundException. */
    public function find(int $id): Model;

    /** Find a published/visible record by its public slug. */
    public function findBySlug(string $slug): Model;

    /** Persist a new record from a validated attribute bag. */
    public function create(array $attributes): Model;

    /** Update an existing record by primary key. */
    public function update(int $id, array $attributes): Model;

    /** Delete a record by primary key. */
    public function delete(int $id): bool;

    /** Total number of records, used by dashboard stat cards. */
    public function count(): int;

    /**
     * Base Eloquent query for server-side dashboard listings (Yajra
     * DataTables). Returned unconstrained: the DataTable applies the
     * requested search/sort/pagination on top.
     */
    public function listingQuery(): \Illuminate\Database\Eloquent\Builder;

    /**
     * Records created per calendar day for the last $days days, as a
     * date => count map. Feeds the real activity sparklines on the
     * dashboard stat cards; empty days are simply absent from the map.
     */
    public function dailyCreatedCounts(int $days): array;

    /**
     * Records created inside a half-open window [$from, $to). Used to
     * compute honest week-over-week deltas on the dashboard.
     */
    public function countCreatedBetween(\DateTimeInterface $from, \DateTimeInterface $to): int;
}
