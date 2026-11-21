<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class BaseEloquentRepository
 *
 * Abstract base repository implementing common CRUD, filtering, sorting,
 * and pagination logic across all domain entities.
 *
 * @template TModel of Model
 *
 * @implements RepositoryInterface<TModel>
 */
abstract class BaseEloquentRepository implements RepositoryInterface
{
    /**
     * The model instance.
     *
     * @var TModel
     */
    protected Model $model;

    /**
     * BaseEloquentRepository constructor.
     *
     * @param  TModel  $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get a fresh query builder for the model.
     *
     * @return Builder<TModel>
     */
    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * {@inheritDoc}
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->query()->get($columns);
    }

    /**
     * {@inheritDoc}
     */
    public function find(int|string $id, array $columns = ['*']): ?Model
    {
        return $this->query()->find($id, $columns);
    }

    /**
     * {@inheritDoc}
     */
    public function findOrFail(int|string $id, array $columns = ['*']): Model
    {
        $result = $this->find($id, $columns);

        if ($result === null) {
            throw (new ModelNotFoundException)->setModel(get_class($this->model), [$id]);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria, array $columns = ['*']): ?Model
    {
        return $this->query()->where($criteria)->first($columns);
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $attributes): Model
    {
        return $this->query()->create($attributes);
    }

    /**
     * {@inheritDoc}
     */
    public function update(int|string $id, array $attributes): Model
    {
        $record = $this->findOrFail($id);
        $record->fill($attributes)->save();

        return $record->fresh();
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int|string $id): bool
    {
        $record = $this->findOrFail($id);

        return (bool) $record->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(
        int $perPage = 15,
        array $filters = [],
        array $columns = ['*'],
        string $orderBy = 'id',
        string $direction = 'desc'
    ): LengthAwarePaginator {
        $query = $this->query();

        $this->applyFilters($query, $filters);

        $sortDir = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($orderBy, $sortDir)->paginate($perPage, $columns);
    }

    /**
     * Hook to apply custom dynamic filters to the query.
     * Subclasses can override to add specific search/scope rules.
     *
     * @param  Builder<TModel>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<TModel>
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query;
    }
}
