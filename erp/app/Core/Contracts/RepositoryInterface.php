<?php

declare(strict_types=1);

namespace App\Core\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface RepositoryInterface
 *
 * Base repository contract adhering to Clean Architecture and Repository Pattern.
 * Isolates data access logic and abstracts persistence layer from domain and application services.
 *
 * @template TModel of Model
 */
interface RepositoryInterface
{
    /**
     * Retrieve all records.
     *
     * @param  array<int, string>  $columns
     * @return Collection<int, TModel>
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Find a record by its primary key or return null.
     *
     * @param  array<int, string>  $columns
     * @return TModel|null
     */
    public function find(int|string $id, array $columns = ['*']): ?Model;

    /**
     * Find a record by its primary key or throw ModelNotFoundException.
     *
     * @param  array<int, string>  $columns
     * @return TModel
     */
    public function findOrFail(int|string $id, array $columns = ['*']): Model;

    /**
     * Find a single record matching specific criteria.
     *
     * @param  array<string, mixed>  $criteria
     * @param  array<int, string>  $columns
     * @return TModel|null
     */
    public function findOneBy(array $criteria, array $columns = ['*']): ?Model;

    /**
     * Create a new record in storage.
     *
     * @param  array<string, mixed>  $attributes
     * @return TModel
     */
    public function create(array $attributes): Model;

    /**
     * Update an existing record.
     *
     * @param  array<string, mixed>  $attributes
     * @return TModel
     */
    public function update(int|string $id, array $attributes): Model;

    /**
     * Delete a record by its primary key.
     */
    public function delete(int|string $id): bool;

    /**
     * Paginate records with dynamic filters and ordering.
     *
     * @param  array<string, mixed>  $filters
     * @param  array<int, string>  $columns
     * @return LengthAwarePaginator<int, TModel>
     */
    public function paginate(
        int $perPage = 15,
        array $filters = [],
        array $columns = ['*'],
        string $orderBy = 'id',
        string $direction = 'desc'
    ): LengthAwarePaginator;
}
